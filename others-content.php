<?php
include 'db.php';

// SQL to fetch data from other table
$sql = "SELECT Id, name, time, amount, date FROM other"; // Include the 'date' column in the SQL query
$result = $conn->query($sql);

// Calculate total amount for the percentage calculation
$totalAmount = 0;
$otherExpenses = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $otherExpenses[] = $row;
    $totalAmount += $row['amount'];
  }
} else {
  echo "<tr><td colspan='6'>No other expenses found</td></tr>";
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>

</head>
</head>
<main role="main ">
  <div class="dashboard-top-bar">
    <button id="mobile-menu-button" class="btn d-lg-none d-md-none">
      <i class="fa fa-bars"></i>
    </button>
    <div class="dashboard-title">
      <h4><b>Others</b></h4>
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
       <!--<div class="month-navigation py-2">
        <button class="month-btn rounded-pill active" data-month="1">January</button>
        <button class="month-btn rounded-pill" data-month="12">December</button>
        <button class="month-btn rounded-pill" data-month="11">November</button>
        <button class="month-btn rounded-pill" data-month="10">October</button>
        <button class="month-btn rounded-pill" data-month="09">September</button>
        </div>-->
    </div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr class="table-header">
          <th>Item</th>
          <th>Amount</th>
          <th>One time or Variable</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
    <?php foreach ($otherExpenses as $expense) : ?>
        <?php
        $formattedDate = date('F j, Y', strtotime($expense['date'])); // Format the date
        ?>
        <tr>
            <td><?php echo htmlspecialchars($expense['name']); ?></td>
            <td>$<?php echo number_format($expense['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($expense['time']); ?></td>
            <td><?php echo $formattedDate; ?></td> <!-- Add this line for the new Date column -->
        </tr>
    <?php endforeach; ?>
</tbody>
    </table>
    <h3>
      <b>Total $<?php echo number_format($totalAmount, 2); ?></b>
    </h3>
  </div>
  <br><br>
  <button class="fixed-expense-btn">ADD OTHER EXPENSE</button>
  <div class="modal" id="addOtherExpenseModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Other Expense</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="add_other.php" method="POST">
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
            <div class="form-group">
              <label for="date">Expense Date:<span style="color:red">*</span></label>
              <input type="date" class="form-control inv-form" id="date" name="date" required>
            </div>
            <!-- Duration Dropdown -->
            <div class="form-group">
              <label for="duration">One time or Variable:<span style="color:red">*</span></label>
              <select class="form-control inv-form" id="time" name="time" required>
                <option value="One time">One time</option>
                <option value="Variable">% of Sales</option>
              </select>
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
    // Handle month button click
    $('.month-btn').click(function() {
      $('.month-btn').removeClass('active'); // Remove active class from all month buttons
      $(this).addClass('active'); // Add active class to the clicked month button

      var selectedMonth = $(this).data('month');
      // Implement the logic to fetch and update the expenses table based on the selected month
    });


    $('.fixed-expense-btn').click(function() {
      $('#addOtherExpenseModal').modal('show');
    });
  });
  $(document).ready(function() {
    $('#time').select2({
      tags: true,
      tokenSeparators: [',', ' ']
    });
  });
</script>

</body>

</html>