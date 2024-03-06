<?php
include 'db.php'; // Ensure this path is correct

session_start(); // Start the session if you plan to use sessions for logged-in user

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $_POST['password']; // Direct retrieval, as this will be hashed

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $row['id']; // Store user id in session
            $_SESSION['username'] = $username; // Store username in session for later use
            header("Location: index.php"); // Redirect to a home page or dashboard
            exit();
        } else {
            // Password is incorrect
            // Consider using a session to pass error messages
            $_SESSION['error'] = 'Invalid username or password.';
            header("Location: log.php"); // Redirect back to login page
            exit();
        }
    } else {
        // Username does not exist
        $_SESSION['error'] = 'Invalid username or password.';
        header("Location: log.php");
        exit();
    }

} else {
    // Redirect back to the login page if not a POST request
    header("Location: log.php");
    exit();
}
?>
