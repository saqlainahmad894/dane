<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    $price = $conn->real_escape_string($_POST['price']); // Correct variable name
    $date = $conn->real_escape_string($_POST['date']); // Correct variable name

    // Check if a labor entry with the same date already exists
    $check_sql = "SELECT COUNT(*) as count FROM labor WHERE date = '$date'";
    $result = $conn->query($check_sql);

    if (!$result) {
        // Debugging: Display SQL error if any
        echo "Error: " . $check_sql . "<br>" . $conn->error;
    } else {
        $row = $result->fetch_assoc();
        $existing_count = $row['count'];

        if ($existing_count > 0) {
            // If a labor entry with the same date exists, display an error message
            echo "<script>alert('Error: A labor entry with the same date already exists.');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        } else {
            // SQL to insert data into labor table
            $sql = "INSERT INTO labor (price, date) VALUES ('$price', '$date')";

            if ($conn->query($sql) === TRUE) {
                $conn->close(); // Close connection after inserting
                header("Location: index.php"); // Redirect back to the main page
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
}
?>
