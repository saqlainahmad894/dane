<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap JS (make sure it's loaded after jQuery) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Center loader vertically and horizontally */
        .loader-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.5);
            /* Semi-transparent background */
            z-index: 9999;
            /* Ensure it's above other elements */
        }
    </style>
</head>

<body>
    <main role="main">
        <div class="dashboard-top-bar">
            <button id="mobile-menu-button" class="btn d-lg-none d-md-none">
                <i class="fa fa-bars"></i>
            </button>
            <div class="dashboard-title">
                <h4><b>Labor</b></h4>
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
                        <th>Date</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody id="table-container">
                    <?php
                    // Include the initial table content for February
                    include 'fetch_labor_data.php';
                    ?>
                </tbody>
            </table>
        </div>
        <div id="loader-container" class="loader-container" style="display: none;">
            <img src="includes/images/loader.gif" alt="Loading..." />
        </div>
        <br><br>
        <button class="labor-btn">ADD LABOR</button>
        <div class="modal" id="addFixedExpenseModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Labor</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="add_labor.php" method="POST">
                        <div class="modal-body">
                            <!-- Expense Name -->
                            <div class="form-group">
                                <label for="price">Price:<span style="color:red">*</span></label>
                                <div class="input-group" style="margin:0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text inv-form2">$</span>
                                    </div>
                                    <input type="number" class="form-control inv-form1" id="price" name="price" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="date">Date:<span style="color:red">*</span></label>
                                <input type="date" class="form-control inv-form" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" style="border-radius: 20px; color:white ; background-color: black;border-color: black;">Add Labor</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px; color:black ; background-color: #dee2e6;border-color: #dee2e6;font-weight: 600;">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script>
        $(document).ready(function() {
            // Function to filter labor data based on selected month and year
            function filterLaborData(month, year) {
                $('#loader-container').show();
                // Send AJAX request to fetch data for the selected month and year
                $.ajax({
                    url: 'fetch_labor_data.php', // Specify the URL of your PHP script to fetch data
                    type: 'GET',
                    data: {
                        'month': month,
                        'year': year
                    },
                    success: function(response) {
                        // Replace the table body content with the fetched data
                        $('#table-container').html(response);
                        $('#loader-container').hide();
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error('Failed to fetch labor data', error);
                        // Optionally, provide user feedback or handle the error accordingly
                        $('#loader-container').hide();
                    }
                });
            }

            // Click event for month buttons to filter data
            $('.month-btn').on('click', function() {
                // Update active class for buttons
                $('.month-btn').removeClass('active');
                $(this).addClass('active');

                // Fetch selected month and year
                var selectedMonth = parseInt($(this).data('month'));
                var selectedYear = parseInt($(this).data('year'));

                // Filter labor data based on selection
                filterLaborData(selectedMonth, selectedYear);
            });

            // Modal display for adding labor expense
            $('.labor-btn').on('click', function() {
                $('#addFixedExpenseModal').modal('show');
            });

            // Event to handle the update of labor price on losing focus
            $('.edit-price').on('blur', function() {
                var id = $(this).data('id');
                var price = $(this).text().replace('$', '').trim(); // Clean the price value

                // AJAX request to update the price in the database
                $.ajax({
                    url: 'update_labor.php', // Specify your backend script for updating
                    type: 'POST',
                    data: {
                        'id': id,
                        'price': price
                    },
                    success: function(response) {
                        // Handle response
                        console.log('Update successful', response);
                        // Optionally, provide user feedback or refresh part of your UI here
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error('Update failed', error);
                        // Optionally, alert the user that the update did not succeed
                    }
                });
            });

            // Optionally, initialize the view by filtering or showing the current month's labor data
            // This could be a separate function call similar to the click handling but for document ready
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
</body>

</html>