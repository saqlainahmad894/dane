<?php
include 'db.php'; // Make sure this path is correct

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data to prevent SQL injection
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    // Prepare the SQL statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        echo "New record created successfully";
        // Redirect to index.php
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If not a POST request, redirect to the form page (or wherever you want)
    header("Location: log.php"); // Adjust if necessary
    exit();
}
?>
