<?php
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('n');

include 'db.php';

// Retrieve distinct months from the database
$distinctMonths = [];
$sqlDistinctMonths = "SELECT DISTINCT MONTH(invoice_date) AS month FROM invoices";
$resultDistinctMonths = $conn->query($sqlDistinctMonths);

if ($resultDistinctMonths->num_rows > 0) {
  while ($row = $resultDistinctMonths->fetch_assoc()) {
    $distinctMonths[] = $row['month'];
  }
}

$invoicesDetails = [];
// Check if the selected month is in the list of distinct months
if (in_array($selectedMonth, $distinctMonths)) {

  // Fetching suppliers and their total invoice amount for the selected month
  $suppliersSql = "SELECT supplier, SUM(invoice_total) AS total_invoice FROM invoices WHERE MONTH(invoice_date) = ? GROUP BY supplier";
  $stmt = $conn->prepare($suppliersSql);
  $stmt->bind_param("i", $selectedMonth);
  $stmt->execute();
  $suppliersResult = $stmt->get_result();

  if ($suppliersResult->num_rows > 0) {
    while ($row = $suppliersResult->fetch_assoc()) {
      $supplier = $row['supplier'];
      $invoicesDetails[$supplier] = $row; // Store the supplier data

      // Use prepared statements for fetching detailed invoice data for each supplier for the selected month
      $stmt = $conn->prepare("SELECT invoice_date, invoice_number, invoice_total FROM invoices WHERE supplier = ? AND MONTH(invoice_date) = ?");
      $stmt->bind_param("si", $supplier, $selectedMonth);
      $stmt->execute();
      $detailsResult = $stmt->get_result();

      if ($detailsResult->num_rows > 0) {
        $invoicesDetails[$supplier]['details'] = $detailsResult->fetch_all(MYSQLI_ASSOC);
      }
      $stmt->close();
    }
  } else {
    echo "0 results for the selected month";
  }
}

$conn->close();
?>
<html>
<head></head>

  <body>
    <table class="table">
      <thead>
        <tr class="table-header">
          <th></th>
          <th>Supplier</th>
          <th>Invoice Total</th>
          <th class="date-column">Date</th>
          <th class="date-column">Invoice</th>
          <th class="date-column">Invoice Total</th>
          <th>% of Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Check if $invoicesDetails is empty and display a message if no data is available
        if (empty($invoicesDetails)) {
          echo '<tr><td colspan="8">No Data available for the selected month</td></tr>';
        } else {
          $supplierIndex = 0; // Initialize a supplier index
          foreach ($invoicesDetails as $supplier => $supplierData) : ?>
            <tr class="accordion-toggle collapsed" data-toggle="collapse" data-target=".details-<?php echo $supplierIndex; ?>">
              <td><i class="fas fa-chevron-right"></i></td>
              <td id="c1"><?php echo $supplier; ?></td>
              <td>$<?php echo $supplierData['total_invoice']; ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td><i class="fa-solid fa-plus"></i></td>
            </tr>
            <?php foreach ($supplierData['details'] as $detail) :
              // Parse the date from the database and format it
              $date = DateTime::createFromFormat('Y-m-d', $detail['invoice_date']);
              $formattedDate = $date->format('F j'); // e.g., "12 December"
            ?>
              <tr style class="collapse details-<?php echo $supplierIndex; ?>">
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $formattedDate; ?></td>
                <td><?php echo $detail['invoice_number']; ?></td>
                <td>$<?php echo $detail['invoice_total']; ?></td>
                <td></td>
                <td></td>
              </tr>
            <?php endforeach; ?>
            <?php $supplierIndex++; // Increment the supplier index for a unique class in the next iteration 
            ?>
          <?php endforeach; ?>
        <?php } ?>
      </tbody>
      <tfoot style="border-top: 2px solid #dee2e6 !important;">
  <tr>
    <td colspan="2" style="text-align: right;    font-weight: 700;
    font-size: 0.9em;">Total :</td>
    <?php
    $totalInvoices = array_sum(array_column($invoicesDetails, 'total_invoice')); // Calculate the total invoice amount
    ?>
    <td>$<?php echo number_format($totalInvoices, 2); ?></td>
  </tr>
</tfoot>
    </table>
  </body>
  <script>
  // JavaScript to handle month button clicks and load content dynamically
  document.querySelectorAll('.month-btn').forEach(monthTab => {
    monthTab.addEventListener('click', function() {
      const selectedMonth = this.getAttribute('data-month');

      // Remove the 'active' class from all month buttons
      document.querySelectorAll('.month-btn').forEach(btn => {
        btn.classList.remove('active');
      });

      // Add 'active' class to the clicked button
      this.classList.add('active');

      // Clear the table content
      document.getElementById('table-container').innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';

      // Load content dynamically using AJAX and send the selected month as a GET parameter
      fetch('fetch_cogs.php?month=' + selectedMonth) // Send the selected month as a GET parameter
        .then(response => response.text())
        .then(data => {
          document.getElementById('table-container').innerHTML = data; // Update the table content

          // Reattach event listeners for toggle buttons
          attachToggleEventListeners();
        })
        .catch(error => {
          console.error('Error fetching data:', error);
        });
    });
  });

  // Function to attach event listeners for toggle buttons
  function attachToggleEventListeners() {
    // Function to handle the click event on the ".accordion-toggle" elements
    function toggleAccordion(event) {
      event.preventDefault();
      event.stopPropagation();
      var chevronIcon = $(this).find('.fas');
      chevronIcon.toggleClass('rotate-icon');
      var targetId = $(this).attr('data-target');
      $(targetId).toggle();
    }

    // Use event delegation to handle the click event for dynamically added elements
    $(document).on('click', '.accordion-toggle', toggleAccordion);

    // Initially, handle the click event for existing elements
    $('.accordion-toggle').click(toggleAccordion);
  }

  // Initially attach event listeners for toggle buttons
  attachToggleEventListeners();
</script>


</html>