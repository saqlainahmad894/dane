<?php
include 'db.php';

// SQL to fetch data from fixed table
$sql = "SELECT Id, name, duration, enddate, amount FROM fixed";
$result = $conn->query($sql);

// Calculate total amount for the percentage calculation
$totalAmount = 0;
$fixedExpenses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fixedExpenses[] = $row;
        $totalAmount += $row['amount'];
    }
} else {
    echo "<tr><td colspan='6'>No fixed expenses found</td></tr>";
}

$conn->close();
?>

<html>
  <head>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap JS (make sure it's loaded after jQuery) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
  </head>
  <main role="main ">
    <div class="dashboard-top-bar">
      <button id="mobile-menu-button" class="btn d-lg-none d-md-none">
      <i class="fa fa-bars"></i>
      </button>
      <div class="dashboard-title">
        <h4><b>Fixed</b></h4>
      </div>
      <div class="dashboard-actions">
        <div class="dashboard-search">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1">
              <i class="fa fa-search"></i>
              </span>
            </div>
            <input id="search1" type="search" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1">
          </div>
          <button class="btn">
          <i class="fa-regular fa-bell"></i>
          </button>
          <button class="btn">
          <i class="fa-regular fa-envelope"></i>
          </button>
          <button class="btn">
          <i class="fa-regular fa-message"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <div class="month-navigation-container">
        <hr class="month-navigation-line">
        <div class="month-navigation py-2">
    <?php
    $now = new DateTime(); // Current date and time
    for ($i = 0; $i < 5; $i++) {
        // Clone the DateTime object to avoid modifying the original $now
        $month = clone $now;
        // Subtract months from the current date
        $month->sub(new DateInterval('P' . $i . 'M'));
        // Format the month for display and for data-month attribute
        $display = $month->format('F'); // Full month name
        $monthValue = $month->format('n'); // Month number without leading zeros
        $yearValue = $month->format('Y'); // Year
        $activeClass = $i === 0 ? 'active' : ''; // Mark the current month as active

        echo "<button class='month-btn rounded-pill $activeClass' data-month='$monthValue' data-year='$yearValue'>$display</button>";
    }
    ?>
</div>

      </div>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr class="table-header">
            <th>Item</th>
            <th>Amount</th>
            <th>Amount %</th>
            <th>Recurring</th>
            <th>End Date</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($fixedExpenses as $expense): ?>
    <?php 
    $formattedEndDate = ''; // Start with an empty string for end date
    if (!empty($expense['enddate'])) { // Check if there's an end date
        $date = new DateTime($expense['enddate']);
        $formattedEndDate = $date->format('F j, Y'); // Format it
    }
    // Output the table row with other expense details
    ?>
    <tr>
      <td id="c1"><?php echo htmlspecialchars($expense['name']); ?></td>
      <td>$<?php echo number_format($expense['amount'], 2); ?></td>
      <td><?php echo $totalAmount > 0 ? round(($expense['amount'] / $totalAmount) * 100, 2) . '%' : 'N/A'; ?></td>
      <td><?php echo htmlspecialchars($expense['duration']); ?></td>
      <td><?php echo $formattedEndDate; ?></td>
    </tr>
<?php endforeach; ?>

</tbody>

      </table>
      <h3>
        <b>Total $<?php echo number_format($totalAmount, 2); ?></b>
      </h3>
      <h5>
    <b>Average Daily: $<span id="average-daily">0.00</span></b>
</h5>

    </div>
    <br><br>
    <button class="fixed-expense-btn">ADD FIXED EXPENSE</button>
    <div class="modal" id="addFixedExpenseModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Fixed Expense</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="add_fixed_expense.php" method="POST">
        <div class="modal-body">
          <!-- Expense Name -->
          <div class="form-group">
            <label for="name">Expense Name:<span style="color:red">*</span></label>
            <input type="text" class="form-control inv-form" id="name" name="name" required>
          </div>
          <div class="form-group">
                <label for="amount">Amount:<span style="color:red">*</span></label>
                <div class="input-group" style="margin:0">
                  <div class="input-group-prepend">
                    <span class="input-group-text inv-form2">$</span>
                  </div>
                  <input type="text" class="form-control inv-form1" id="invoiceTotal" name="amount" pattern="\d+(\.\d{1})?" title="Please enter a valid amount." required>
                </div>
              </div>
          <!-- Duration Dropdown -->
          <div class="form-group">
            <label for="duration">Duration:<span style="color:red">*</span></label>
            <select class="form-control inv-form" id="duration" name="duration">
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
              <option value="Yearly">Yearly</option>
            </select>
          </div>
          
          <!-- Optional End Date -->
          <div class="form-group">
  <label for="endDate">End Date (optional):</label>
  <input type="date" class="form-control inv-form" id="endDate" name="endDate">
</div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" style="border-radius: 20px; color:white ; background-color: black;border-color: black;">Add Expense</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px; color:black ; background-color: #dee2e6;border-color: #dee2e6;font-weight: 600;">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

  </main>
  <script>
    function changeMonth(button) {
       const monthButtons = document.querySelectorAll('.month-btn');
       monthButtons.forEach((btn) => {
          btn.classList.remove('active');
       });
       button.classList.add('active');
    }
  </script>
  <script>
    $(document).ready(function() {
       $('.accordion-toggle').click(function(e) {
          e.preventDefault();
          e.stopPropagation();
          var chevronIcon = $(this).find('.fas');
          chevronIcon.toggleClass('rotate-icon');
          var targetId = $(this).attr('data-target');
          $(targetId).collapse('toggle');
       });
    });
  </script> 
  <script>
    var $sidebar = $(".sidebar");
       var $mobileMenuButton = $("#mobile-menu-button");
       var $sidebarBackdrop = $("#sidebar-backdrop");
       $mobileMenuButton.click(function() {
          $sidebar.toggleClass("open");
          $(this).toggleClass("active");
          $sidebarBackdrop.toggle();
       });
       $(".nav-link").click(function(e) {
          e.preventDefault();
          var contentToLoad = $(this).data("content");
          $(".nav-link").removeClass('active');
          $(this).addClass('active');
          if (contentToLoad === "daily-profit" || contentToLoad === "sales" || contentToLoad === "cogs" || contentToLoad === "labor" || contentToLoad === "fixed" || contentToLoad === "others") {
             $sidebar.removeClass("open");
             $mobileMenuButton.removeClass("active");
             $sidebarBackdrop.hide();
          }
       });
       $sidebarBackdrop.on("swipeleft", function() {
          if ($sidebar.hasClass("open")) {
             $sidebar.removeClass("open");
             $mobileMenuButton.removeClass("active");
             $(this).hide();
          }
       });
          
  </script>
<script>
$(document).ready(function() {
    // Function to calculate and update the average daily expense
    function updateAverageDailyExpense(selectedMonth) {
        var year = new Date().getFullYear(); // Current year
        var daysInMonth = new Date(year, selectedMonth, 0).getDate(); // Days in selected month

        // Check for leap year if February is selected
        var isLeap = selectedMonth === 2 && ((year % 4 === 0 && year % 100 !== 0) || year % 400 === 0);
        daysInMonth = isLeap && daysInMonth === 28 ? 29 : daysInMonth;

        // Initialize total amounts
        var totalDailyAmount = 0;
        var totalMonthlyAmount = 0;
        var totalYearlyAmount = 0;

        // Loop through fixed expenses to calculate total amounts for each duration
        <?php foreach ($fixedExpenses as $expense): ?>
            <?php if ($expense['duration'] == 'Daily'): ?>
                totalDailyAmount += <?php echo $expense['amount']; ?>;
            <?php elseif ($expense['duration'] == 'Monthly'): ?>
                totalMonthlyAmount += <?php echo $expense['amount']; ?>;
            <?php elseif ($expense['duration'] == 'Yearly'): ?>
                totalYearlyAmount += <?php echo $expense['amount']; ?>;
            <?php endif; ?>
        <?php endforeach; ?>

        // Calculate average daily expense for each duration
        var averageDailyDaily = totalDailyAmount;
        var averageDailyMonthly = totalMonthlyAmount / daysInMonth;
        var averageDailyYearly = totalYearlyAmount / (isLeap ? 366 : 365); // Adjust for leap year

        // Calculate total average daily expense
        var totalAverageDaily = averageDailyDaily + averageDailyMonthly + averageDailyYearly;

        // Update the Average Daily text
        $('#average-daily').text(totalAverageDaily.toFixed(2));
    }

    // Initial call for the default selected month
    var initialSelectedMonth = $('.month-btn.active').data('month');
    updateAverageDailyExpense(initialSelectedMonth);

    // Handle month button click
    $('.month-btn').click(function() {
        $('.month-btn').removeClass('active'); // Remove active class from all month buttons
        $(this).addClass('active'); // Add active class to the clicked month button

        var selectedMonth = $(this).data('month');
        updateAverageDailyExpense(selectedMonth); // Call the update function with the new month
    });

    $('.fixed-expense-btn').click(function() {
        $('#addFixedExpenseModal').modal('show');
    });
});

</script>

  </body>
</html>