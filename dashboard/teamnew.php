<?php

include 'header.php';
include 'database.php';

// Get the current user's email from the session
$current_email = $_SESSION['email'] ?? '';
if (empty($current_email)) {
    die("User not logged in. Please log in first.");
}

// Fetch current user's empid using their email
$empid_sql = "SELECT empid FROM register WHERE email = ?";
$empid_stmt = $conn->prepare($empid_sql);
$empid_stmt->bind_param("s", $current_email);
$empid_stmt->execute();
$empid_result = $empid_stmt->get_result();

if ($empid_result->num_rows > 0) {
    $current_empid_row = $empid_result->fetch_assoc();
    $current_empid = $current_empid_row['empid'];
} else {
    die("No user found with the provided email.");
}

// Fetch details of employees registered with the same inviter_id as the current user's empid
$invited_employee_sql = "
SELECT r.username, r.empid, r.EmpRole, r.email, k.mobile, k.city
FROM register r
LEFT JOIN user_kyc k ON r.email = k.email
WHERE r.inviter_id = ?";
$invited_employee_stmt = $conn->prepare($invited_employee_sql);
$invited_employee_stmt->bind_param("s", $current_empid);
$invited_employee_stmt->execute();
$invited_employee_result = $invited_employee_stmt->get_result();

$invited_employee_details = [];
$invited_employee_count = 0; // Initialize count
if ($invited_employee_result->num_rows > 0) {
    $invited_employee_details = $invited_employee_result->fetch_all(MYSQLI_ASSOC);
    $invited_employee_count = $invited_employee_result->num_rows; // Count total employees
}

// Fetch count of employees by each EmpRole
$role_count_sql = "
SELECT EmpRole, COUNT(*) as role_count
FROM register
WHERE inviter_id = ?
GROUP BY EmpRole";
$role_count_stmt = $conn->prepare($role_count_sql);
$role_count_stmt->bind_param("s", $current_empid);
$role_count_stmt->execute();
$role_count_result = $role_count_stmt->get_result();

$role_count = [];
if ($role_count_result->num_rows > 0) {
    while ($row = $role_count_result->fetch_assoc()) {
        $role_count[$row['EmpRole']] = $row['role_count'];
    }
}

// Fetch details of employee with the same empid as the currently logged-in user
$details_sql = "SELECT name, phone, activation_plan, completed_tasks, incompleted_tasks
                FROM user
                WHERE empid = ?";
$details_stmt = $conn->prepare($details_sql);
$details_stmt->bind_param("s", $current_empid);
$details_stmt->execute();
$details_result = $details_stmt->get_result();

$invited_user_details = [];
$invited_user_count = 0; // Initialize count
if ($details_result->num_rows > 0) {
    $invited_user_details = $details_result->fetch_all(MYSQLI_ASSOC);
    $invited_user_count = $details_result->num_rows; // Count total invited employees
}

// Close the database connection
$empid_stmt->close();
$invited_employee_stmt->close();
$details_stmt->close();
$conn->close();
?>
<style>
 /* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    color: #444;
    background-color: #ffff;
    margin: 0;
    padding: 0;
}

h1, h2, h3 {
    color: #2c3e50;
    font-weight: 600;
    margin-top: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Button Styles */
.btn {
    display: inline-block;
    padding: 12px 24px;
    font-size: 16px;
    color: #fff;
    background-color: #3498db;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s, transform 0.2s, box-shadow 0.3s;
    border: none;
    cursor: pointer;
    margin: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background-color: #2ecc71;
}

.btn-primary:hover {
    background-color: #27ae60;
}

.btn-copy, .btn-share {
    background-color: #e67e22;
}

.btn-copy:hover, .btn-share:hover {
    background-color: #d35400;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    animation: slideDown 0.3s;
}

.modal-header {
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.modal-footer {
    border-top: 1px solid #ddd;
    padding-top: 10px;
    text-align: right;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #333;
    text-decoration: none;
    cursor: pointer;
}

/* Table Styles */
.invited-employee-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.invited-employee-table th, .invited-employee-table td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.invited-employee-table th {
    background-color: #3498db;
    color: #fff;
    font-weight: 600;
}

.invited-employee-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

.invited-employee-table tr:hover {
    background-color: #e1ecf4;
}

/* List Styles */
.horizontal-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
}

.horizontal-list li {
    position: relative;
    margin-right: 20px;
    padding-left: 20px;
    font-weight: 500;
    color: #2980b9;
}

.horizontal-list li::before {
    content: '•';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    color: #2980b9;
    font-size: 24px;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 90%;
    }

    .horizontal-list {
        flex-direction: column;
    }

    .horizontal-list li {
        margin-bottom: 10px;
        margin-right: 0;
    }

    .btn {
        padding: 10px 18px;
        font-size: 14px;
    }
}


