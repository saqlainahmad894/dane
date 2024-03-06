<?php
// Assume we're inside invoice.php

// Retrieve the selected date from the GET parameter
if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];
    // Format the date for display
    $displayDate = date("M d, Y", strtotime($selectedDate));
} else {
    // Default or error handling for no date provided
    $displayDate = "No date selected";
    $selectedDate = null;
}

include 'db.php';

// Initialize transactions as an array
$transactions = [];

// Proceed if we have a selected date
if ($selectedDate) {
    // SQL query to fetch supplier transactions for the selected date
    $sql = "SELECT supplier, invoice_total FROM invoices WHERE invoice_date = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $selectedDate);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $transactions[] = $row;
            }
        } else {
            $transactions[] = ['supplier' => 'No transactions', 'invoice_total' => 'N/A'];
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  </head>
  <body>
    <main role="main ">
      <div class="dashboard-top-bar">
        <button id="mobile-menu-button" class="btn d-lg-none d-md-none">
        <i class="fa fa-bars"></i>
        </button>
        <div class="dashboard-title">
          <h4><b><?php echo"$displayDate" ?></b></h4>
        </div>
      </div>
      </div>
      <div class="table-responsive">
        
      </div>
      <div class="table-responsive">
        <table class="table">
          <tbody>
            <tr>
              <td id="p2" style="width: 20%;">Sales</td>
              <td id="c3" style="width: 20%;">$1,001</td>
              <td id="c3" style="width: 20%;"></td>
              <td id="c3"></td>
              <td id="c3"></td>
              <td id="c3"></td>
              <td>
              </td>
            </tr>
            <tr>
              <td id="p2" style="width: 20%;">Labour Cost</td>
              <td id="c3" style="width: 20%;">$1,001</td>
              <td id="c3" style="width: 20%;"></td>
              <td id="c3"></td>
              <td id="c3"></td>
              <td id="c3"></td>
              <td>
              </td>
            </tr>
            <tr>
              <td id="p2" style="width: 20%;"></td>
              <td id="c4" style="width: 20%;"></td>
              <td id="c4" style="width: 20%;"></td>
              <td id="c4"></td>
              <td id="c4"></td>
              <td id="c4"></td>
              <td>
              </td>
            </tr>
            <tr>
              <td style="width: 20%;"></td>
              <td id="p2" style="width: 20%;">Amount</td>
              <td id="p2" style="width: 20%;">Supplier</td>
              <td id="c3"></td>
              <td id="c3"></td>
              <td id="c3"></td>
              <td id="c3"><a href="#" style="color:black" class="cell-icon"><b style="font-size:1.2rem">:</b></a>
              </td>
            </tr>
            <?php $firstInvoice = true; ?>
            <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <?php if ($firstInvoice): ?>
                                <td id="p2" style="width: 20%;">Invoices</td>
                                <?php $firstInvoice = false; ?>
                            <?php else: ?>
                                <td style="width: 20%;"></td>
                            <?php endif; ?>
                            <td style="width: 20%;"  id="c3">$<?php echo htmlspecialchars($transaction['invoice_total']); ?></td>
                            <td style="width: 20%;"  id="c3"><?php echo htmlspecialchars($transaction['supplier']); ?></td>
                            <td id="c3"></td>
                            <td id="c3"></td>
                            <td id="c3"></td>
                            <td id="c3"><a href="#" style="color:black" class="cell-icon"><b style="font-size:1.2rem">:</b></a></td>
                        </tr>
                    <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3"></script>
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
            if (contentToLoad === "daily-profit" || contentToLoad === "sales" || contentToLoad === "cogs" || contentToLoad === "fixed" || contentToLoad === "others") {
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
      initializeBarChart();
    </script>
  </body>
</html>