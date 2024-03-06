<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    // Capture form data
    $name = $conn->real_escape_string($_POST['name']);
    $time = $conn->real_escape_string($_POST['time']);
    $amount = $conn->real_escape_string($_POST['amount']);
    $date = $conn->real_escape_string($_POST['date']); // Capture date value

    // SQL to insert data into other table
    $sql = "INSERT INTO other (name, time, amount, date) VALUES ('$name', '$time', '$amount', '$date')";

    if ($conn->query($sql) === TRUE) {
        $conn->close(); // Close connection after inserting
        header("Location: index.php"); // Redirect back to the main page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

?>>
