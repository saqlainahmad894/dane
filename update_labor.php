<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    $id = $conn->real_escape_string($_POST['id']); // Correct variable name
    $price = $conn->real_escape_string($_POST['price']); // Correct variable name

    // SQL to update price in labor table
    $sql = "UPDATE labor SET price = '$price' WHERE Id = '$id'";

    if ($conn->query($sql) === TRUE) {
        $conn->close(); // Close connection after updating
        echo "Price updated successfully";
    } else {
        echo "Error updating price: " . $conn->error;
    }
} else {
    echo "Invalid request";
}
?>
