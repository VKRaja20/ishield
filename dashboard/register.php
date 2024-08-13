<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ishield"; // Corrected database name

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a new EmpId
function generateEmpId($conn) {
    $prefix = "ISH";
    $empid = '';

    // Fetch the latest empid from the database
    $query = "SELECT MAX(RIGHT(empid, 4)) AS max_empid FROM register WHERE empid LIKE 'ISH%'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $max_empid = $row['max_empid'];

    // Increment the last sequential number
    $next_number = ($max_empid !== null) ? intval($max_empid) + 1 : 1;
    $padded_number = str_pad($next_number, 4, '0', STR_PAD_LEFT); // Pad with leading zeros if necessary

    // Generate the new empid
    $empid = $prefix . $padded_number;

    return $empid;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $EmpRole = $_POST['EmpRole'];
    $inviter_id = $_POST['inviter_id'];

    // Validate inputs
    if ($password !== $confirmpassword) {
        echo "<script>
                alert('Passwords do not match.');
                window.location.href = '../Sign.html';
              </script>";
        exit();
    }

    // Generate a new EmpId
    $empid = generateEmpId($conn);

    // Prepare and bind the statement
    $stmt = $conn->prepare("INSERT INTO register (empid, username, email, password, EmpRole, inviter_id) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $empid, $username, $email, $password, $EmpRole, $inviter_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful!');
                window.location.href = '../Sign_in.html';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $stmt->error . "');
                window.location.href = '../Sign.html';
              </script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
