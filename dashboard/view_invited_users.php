<?php
session_start(); // Ensure you are using sessions to track logged-in users

function getInvitedUsers($conn, $inviter_id) {
    $stmt = $conn->prepare("SELECT empid, username, email, EmpRole FROM register WHERE inviter_id = ?");
    $stmt->bind_param("s", $inviter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invited_users = [];
    while ($row = $result->fetch_assoc()) {
        $invited_users[] = $row;
    }
    $stmt->close();
    return $invited_users;
}

// Assuming you have the current logged-in user's inviter_id stored in session
$current_user_inviter_id = $_SESSION['empid'];

// Create connection to the database
$servername = "localhost";
$username = "root";
$password = "";
$database = "ishield";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$invited_users = getInvitedUsers($conn, $current_user_inviter_id);
$conn->close();
?>
