<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    // Capture form data
    $name = $conn->real_escape_string($_POST['name']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $amount = $conn->real_escape_string($_POST['amount']);
    // Check if the end date is set and not empty
    $endDate = !empty($_POST['endDate']) ? $conn->real_escape_string($_POST['endDate']) : NULL; // Handling optional end date

    // SQL to insert data into fixed table
    $sql = "INSERT INTO fixed (duration, name, enddate, amount) VALUES ('$duration', '$name', ";
    // Append the end date or NULL depending on the condition
    $sql .= $endDate ? "'$endDate'" : 'NULL';
    $sql .= ", $amount)";

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
