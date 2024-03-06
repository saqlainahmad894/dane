<!--<?php
include 'db.php';

// Fetch invoice dates
$sql = "SELECT DISTINCT invoice_date FROM invoices ORDER BY invoice_date ASC";
$result = $conn->query($sql);

$invoiceDates = [];
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $invoiceDates[] = $row["invoice_date"];
  }
}

$conn->close();

// Pass the dates to JavaScript
echo "<script>var invoiceDates = " . json_encode($invoiceDates) . ";</script>";
?>-->


<aside>
<div id="loader-container" class="loader-container" style="display: none;">
    <img src="includes/images/loader.gif" alt="Loading..." />
    </div>
   <div class="right-sidebar-widget">
      <!-- <h4><b>Invoices</b></h4>
      <div class="calendar">
       <ul class="list-unstyled" id="calendar-list">
       </ul>
     </div> -->
     <h5><b>ADD</b></h5>
     <div class="aside-button" data-target-page="cogs-content.php">
      <img src="includes/images/contract.png" alt="Contract Icon" class="icon">
      <span>Invoices</span>
    </div>
    
    <!-- Fixed Expenses aside button -->
    <div class="aside-button" data-target-page="fixed-content.php">
      <img src="includes/images/car-rental.png" alt="Car Icon" class="icon">
      <span>Fixed</span>
    </div>
     <div class="aside-button" data-target-page="others-content.php">
      <img src="includes/images/keys.png" alt="Other Icon" class="icon">
      <span>Other</span>
    </div>
    <!--<div class="aside-button" data-target-page="labour-content.php">
      <img src="includes/images/keys.png" alt="Other Icon" class="icon">
      <span>Other</span>
    </div>-->
   </div>
   <!--<div class="exam-info">
     <h4 class="exam-title">World Economy Exam</h4>
     <p class="exam-date">Monday, 15 May 2023</p>
     <p class="exam-time">11 AM, Online</p>
   </div>-->
 </aside>
 <!--<script>
function generateCalendar() {
    const daysOfWeek = ["S", "M", "T", "W", "Th", "F", "Sa"];
    const calendarList = document.getElementById('calendar-list');

    // Sort invoiceDates in descending order
    invoiceDates.sort(function(a, b) {
        return new Date(b) - new Date(a);
    });

    invoiceDates.forEach(function(invoiceDate) {
        const date = new Date(invoiceDate);
        const dayOfWeek = daysOfWeek[date.getDay()];
        const formattedDate = date.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric"
        });
        const listItem = document.createElement("li");
        listItem.innerHTML = `${dayOfWeek} <span>${formattedDate}</span>`;
        listItem.className = 'calendar-date';
        listItem.onclick = function() {
            loadContent(`invoice.php?date=${invoiceDate}`, "#main-content-placeholder");
            $('.nav-link').removeClass('active');
            $('.nav-link[data-content="daily-profit"]').addClass('active');
        };
        calendarList.appendChild(listItem);
    });
}

$(document).ready(function() {
    generateCalendar();
});

function loadContent(url, placeholderSelector) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                $(placeholderSelector).html(xhr.responseText);
            } else {
                $(placeholderSelector).html('<p>Error loading content.</p>');
            }
        }
    };
    xhr.open("GET", url, true);
    xhr.send();
}
</script>-->