<?php
$db_host = 'localhost';  // Database host
$db_name = 'ishield';  // Database name
$db_user = 'root';  // Database username
$db_pass = '';  // Database password

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Protect against SQL injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Prepare and bind statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM register WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Start a new session
        session_start();
        session_regenerate_id(true); // Regenerate session ID to prevent fixation
        $_SESSION['email'] = $email;
        // Redirect to your desired page after successful login
        header("Location: ../dashboard/index.html");
        exit;
    } else {
        // Invalid email or password
        echo "<script type='text/javascript'>alert('Invalid email or password!'); location='../sign_in.html';</script>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
