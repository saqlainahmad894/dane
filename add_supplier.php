<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    // Extract data from POST
    $supplierName = $conn->real_escape_string($_POST['supplierName']);
    $abbreviation = $conn->real_escape_string($_POST['abbreviation']);
    $websitelink = $conn->real_escape_string($_POST['websitelink']);
    $catalogpath = $conn->real_escape_string($_POST['catalogpath']);
    $cataloglink = $conn->real_escape_string($_POST['cataloglink']);
    $contactname = $conn->real_escape_string($_POST['contactname']);
    $contactemail = $conn->real_escape_string($_POST['contactemail']);
    $orderemail = $conn->real_escape_string($_POST['orderemail']);
    $address = $conn->real_escape_string($_POST['address']);
    $notes = $conn->real_escape_string($_POST['notes']);
    $status = $conn->real_escape_string($_POST['status']);

    // SQL to insert data into suppliers table
    $sql = "INSERT INTO suppliers (name, abbreviation, websitelink, catalogpath, cataloglink, contactname, contactemail, orderemail, address, notes, status) VALUES ('$supplierName', '$abbreviation', '$websitelink', '$catalogpath', '$cataloglink', '$contactname', '$contactemail', '$orderemail', '$address', '$notes', '$status')";

    if ($conn->query($sql) === TRUE) {
        $conn->close(); // Close connection after inserting
        header("Location: index.php"); // Redirect back to the main page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
