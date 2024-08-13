<?php
include 'database.php'; // Include database connection

// Define role hierarchy and earnings
$role_hierarchy = [
    'Business_Development_Officer' => [],
    'Sales_Manager' => ['Business_Development_Officer'],
    'Regional_Coordinator' => ['Sales_Manager', 'Business_Development_Officer'],
    'Zonal_Head' => ['Regional_Coordinator', 'Sales_Manager', 'Business_Development_Officer'],
    'State_President' => ['Zonal_Head', 'Regional_Coordinator', 'Sales_Manager', 'Business_Development_Officer'],
    'Royality_Stake_Holder' => ['State_President', 'Zonal_Head', 'Regional_Coordinator', 'Sales_Manager', 'Business_Development_Officer']
];

$earnings = [
    'Business_Development_Officer' => 50,
    'Sales_Manager' => 70,
    'Regional_Coordinator' => 86,
    'Zonal_Head' => 104,
    'State_President' => 116,
    'Royality_Stake_Holder' => 122
];

function getEarningsForRole($role, $earnings) {
    return $earnings[$role] ?? 0;
}

function getReferrerEarnings($role, $earnings) {
    $earnings_map = [
        'Business_Development_Officer' => 50,
        'Sales_Manager' => 70,
        'Regional_Coordinator' => 86,
        'Zonal_Head' => 104,
        'State_President' => 116,
        'Royality_Stake_Holder' => 122
    ];

    // Calculate referrer earnings based on the role
    return array_reduce(array_keys($earnings_map), function($carry, $r) use ($role, $earnings_map) {
        return $carry + ($r === $role ? 0 : $earnings_map[$r] * in_array($role, $role_hierarchy[$r]));
    }, 0);
}

// Function to update balance for referred and referrer
function updateBalances($conn, $inviter_empid) {
    global $role_hierarchy, $earnings;

    // Get the role of the inviter
    $query = "SELECT role, empid FROM register WHERE empid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $inviter_empid);
    $stmt->execute();
    $result = $stmt->get_result();
    $inviter = $result->fetch_assoc();
    $inviter_role = $inviter['role'] ?? null;

    if (!$inviter_role) {
        echo "Inviter not found.";
        return;
    }

    // Calculate earnings for the inviter
    $inviter_earnings = getEarningsForRole($inviter_role, $earnings);

    // Update balance for inviter
    $query = "UPDATE register SET balance = balance + ? WHERE empid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $inviter_earnings, $inviter_empid);
    $stmt->execute();

    // Fetch and update balance for referrer
    foreach ($role_hierarchy[$inviter_role] as $referrer_role) {
        $query = "SELECT empid FROM register WHERE role = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $referrer_role);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $referrer_empid = $row['empid'];
            $referrer_earnings = getReferrerEarnings($referrer_role, $earnings);
            $query = "UPDATE register SET balance = balance + ? WHERE empid = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $referrer_earnings, $referrer_empid);
            $stmt->execute();
        }
    }
}

// Example usage
$logged_in_empid = $_SESSION['empid']; // Assume this is set after user logs in
updateBalances($conn, $logged_in_empid);

$conn->close();

?>