</style>
<main>
    <div class="head-title">
        <div class="left">
            <h1>Dashboard</h1>
            <ul class="breadcrumb">
                <li><a href="index.html">Dashboard</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li><a class="active" href="profile.php">TEAM</a></li>
            </ul>
        </div>
    </div>
    <div class="head-title">
        <h1>Team Management</h1>
    </div>

    <!-- Button Container -->
<div class="button-container">
    <a href="#" id="openModalBtn" class="btn">
        <i class='bx bx-user-plus'></i>
        <span class="text">Add Employee</span>
    </a>
</div>
<!-- Invited employee Details -->
<div class="invited-employee-container">
    <h2>Details of Employees Appointed by You</h2>
    <?php if (!empty($invited_employee_details)): ?>
        <table class="invited-employee-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Employee ID</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invited_employee_details as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['empid']); ?></td>
                        <td><?php echo htmlspecialchars($user['EmpRole']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                        <td><?php echo htmlspecialchars($user['city']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="horizontal-count">Total Employees Referred: <?php echo $invited_employee_count; ?></p>

<h3>Employee Count by Role</h3>
<ul class="horizontal-list">
    <?php foreach ($role_count as $role => $count): ?>
        <li><?php echo htmlspecialchars($role) . ': ' . htmlspecialchars($count); ?></li>
    <?php endforeach; ?>
</ul>

    <?php else: ?>
        <p>No details available for employees with your empid.</p>
    <?php endif; ?>
</div>


    <!-- Invited user Details -->
    <div class="invited-user-container">
        <h2>Details of Invited user</h2>
        <?php if (!empty($invited_user_details)): ?>
            <table class="invited-employee-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Activation Plan</th>
                        <th>Completed Tasks</th>
                        <th>Incompleted Tasks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invited_user_details as $emp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($emp['name']); ?></td>
                            <td><?php echo htmlspecialchars($emp['phone']); ?></td>
                            <td><?php echo htmlspecialchars($emp['activation_plan']); ?></td>
                            <td><?php echo htmlspecialchars($emp['completed_tasks']); ?></td>
                            <td><?php echo htmlspecialchars($emp['incompleted_tasks']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>Total Invited user: <?php echo $invited_user_count; ?></p>
        <?php else: ?>
            <p>No details available for invited employees.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for Adding Employee -->
    <div id="signupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Generate Signup Link</h2>
            </div>
            <div class="modal-body">
                <form id="signupForm">
                    <label for="role">Role:</label>
                    <select id="role" name="role" class="role-box">
                        <!-- Options will be populated dynamically -->
                    </select>
                    <br>
                    <input type="submit" class="btn btn-primary" value="Generate Link">
                </form>
                <div id="linkResult" style="margin-top: 10px;">
                    <input type="text" id="inviteLink" class="copy-input" readonly>
                    <button class="btn-copy" id="copyLinkBtn">Copy Link</button>
                    <button class="btn-share" id="shareLinkBtn">Share Link</button>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-primary-close" id="closeModalBtn">Close</button>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript -->
<script>
    // Modal functionality
    var modal = document.getElementById("signupModal");
    var openBtn = document.getElementById("openModalBtn");
    var closeBtn = document.getElementsByClassName("close")[0];
    var closeModalBtn = document.getElementById("closeModalBtn");

    openBtn.onclick = function () {
        modal.style.display = "block";
        loadAllowedRoles(); // Load allowed roles when the modal is opened
    }

    closeBtn.onclick = function () {
        modal.style.display = "none";
    }

    closeModalBtn.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function loadAllowedRoles() {
        fetch('get_allowed_roles.php')
            .then(response => response.json())
            .then(data => {
                var roleSelect = document.getElementById("role");
                roleSelect.innerHTML = '';
                data.forEach(role => {
                    var option = document.createElement("option");
                    option.value = role.value;
                    option.textContent = role.text;
                    roleSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching roles:', error));
    }

    // Form submission and link generation
    document.getElementById("signupForm").onsubmit = function (event) {
        event.preventDefault();
        var role = document.getElementById("role").value;

        // Fetch the new inviter_id from the server
        fetch('generate_inviter_id.php')
            .then(response => response.json())
            .then(data => {
                var inviterId = data.inviter_id;
                var generatedLink = "http://localhost/i%20shield/dashboard/register.html?role=" + role + "&inviter_id=" + inviterId;
                document.getElementById("inviteLink").value = generatedLink;
            })
            .catch(error => console.error('Error generating link:', error));
    }

    // Copy link to clipboard
    document.getElementById("copyLinkBtn").onclick = async function () {
        try {
            const link = document.getElementById("inviteLink").value;
            await navigator.clipboard.writeText(link);
            alert("Link copied to clipboard!");
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    }

    // Share link (simple alert for demo purposes)
    document.getElementById("shareLinkBtn").onclick = function () {
        var link = document.getElementById("inviteLink").value;
        alert("Share this link: " + link);
    }
</script>
</body>

</html>