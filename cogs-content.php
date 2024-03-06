<?php
include 'db.php';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare an insert statement with an additional column for sales
    $stmt = $conn->prepare("INSERT INTO invoices (supplier, invoice_date, invoice_number, invoice_total, sales) VALUES (?, ?, ?, ?, ?)");
    
    // Assuming $sales is the value you want to insert into the sales column, calculate it here
    // For simplicity, we're adding a fixed value (1000) to every invoice's sales column as per your requirement
    $sales = 1000; // This is where you adjust based on your logic for calculating sales
    
    // Bind parameters (sssd indicates the types of the bound variables: string, string, string, double, double)
    $stmt->bind_param("sssdd", $supplier, $invoice_date, $invoice_number, $invoice_total, $sales);
    
    // Set parameters and execute
    $supplier = $_POST['supplier'];
    $invoice_date = $_POST['invoice_date'];
    $invoice_number = $_POST['invoice_number'];
    $invoice_total = $_POST['invoice_total'];
    
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    
    // Redirect to a confirmation page or back to the form
    header("Location: index.php");
    exit();
}

// Fetching suppliers and their total invoice amount
$suppliersSql = "SELECT supplier, SUM(invoice_total) AS total_invoice FROM invoices GROUP BY supplier";
$suppliersResult = $conn->query($suppliersSql);

$invoicesDetails = [];

