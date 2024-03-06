<!DOCTYPE html>
<html>

<head>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <main role="main ">
    <div class="dashboard-top-bar">
      <button id="mobile-menu-button" class="btn d-lg-none d-md-none">
        <i class="fa fa-bars"></i>
      </button>
      <div class="dashboard-title">
        <h4>
          <b>Daily Profit</b>
        </h4>
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
      include 'fetch_data.php';
      ?>
    </div>
    <div id="loader-container" class="loader-container" style="display: none;">
      <img src="includes/images/loader.gif" alt="Loading..." />
    </div>


    <div class="row mt-4">
      <div class="col-md-6">
        <div class="graph-box">
          <canvas id="barChart" style="box-sizing: border-box;display: block;height: 167px!important;width: 333px!important;">
          </canvas>
        </div>
      </div>
      <div class="col-md-6">
        <div class="graph-box progress-container">
        <div class="progress-box" id="cogs-progress-box" title="Total COGS: $<?php echo round($totalInvoices); ?>">
            <div class="progress-label">COGS</div>
            <div class="progress-bar-container">
              <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo round($cogsPercentage); ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar-fill" style="width: <?php echo round($cogsPercentage); ?>%;"></div>
              </div>
              <div class="percentage"><?php echo round($cogsPercentage); ?>% </div>
            </div>
          </div>
          <div class="progress-box" id="labor-progress-box" title="Total Labor: $<?php echo round($totalLabor); ?>">
            <div class="progress-label">Labor</div>
            <div class="progress-bar-container">
              <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo round($laborPercentage); ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar-fill" style="width: <?php echo round($laborPercentage); ?>%;"></div>
              </div>
              <div class="percentage"><?php echo round($laborPercentage); ?>% </div>
            </div>
          </div>
          <div class="progress-box" id="fixed-progress-box" title="Total Fixed: $<?php echo round($totalFixedExpenses); ?>">
            <div class="progress-label">Fixed</div>
            <div class="progress-bar-container">
              <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo round($fixedPercentage); ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar-fill" style="width: <?php echo round($fixedPercentage); ?>%;"></div>
              </div>
              <div class="percentage"><?php echo round($fixedPercentage); ?>% </div>
            </div>
          </div>

          <div class="progress-box" id="other-progress-box" title="Total Other: $<?php echo round($totalOtherExpenses); ?>">
            <div class="progress-label">Other</div>
            <div class="progress-bar-container">
              <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo round($otherPercentage); ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar-fill" style="width: <?php echo round($otherPercentage); ?>%;"></div>
              </div>
              <div class="percentage"><?php echo round($otherPercentage); ?>% </div>
            </div>
          </div>
          <div class="progress-box" id="net-progress-box" title="Total Net: $<?php echo round($lastIndexMonthlyProfit); ?>">
            <div class="progress-label">Net</div>
            <div class="progress-bar-container">
              <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo round($netPercentage); ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar-fill" style="width: <?php echo round($netPercentage); ?>%;"></div>
              </div>
              <div class="percentage"><?php echo round($netPercentage); ?>% </div>
            </div>
          </div>


        </div>
      </div>
    </div>
    <br>
    <br>
  </main>
  <script>
    var globalBarChart; // Declare a global variable outside of the function

    function initializeBarChart() {
      function randomData() {
        return Math.floor(Math.random() * 100) + 1;
      }
      const staticData = [50, 40, 30, 60, 80];
      const yLabels = [0, 20, 40, 60, 80, 100];

      function createChart() {
        var ctxBar = document.getElementById('barChart');

        // Destroy any existing Chart instance
        if (globalBarChart) {
          globalBarChart.destroy();
        }

        // Create a new chart instance
        globalBarChart = new Chart(ctxBar, {
          type: 'bar',
          data: {
            labels: ['September', 'October', 'November', 'December', 'January'],
            datasets: [{
              data: staticData,
              backgroundColor: ['rgba(165, 217, 116, 1)', 'rgba(225, 225, 225, 1)', 'rgba(165, 217, 116, 1)', 'rgba(255, 117, 149, 1)', 'rgba(232, 173, 239, 1)'],
              borderColor: 'transparent',
              borderWidth: 0,
              barThickness: 15,
              borderRadius: {
                topLeft: 10,
                topRight: 10,
                bottomLeft: 10,
                bottomRight: 10
              }
            }]
          },
          options: {
            scales: {
              x: {
                display: true,
                grid: {
                  display: false
                },
                ticks: {
                  display: false
                }
              },
              y: {
                beginAtZero: true,
                max: 120,
                grid: {
                  display: true,
                  drawBorder: false,
                  color: function(context) {
                    if (context.tick.value === 0) {
                      return 'transparent';
                    }
                    return 'rgba(0, 0, 0, 0.1)';
                  }
                },
                ticks: {
                  display: true,
                  stepSize: 20,
                  callback: function(value, index, values) {
                    return yLabels[index];
                  }
                }
              }
            },
            plugins: {
              legend: {
                display: false
              }
            },
            maintainAspectRatio: false
          }
        });
      }

      createChart();

      document.addEventListener("visibilitychange", function() {
        if (document.visibilityState === "visible") {
          createChart();
        }
      });
    }
    window.onload = initializeBarChart;
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
    initializeBarChart()
  </script>
  <script>
    // Event listeners for editing sales cells
    function attachSalesCellEventListeners() {
      document.querySelectorAll('.editable').forEach(cell => {
        cell.addEventListener('click', function() {
          this.setAttribute('contenteditable', 'true');
          this.focus();
        });
        cell.addEventListener('blur', function() {
          this.setAttribute('contenteditable', 'false');
          saveNewSalesValue(this); // Update this line
        });
        cell.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            this.blur();
          }
        });
      });
    }

    // Reattach event listeners for editing sales cells
    attachSalesCellEventListeners();

    // Function to save new sales value
    function saveNewSalesValue(cell) {
      const date = cell.parentElement.querySelector('[data-date]').getAttribute('data-date');
      const sales = parseFloat(cell.innerText.replace(/[^\d.]/g, '')); // Parse sales value

      // Send AJAX request to update sales
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_sales.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            // Update the sales value in the table cell
            cell.innerText = "$" + sales.toFixed(2);

            // Call function to recalculate daily and monthly profit
            recalculateProfit(date, sales);
          } else {
            console.error("Error updating sales: " + response.error);
          }
        }
      };
      xhr.send("date=" + date + "&sales=" + sales);
    }

    // Update event listener for month buttons
