<?php

// Include necessary files
include('header.php');
include('database.php');

// Get the logged-in user's email
$email = $_SESSION['email']; 

// Function to fetch user details
function getUserDetails($conn, $email) {
    $query = "SELECT empid, EmpRole FROM register WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to fetch appointed employees
function getAppointedEmployees($conn, $empid) {
    $query = "SELECT empid FROM register WHERE inviter_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $empid);
    $stmt->execute();
    $result = $stmt->get_result();
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row['empid'];
    }
    return $employees;
}

// Function to calculate total number of direct referrals
function calculateTotalDirectReferrals($conn, $appointedEmpids) {
    $totalReferrals = 0;
    foreach ($appointedEmpids as $empid) {
        $query = "SELECT COUNT(*) as directReferralCount FROM user WHERE empid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $empid);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalReferrals += $result->fetch_assoc()['directReferralCount'];
    }
    return $totalReferrals;
}

// Fetch user details
$userDetails = getUserDetails($conn, $email);
$empid = $userDetails['empid'];

// Fetch appointed employees
$appointedEmpids = getAppointedEmployees($conn, $empid);

// Calculate total direct referrals
$totalDirectReferrals = calculateTotalDirectReferrals($conn, $appointedEmpids);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Earnings</title>
    <style>
        /* styles.css */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .main-container {
            padding: 20px;
            background-color: #fff;
            margin: 50px auto;
            max-width: 800px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .user-details p {
            font-size: 16px;
            margin: 5px 0;
            color: #333;
        }
        .earnings-section {
            margin-top: 40px;
        }
        .earnings-section h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .earnings-section p {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }
        .earnings-section strong {
            color: #2c3e50;
        }
        @media (max-width: 600px) {
            .main-container {
                margin: 20px;
                padding: 15px;
            }
            .earnings-section h2 {
                font-size: 20px;
            }
            .earnings-section p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="main-container">
            <!-- User details section -->
            <div class="user-details" style="text-align: right; margin-top: 20px; margin-right: 20px;">
                <p>Emp ID: <strong><?php echo htmlspecialchars($empid); ?></strong></p>
                <p>Role: <strong><?php echo htmlspecialchars($userDetails['EmpRole']); ?></strong></p>
            </div>

            <!-- Earnings section -->
            <div class="earnings-section" style="margin: 40px;">
                <h2>My Earnings for This Month</h2>
                <p>Total Direct Referrals of Appointed Employees: <strong><?php echo htmlspecialchars($totalDirectReferrals); ?></strong></p>
            </div>
        </div>
    </main>
</body>
</html>