if ($suppliersResult->num_rows > 0) {
    while ($row = $suppliersResult->fetch_assoc()) {
        $supplier = $row['supplier'];
        // Use prepared statements for fetching detailed invoice data for each supplier
        $stmt = $conn->prepare("SELECT invoice_date, invoice_number, invoice_total FROM invoices WHERE supplier = ?");
        $stmt->bind_param("s", $supplier);
        $stmt->execute();
        $detailsResult = $stmt->get_result();

        if ($detailsResult->num_rows > 0) {
            $invoicesDetails[$supplier] = $detailsResult->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
} else {
    echo "0 results";
}

// Always close the connection when you're done with it
$conn->close();
?>

<html>

<head>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2/dist/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>



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
    // Assuming .add and .add1 are classes for your "+" icons
    $(document).on('click', '.add', function() {
        $('#popup-form').modal('show');
    });

    $(document).on('click', '.add1', function() {
        $('#popup-form1').modal('show');
    });
});
  document.querySelectorAll('.month-btn').forEach(monthTab => {
    monthTab.addEventListener('click', function() {
        const selectedMonth = this.getAttribute('data-month');
        document.querySelectorAll('.month-btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('table-container').innerHTML = ''; // Clear existing table content
        document.getElementById('loader-container').style.display = 'flex'; // Show loader

        fetch('fetch_cogs.php?month=' + selectedMonth)
            .then(response => response.text())
            .then(data => {
                document.getElementById('loader-container').style.display = 'none'; // Hide loader
                document.getElementById('table-container').innerHTML = data;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('loader-container').style.display = 'none'; // Ensure loader is hidden on error
            });
    });
});

</script>

<script>
    $(document).ready(function() {
      $('#supplierSelect').select2({
        placeholder: "Select a supplier",
        allowClear: true
    });
    });
</script>
</head>
</head>
<main role="main ">
  <div class="dashboard-top-bar">
    <button id="mobile-menu-button" class="btn d-lg-none d-md-none">
      <i class="fa fa-bars"></i>
    </button>
    <div class="dashboard-title">
      <h4><b>COGS</b></h4>
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
  <div class="table-responsive" id="table-container">
        <?php
        // Include the initial table content for February
        include 'fetch_cogs.php';
        ?>
    </div>
    <div id="loader-container" class="loader-container" style="display: none;">
    <img src="includes/images/loader.gif" alt="Loading..." />
    </div>


    <button class="btn add" style="font-size: 1rem;"><i class="fa-solid fa-plus"></i> Add Invoice </button>
    <br><br>
    <button class="supplier-btn add1">Add Supplier</button>
    <div class="modal" id="popup-form1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="add_supplier.php" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="supplierName">Supplier Name:<span style="color:red">*</span></label>
            <input type="text" class="form-control inv-form" id="supplierName" name="supplierName" required>
          </div>
          <div class="form-group">
            <label for="abbreviation">Abbreviation:<span style="color:red">*</span></label>
            <input type="text" class="form-control inv-form" id="abbreviation" name="abbreviation" required>
          </div>
          <div class="form-group">
            <label for="websitelink">Website Link:<span>(Optional)</span></label>
            <input type="text" class="form-control inv-form" id="websitelink" name="websitelink">
          </div>
          <div class="form-group">
            <label for="catalogpath">Catalog Path:<span>(Optional)</span></label>
            <input type="text" class="form-control inv-form" id="catalogpath" name="catalogpath">
          </div>
          <div class="form-group">
            <label for="cataloglink">Catalog Link:<span>(Optional)</span></label>
            <input type="text" class="form-control inv-form" id="cataloglink" name="cataloglink">
          </div>
          <div class="form-group">
            <label for="contactname">Contact Name:<span style="color:red">*</span></label>
            <input type="text" class="form-control inv-form" id="contactname" name="contactname" required>
          </div>
          <div class="form-group">
            <label for="contactemail">Contact Email:<span style="color:red">*</span></label>
            <input type="email" class="form-control inv-form" id="contactemail" name="contactemail" required>
          </div>
          <div class="form-group">
            <label for="orderemail">Order Email:<span>(Optional)</span></label>
            <input type="email" class="form-control inv-form" id="orderemail" name="orderemail" >
          </div>
          <div class="form-group">
            <label for="address">Address:<span>(Optional)</span></label>
            <textarea class="form-control inv-form" id="address" name="address" ></textarea>
          </div>
          <div class="form-group">
            <label for="notes">Notes:<span>(Optional)</span></label>
            <textarea class="form-control inv-form" id="notes" name="notes"></textarea>
          </div>
          <div class="form-group">
            <label for="status">Status:<span>(Optional)</span></label>
            <select class="form-control inv-form" id="status" name="status">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary" style="border-radius: 20px; color:white ; background-color: black;border-color: black;">Add Supplier</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px; color:black ; background-color: #dee2e6;border-color: #dee2e6;font-weight: 600;">Close</button>
 
        </div>
      </form>
    </div>
  </div>
</div>

    <div id="popup-form" class="modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Invoice</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <form action="cogs-content.php" method="POST">
            <div class="form-group">
                <label for="supplierSelect">Supplier:<span style="color:red">*</span></label><br>
                <select class="form-control inv-form" id="supplierSelect" name="supplier" required>
    <?php
   include 'db.php';

    $supplierQuery = "SELECT * FROM suppliers";
    $supplierResult = $conn->query($supplierQuery);

    if ($supplierResult->num_rows > 0) {
        while ($row = $supplierResult->fetch_assoc()) {
            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
        }
    } else {
        echo '<option value="" disabled>No suppliers available</option>';
    }

    $conn->close();
    ?>
</select>
            </div>
              <div class="form-group">
                <label for="invoiceDate">Invoice Date:<span style="color:red">*</span></label>
                <input type="date" class="form-control inv-form" id="invoiceDate" name="invoice_date" required>
              </div>
              <div class="form-group">
                <label for="invoice">Invoice:<span style="color:red">*</span></label>
                <div class="input-group" style="margin:0">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="padding-left: 9px; padding-right: 9px; border-top-left-radius: 15px!important; border-bottom-left-radius: 15px!important;">#</span>
                  </div>
                  <input type="text" class="form-control inv-form1" id="invoice" name="invoice_number"  title="Invoice number must be exactly 5 digits long." required>
                </div>
              </div>

              <div class="form-group">
                <label for="invoiceTotal">Invoice Total:<span style="color:red">*</span></label>
                <div class="input-group" style="margin:0">
                  <div class="input-group-prepend">
                    <span class="input-group-text inv-form2">$</span>
                  </div>
                  <input type="number" class="form-control inv-form1" id="invoiceTotal" name="invoice_total"  required>
                </div>
              </div>

              <button type="submit" class="btn btn-primary" style="border-radius: 20px; color:white ; background-color: black;border-color: black;">Submit</button>

              <button type="button" class="btn btn-secondary" style="border-radius: 20px; color:black ; background-color: #dee2e6;border-color: #dee2e6;font-weight: 600;" data-dismiss="modal">Close</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
  <br><br><br>
</main>

</body>

</html>