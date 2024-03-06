<?php
include 'db.php';

// Extract month and year from the request parameters
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// SQL to fetch data from labor table for the selected month and year
$sql = "SELECT Id, price, date FROM labor WHERE MONTH(date) = $selectedMonth AND YEAR(date) = $selectedYear ORDER BY date DESC";

$result = $conn->query($sql);

$amount = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $amount[] = $row;
    }
}

$conn->close();

// Generate HTML for the table rows
if (empty($amount)) {
    echo "<tr><td colspan='2'>No Data available for the selected month</td></tr>";
} else {
    foreach ($amount as $labor) {
        // Convert the date to the desired format: "M d, Y"
        $formattedDate = date('M j, Y', strtotime($labor['date']));
        
        echo "<tr data-month='" . date('n', strtotime($labor['date'])) . "' data-year='" . date('Y', strtotime($labor['date'])) . "'>";
        echo "<td id='c1'>" . $formattedDate . "</td>";
        echo "<td class='edit-price' data-id='" . $labor['Id'] . "' contenteditable='true'>$" . number_format($labor['price'], 2) . "</td>";
        echo "</tr>";
    }
    
}
?>
