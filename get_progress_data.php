<?php
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('n');

function daysInMonth($month, $year)
{
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

function isLeapYear($year)
{
    return ($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0));
}

include 'db.php';

$distinctMonths = [];
$sqlDistinctMonths = "SELECT DISTINCT MONTH(invoice_date) AS month FROM invoices";
$resultDistinctMonths = $conn->query($sqlDistinctMonths);

if ($resultDistinctMonths->num_rows > 0) {
    while ($row = $resultDistinctMonths->fetch_assoc()) {
        $distinctMonths[] = $row['month'];
    }
}

$data = [];
$totalInvoiceTillDate = 0;
$totalDailyProfit = 0;

$totalDailyFixedExpense = 0; // Initialize variable
$otherAmounts = []; // Initialize variable
$laborPrices = []; // Initialize variable

if (in_array($selectedMonth, $distinctMonths)) {

    $sql = "SELECT invoice_date, SUM(invoice_total) AS total_invoice, SUM(fixed_field) AS total_fixed, sales AS sales FROM invoices WHERE MONTH(invoice_date) = $selectedMonth GROUP BY invoice_date";

    $result = $conn->query($sql);

    $monthlyProfit = 0;

    if ($result->num_rows > 0) {
        $rowCount = 0; // Initialize row count
        while ($row = $result->fetch_assoc()) {
            $rowCount++;
            $totalInvoiceTillDate += $row['total_invoice'];
            $row['total_invoice_till_date'] = $totalInvoiceTillDate;
            $dailyCogs = $totalInvoiceTillDate / $rowCount; // Calculate daily COGS based on row count

            $data[] = $row;
        }
    }
}

$selectedYear = date('Y');

$daysInSelectedMonth = daysInMonth($selectedMonth, $selectedYear);

$daysInYear = isLeapYear($selectedYear) ? 366 : 365;
$daysPassed = date('j');
$sqlFixed = "SELECT Id, name, duration, enddate, amount FROM fixed";
$resultFixed = $conn->query($sqlFixed);

$fixedExpensesDaily = [];

while ($fixed = $resultFixed->fetch_assoc()) {
    $dailyAmount = 0;
    switch ($fixed['duration']) {
        case 'Daily':
            $dailyAmount = $fixed['amount'];
            break;
        case 'Monthly':
            $dailyAmount = $fixed['amount'] / $daysInSelectedMonth;
            break;
        case 'Yearly':
            $dailyAmount = $fixed['amount'] / $daysInYear;
            break;
    }

    if (!empty($fixed['enddate']) && new DateTime($fixed['enddate']) < new DateTime("$selectedYear-$selectedMonth-01")) {

        $dailyAmount = 0;
    }

    $fixedExpensesDaily[] = $dailyAmount;
}
$totalDailyFixedExpense = array_sum($fixedExpensesDaily);

$sqlOther = "SELECT date, amount FROM other WHERE MONTH(date) = $selectedMonth";
$resultOther = $conn->query($sqlOther);
if ($resultOther->num_rows > 0) {
    while ($row = $resultOther->fetch_assoc()) {
        $otherAmounts[$row['date']] = $row['amount'];
    }
}

$sqlLabor = "SELECT date, price FROM labor WHERE MONTH(date) = $selectedMonth";
$resultLabor = $conn->query($sqlLabor);
if ($resultLabor->num_rows > 0) {
    while ($row = $resultLabor->fetch_assoc()) {
        $laborPrices[$row['date']] = $row['price'];
    }
}

function calculateDailyProfit($invoiceDate, $dailyCogs, $totalFixed, $otherAmounts, $laborPrice, $conn)
{
    // Fetch sales data for the given date from the invoices table
    $sqlSales = "SELECT sales FROM invoices WHERE invoice_date = '$invoiceDate'";
    $resultSales = $conn->query($sqlSales);
    
    // Initialize sales variable
    $sales = 0;

    if ($resultSales->num_rows > 0) {
        $row = $resultSales->fetch_assoc();
        $sales = $row['sales'];
    }

    $otherAmount = isset($otherAmounts[$invoiceDate]) ? $otherAmounts[$invoiceDate] : 0;
    $finalDailyProfit = $sales - $dailyCogs - $otherAmount - $totalFixed - $laborPrice;
    return $finalDailyProfit;
}

$totalMonthlyProfit = 0;
$monthlyProfits = [];
$firstIndex = 0;
$lastIndex = count($data) - 1;

$totalMonthlyProfit = 0;
$monthlyProfits = [];

foreach ($data as $index => $row) {
    $date = $row['invoice_date'];
    $dailyCogs = $totalInvoiceTillDate / count($data); // Recalculate daily COGS
    
    // Fetch labor price for the current date
    $laborPrice = isset($laborPrices[$date]) ? $laborPrices[$date] : 0;

    // Calculate daily profit
    $dailyProfit = calculateDailyProfit($date, $dailyCogs, $totalDailyFixedExpense, $otherAmounts, $laborPrice, $conn);

    // Update monthly profit
    $totalMonthlyProfit += $dailyProfit;

    // Set monthly profit for the current date
    $monthlyProfits[$date] = $totalMonthlyProfit;
}
$totalMonthlyFixedExpense = array_sum($fixedExpensesDaily);

// Calculate total values for labor, invoices, fixed expenses, and other expenses
$totalLabor = array_sum($laborPrices);
$totalInvoices = $totalInvoiceTillDate;
$totalFixedExpenses = $totalMonthlyFixedExpense; // Use monthly fixed expenses instead of daily
$totalOtherExpenses = array_sum($otherAmounts);
$lastIndexMonthlyProfit = end($monthlyProfits);
$netProfit = $lastIndexMonthlyProfit;

// Calculate the total of all values
$totalAll = $totalLabor + $totalInvoices + $totalFixedExpenses + $totalOtherExpenses + $lastIndexMonthlyProfit;

// Calculate percentages
$cogsPercentage = ($totalInvoices / $totalAll) * 100;
$laborPercentage = ($totalLabor / $totalAll) * 100;
$fixedPercentage = ($totalFixedExpenses / $totalAll) * 100;
$otherPercentage = ($totalOtherExpenses / $totalAll) * 100;
$netPercentage = ($netProfit / $totalAll) * 100;

// Prepare the data to return
$dataToReturn = [
    'progressBarData' => [
        'cogsPercentage' => $cogsPercentage,
        'laborPercentage' => $laborPercentage,
        'fixedPercentage' => $fixedPercentage,
        'otherPercentage' => $otherPercentage,
        'netPercentage' => $netPercentage
    ]
];

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($dataToReturn);
exit(); // Stop execution after echoing JSON data
?>
