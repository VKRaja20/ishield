<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ishield";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session
session_start();

// Retrieve the email from the session
if (isset($_SESSION['email'])) {
    $email = $conn->real_escape_string($_SESSION['email']);

    // Query to get empid based on the email address
    $query = "SELECT * FROM register WHERE email = '$email'";
    $result = $conn->query($query);
    // if ($result){
    //      // Fetch the result
    //      if ($row = $result->fetch_assoc()) {
    //         // Store empid in a variable
    //         $empid = $row['empid'];
            
    //         // Output the empid for demonstration purposes
    //         echo "The empid is: " . htmlspecialchars($empid);
    //     } else {
    //         // No record found
    //         echo "No user found with the provided email";
    //     }
    // }

    if ($result->num_rows > 0) {
        // Fetch the empid
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $empid = $row['empid'];

        // Return the empid as inviter_id
        echo json_encode(['inviter_id' => $empid]);
    } else {
        // Handle case where email does not exist
        echo json_encode(['error' => 'No user found with the provided email']);
    }
} else {
    // Handle case where email is not provided
    echo json_encode(['error' => 'Email not provided']);
}

// Close the database connection
$conn->close();
?>
