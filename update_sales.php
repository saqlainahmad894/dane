<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $sales = $_POST['sales'];

    // Update sales in the invoices table
    $sql = "UPDATE invoices SET sales = $sales WHERE invoice_date = '$date'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "error" => $conn->error));
    }
    
    $conn->close();
} else {
    echo json_encode(array("success" => false, "error" => "Invalid request method"));
}
?>