document.querySelectorAll('.month-btn').forEach(monthTab => {
    monthTab.addEventListener('click', function() {
        const selectedMonth = this.getAttribute('data-month');

        // Remove 'active' class from all month buttons
        document.querySelectorAll('.month-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Add 'active' class to the clicked button
        this.classList.add('active');

        // Show the loader
        document.getElementById('loader-container').style.display = 'flex';

        // Fetch new content and update the table container
        fetch(`fetch_data.php?month=${selectedMonth}`)
            .then(response => response.text())
            .then(data => {
                // Hide the loader once data is loaded
                document.getElementById('loader-container').style.display = 'none';
                // Update the table container with the fetched data
                document.getElementById('table-container').innerHTML = data;

                // Reattach event listeners for editing sales cells
                attachSalesCellEventListeners();

                // Update progress bars
                updateTableAndProgressBars(selectedMonth);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                // Hide the loader in case of an error
                document.getElementById('loader-container').style.display = 'none';
            });
    });
});

    function recalculateProfit(date, newSales) {
      // Find the row corresponding to the edited sales date
      const row = document.querySelector(`[data-date='${date}']`).closest('tr');

      // Retrieve other necessary data from the row
      const dailyCogs = parseFloat(row.cells[2].innerText.replace(/[^\d.]/g, ''));
      const otherAmount = parseFloat(row.cells[3].innerText.replace(/[^\d.]/g, ''));
      const totalFixed = parseFloat(row.cells[4].innerText.replace(/[^\d.]/g, ''));
      const laborPrice = parseFloat(row.cells[5].innerText.replace(/[^\d.]/g, ''));

      // Calculate new daily profit
      let newDailyProfit = newSales - dailyCogs - otherAmount - totalFixed - laborPrice;

      // Update the daily profit cell in the table
      row.cells[6].innerHTML = `<span class="${newDailyProfit >= 0 ? 'profit-positive' : 'profit-negative'}">$${Math.abs(newDailyProfit).toFixed(2)}</span>`;

      // Update the monthly profit cell for the edited row
      const monthlyProfitCell = row.cells[7];
      let currentMonthlyProfit = newDailyProfit;

      // If the updated row is the last row, its monthly profit will only be its daily profit
      if (!row.nextElementSibling) {
        monthlyProfitCell.innerHTML = `<span class="${currentMonthlyProfit >= 0 ? 'profit-positive' : 'profit-negative'}">$${currentMonthlyProfit.toFixed(2)}</span>`;
      } else {
        // If the updated row is not the last row, its monthly profit will be its edited daily profit plus the next row's monthly profit
        const nextRow = row.nextElementSibling;
        const nextMonthlyProfit = parseFloat(nextRow.cells[7].innerText.replace(/[^\d.]/g, ''));
        currentMonthlyProfit += nextMonthlyProfit;
        monthlyProfitCell.innerHTML = `<span class="${currentMonthlyProfit >= 0 ? 'profit-positive' : 'profit-negative'}">$${currentMonthlyProfit.toFixed(2)}</span>`;
      }

      // Update the monthly profits of previous rows
      let previousRow = row.previousElementSibling;
      while (previousRow) {
        const previousDailyProfit = parseFloat(previousRow.cells[6].innerText.replace(/[^\d.-]/g, ''));

        // Add the daily profit to the current monthly profit
        currentMonthlyProfit += previousDailyProfit;

        // Update the monthly profit cell for the previous row
        previousRow.cells[7].innerHTML = `<span class="${currentMonthlyProfit >= 0 ? 'profit-positive' : 'profit-negative'}">$${Math.abs(currentMonthlyProfit).toFixed(2)}</span>`;

        // Move to the previous row
        previousRow = previousRow.previousElementSibling;
      }

      // Update the monthly profit in the database via AJAX
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_monthly_profit.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            console.log("Monthly profit updated successfully!");
          } else {
            console.error("Error updating monthly profit: " + response.error);
          }
        }
      };
      xhr.send("date=" + date + "&monthly_profit=" + currentMonthlyProfit);
    }
  </script>
  <script>
   
   var selectedMonth = new Date().getMonth() + 1;
   function updateTableAndProgressBars(selectedMonth) {
 
$.ajax({
    url: 'get_progress_data.php',
    method: 'GET',
    data: {
        month: selectedMonth
    },
    success: function(response) {
    updateProgressBars(response.progressBarData);
},

    error: function(xhr, status, error) {
        console.error('Error fetching progress data:', error);
    }
});

}

function updateProgressBars(progressBarData) {
    try {
        $('#cogs-progress-box .progress-bar-fill').css('width', progressBarData.cogsPercentage + '%');
        $('#labor-progress-box .progress-bar-fill').css('width', progressBarData.laborPercentage + '%');
        $('#fixed-progress-box .progress-bar-fill').css('width', progressBarData.fixedPercentage + '%');
        $('#other-progress-box .progress-bar-fill').css('width', progressBarData.otherPercentage + '%');
        $('#net-progress-box .progress-bar-fill').css('width', progressBarData.netPercentage + '%');
    } catch (error) {
        console.error('Error updating progress bars:', error);
    }
}

$(window).ready(function() {
    updateTableAndProgressBars(selectedMonth);
});

  </script>

</body>

</html>