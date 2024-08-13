<?php
session_start(); // Ensure you start the session

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ishield";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the email is in the session
if (!isset($_SESSION['email'])) {
    die("No email found in session!");
}

$email = $_SESSION['email'];

// Fetch the user's role from the database
$sql = "SELECT EmpRole FROM register WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $currentUserRole = $row['EmpRole'];
} else {
    die("User role not found!");
}

$stmt->close();

// Define the role hierarchy
$roles = [
    'Business_Development_Officer' => [],
    'Sales_Manager' => ['Business_Development_Officer'],
    'Regional_Coordinator' => ['Sales_Manager', 'Business_Development_Officer'],
    'Zonal_Head' => ['Sales_Manager', 'Business_Development_Officer', 'Regional_Coordinator'],
    'State_President' => ['Sales_Manager', 'Business_Development_Officer', 'Regional_Coordinator', 'Zonal_Head'],
    'Royality_Stake_Holder' => ['State_President', 'Sales_Manager', 'Business_Development_Officer', 'Regional_Coordinator', 'Zonal_Head']
];

// Determine allowed roles based on current user's role
$allowedRoles = $roles[$currentUserRole] ?? [];

// If the current user role is Royality_Stake_Holder, allow all roles except themselves
if ($currentUserRole === 'Royality_Stake_Holder') {
    $allowedRoles = array_keys($roles);
    $allowedRoles = array_filter($allowedRoles, function($role) {
        return $role !== 'Royality_Stake_Holder'; // Exclude Royality_Stake_Holder from the allowed roles
    });
}

// Convert allowed roles to the format expected by the frontend
$response = array_map(function($role) {
    return ['value' => $role, 'text' => str_replace('_', ' ', $role)];
}, $allowedRoles);

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$conn->close();
?>
