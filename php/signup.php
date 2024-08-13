<?php
$conn = mysqli_connect('localhost', 'root', '', 'ishield');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate unique empid starting with "IS" and a random 4-digit number
function generateEmpId($conn) {
    $prefix = "IS";
    $unique = false;
    $empid = '';

    // Fetch the latest empid from the database to determine the next sequential number
    $query = "SELECT MAX(RIGHT(empid, 4)) AS max_empid FROM register WHERE empid LIKE 'IS%'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $max_empid = $row['max_empid'];

    // Increment the last sequential number
    $next_number = ($max_empid !== null) ? intval($max_empid) + 1 : 1;
    $padded_number = str_pad($next_number, 4, '0', STR_PAD_LEFT); // Pad with leading zeros if necessary

    // Generate the new empid
    $empid = $prefix . $padded_number;

    return $empid;
}

// Fetch input data from POST
$empid = generateEmpId($conn); // Generate unique empid
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmpassword = $_POST['confirmpassword'];

// Ensure that passwords match
if ($password !== $confirmpassword) {
    echo "<script type='text/javascript'>alert('Passwords do not match!'); location='../sign_in.html';</script>";
    exit();
}

// Prepare SQL statement to avoid SQL injection
$stmt = $conn->prepare("INSERT INTO register (empid, username, email, password, confirmpassword) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $empid, $username, $email, $password, $confirmpassword);

// Execute the statement
if ($stmt->execute()) {
    echo "<script type='text/javascript'>alert('Registration successful! Please login.'); location='../sign_in.html';</script>";
} else {
    echo "<script type='text/javascript'>alert('Registration failed!'); location='../sign_in.html';</script>";
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
mysqli_close($conn);
?>
