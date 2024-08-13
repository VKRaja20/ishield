<?php
include 'database.php'; // Ensure this file correctly sets up the $conn variable
include 'header.php';   // Include header if needed (e.g., for navigation)

// Fetch user email from session
$user_email = $_SESSION['email'] ?? ''; // Fetch email from session

if (empty($user_email)) {
    die("User not logged in.");
}

// Prepare and execute SQL query for user data
$sql = "SELECT * FROM register WHERE email = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $balance = $user['balance'];

    // Prepare and execute SQL query for transaction history
    $sql = "SELECT * FROM transactions WHERE email = ? ORDER BY date DESC";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param('s', $user_email);
    $stmt->execute();
    $transactions = $stmt->get_result();
} else {
    $balance = 'N/A';
    $transactions = [];
}

$stmt->close();
$conn->close();
?> 
<style>
    /* Main Content Styles */

.head-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.head-title h1 {
    margin: 0;
    font-size: 2em;
    color: #333;
}

.breadcrumb {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
}

.breadcrumb li {
    margin-right: 5px;
}

.breadcrumb a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb i {
    margin: 0 5px;
    color: #888;
}

.wallet-container {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.wallet-container h1 {
    margin-top: 0;
    font-size: 1.8em;
    color: #333;
}

.balance {
    margin-bottom: 20px;
}

.balance h2 {
    margin: 0;
    font-size: 1.5em;
    color: #555;
}

.balance p {
    font-size: 2em;
    color: #2e8b57;
    margin: 0;
}

.transaction-history h2 {
    margin-top: 0;
    font-size: 1.5em;
    color: #333;
}

.transaction-history ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.transaction-history li {
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
}

.transaction-date {
    font-weight: bold;
    color: #333;
}

.transaction-description {
    color: #555;
}

.withdrawal {
    margin-top: 20px;
}

.withdraw-button {
    background-color: #2e8b57;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 1em;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.withdraw-button:hover {
    background-color: #1f6b40;
}

</style><!-- MAIN -->
<main>
    <div class="head-title">
        <div class="left">
            <h1>Dashboard</h1>
            <ul class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li><a class="active" href="#">Wallet</a></li>
            </ul>
        </div>
    </div>

    <div class="wallet-container">
        <h1>My Wallet</h1>
        <div class="balance">
            <h2>Balance</h2>
            <p>$<?php echo htmlspecialchars(number_format($balance, 2)); ?></p>
        </div>
        <div class="transaction-history">
            <h2>Transaction History</h2>
            <ul>
                <?php if (isset($transactions) && $transactions->num_rows > 0): ?>
                    <?php while ($transaction = $transactions->fetch_assoc()): ?>
                        <li>
                            <span class="transaction-date"><?php echo htmlspecialchars($transaction['date']); ?></span>
                            <span
                                class="transaction-description"><?php echo htmlspecialchars($transaction['description']); ?></span>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No transactions found.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="withdrawal">
            <button class="withdraw-button">Withdraw Funds</button>
        </div>
    </div>
</main>
<!-- MAIN -->
</section>
<!-- CONTENT -->

<script src="script.js"></script>
</body>

</html>