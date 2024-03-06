<html lang="en">
  <head>
 <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<title>Great Dane Coffee</title>
<link rel="stylesheet" href="includes/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="icon" href="includes/images/logo.png" type="image/x-icon">
<link href="https://cdn.jsdelivr.net/npm/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2/dist/js/select2.min.js"></script>
  </head>
  <body>
  <div class="container-fluid">
      <div class="row">
        <!-- Sidebar -->
        <div id="sidebar-placeholder" class="col-lg-2 col-md-0 col-12 p-0">
          <nav class="sidebar">
            <div class="sidebar-header">
              <img src="includes/images/logo.png" alt="Logo">
              <span>Great Dane Coffee</span>
            </div>
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link active" href="#" data-content="daily-profit">
                <i class="fa-solid fa-chart-line daily-profit-icon"></i>
                <span>Daily Profit</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" data-content="sales">
                <i class="fa-solid fa-dollar-sign sales-icon"></i>
                <span>Sales</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" id="sidebar-cogs-link" data-content="cogs">
                <i class="fa-solid fa-lemon cogs-icon"></i>
                <span>COGS</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" data-content="labor">
                <i class="fa-solid fa-users labour-icon"></i>
                <span>Labor</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" data-content="fixed">
                <i class="fa-solid fa-car fixed-icon"></i>
                <span>Fixed</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" data-content="others">
                <i class="fa-solid fa-credit-card other-icon"></i>
                <span>Others</span>
                </a>
              </li>
            </ul>
            </ul>
            <ul class="nav flex-column" style="margin-top: auto; margin-bottom: 5px; border-top: 1px solid #757575;">
              <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="fa-solid fa-gear settings-icon"></i>
                <span>Settings</span>
                </a>
              </li>
              <li class="nav-item">
                <a style="text-decoration: none;" class="nav-link1" href="log.php">
                <i class="fa-solid fa-arrow-right-from-bracket logout-icon"></i>
                <span>Logout</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <div id="main-content-placeholder" class="col-lg-7 col-md-9 col-12 px-4">
        </div>
        <div id="aside-content-placeholder" class="col-lg-3 col-md-3 col-12 p-0 ml-auto">
        </div>
      </div>
    </div>

    <script>
  $(function() {
    function showLoadingIndicator() {
      var overlay = $('<div id="loading-overlay"></div>');
      overlay.css({
        'position': 'fixed',
        'top': 0,
        'left': 0,
        'width': '100%',
        'height': '100%',
        'background': 'rgba(255, 255, 255, 0.8)',
        'z-index': 9999,
        'display': 'flex',
        'justify-content': 'center',
        'align-items': 'center',
      });
      overlay.html('<img src="includes/images/loader.gif" alt="Loading...">');
      $('body').append(overlay);
    }

    function hideLoadingIndicator() {
      $('#loading-overlay').remove();
    }

   
    function loadContent(url, placeholderSelector, openModalFunction = null, setActiveNav = null) {
        showLoadingIndicator(); // Show the loading overlay before making the request
        $.ajax({
            url: url,
            success: function(response) {
                $(placeholderSelector).html(response);
                hideLoadingIndicator(); // Hide the loading overlay when the content is loaded
                if (openModalFunction) {
                    openModalFunction(); // Call the function to open a specific modal if provided
                }
                if (setActiveNav) {
                    // Remove 'active' class from all nav links and set it on the specific nav item
                    $('.nav-item .nav-link').removeClass('active');
                    $(`.nav-item .nav-link[data-content="${setActiveNav}"]`).addClass('active');
                }
            },
            error: function() {
                $(placeholderSelector).html('<p>Error loading content.</p>');
                hideLoadingIndicator();
            }
        });
    }
    
    // Event listener for aside buttons with data-target-page attribute
    $('body').on('click', '.aside-button', function() {
        const targetPage = $(this).data('target-page');
        let openModalFunction = null;
        let setActiveNav = null;

        // Determine which content is being loaded and set the corresponding function to open the modal
        // Also, set which nav item should be active
        if (targetPage.includes("cogs")) {
            openModalFunction = () => $('#popup-form').modal('show');
            setActiveNav = "cogs"; // Assuming this is the data-content attribute value for the COGS nav link
        } else if (targetPage.includes("others")) {
            openModalFunction = () => $('#addOtherExpenseModal').modal('show');
            setActiveNav = "others";
        } else if (targetPage.includes("fixed")) {
            openModalFunction = () => $('#addFixedExpenseModal').modal('show');
            setActiveNav = "fixed";
        }

        loadContent(targetPage, '#main-content-placeholder', openModalFunction, setActiveNav);
    });

    loadContent("aside-content.php", "#aside-content-placeholder");
    loadContent("main-content.php", "#main-content-placeholder");
    $(document).ready(function() {
    // Function to handle clicking on a date
    $('body').on('click', '.clickable#c1', function() {
        // Assuming each clickable date element has a data attribute 'data-date' that holds its date
        // If not, you might need to adjust how you retrieve the date from the clicked element
        var date = $(this).data('date'); // Retrieve the date from the data attribute

        // Construct the URL to fetch data from invoice.php with the selected date
        var fetchUrl = 'invoice.php?date=' + date;

        // Show the loading indicator before making the AJAX call
        showLoadingIndicator();

        // Make the AJAX call to fetch data from invoice.php
        $.ajax({
            url: fetchUrl,
            success: function(response) {
                // On success, hide the loading indicator and update the main-content-placeholder with the response
                hideLoadingIndicator();
                $('#main-content-placeholder').html(response);
                // Optionally, set the active navigation link if needed
                $('.nav-link').removeClass('active');
                $('.nav-link[data-content="daily-profit"]').addClass('active');
            },
            error: function() {
                // On error, hide the loading indicator and show an error message
                hideLoadingIndicator();
                $('#main-content-placeholder').html('<p>Error loading content.</p>');
            }
        });
    });});
         $('body').on('click', '#cogs-header', function() {
      loadContent("cogs-content.php", "#main-content-placeholder");
      $('.nav-link').removeClass('active');
      $('.nav-link[data-content="cogs"]').addClass('active');
      });
     
    $('.nav-link').click(function(e) {
      e.preventDefault();
      var contentToLoad = $(this).data("content");
      $(".nav-link").removeClass('active');
      $(this).addClass('active');
      switch (contentToLoad) {
        case "daily-profit":
          loadContent("main-content.php", "#main-content-placeholder");
          break;
        case "sales":
          loadContent("main-content.php", "#main-content-placeholder");
          break;
        case "cogs":
          loadContent("cogs-content.php", "#main-content-placeholder");
          break;
        case "labor":
          loadContent("labour-content.php", "#main-content-placeholder");
          break;
        case "fixed":
          loadContent("fixed-content.php", "#main-content-placeholder");
          break;
        case "others":
          loadContent("others-content.php", "#main-content-placeholder");
          break;
      }
    });
  });
  
</script>
<script>
  $(document).ready(function() {
    $('.fixed-expense-btn').click(function() {
      $('#addOtherExpenseModal').modal('show');
    });
  });
  $(document).ready(function() {
$('.fixed-expense-btn').click(function() {
  console.log('Button clicked, trying to open modal...');
  $('#addOtherExpenseModal').modal('show');
});
});

</script>
<script>
  $(document).ready(function() {
    $('.fixed-expense-btn').click(function() {  
      $('#addFixedExpenseModal').modal('show');
    });
    $(document).on('click', '.add', function() {
        $('#popup-form').modal('show');
    });
  });

</script>
  </body>
</html>