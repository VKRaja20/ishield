<?php
include 'header.php';

// Database connection
$db_host = 'localhost';
$db_name = 'ishield';
$db_user = 'root';
$db_pass = '';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// Updated query to include EmpRole
$sql = "
    SELECT r.username, r.empid, r.email, r.profile_picture, r.EmpRole, u.mobile 
    FROM register r 
    LEFT JOIN user_kyc u ON r.email = u.email 
    WHERE r.email = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = htmlspecialchars($row['username']);
    $empID = htmlspecialchars($row['empid']);
    $email = htmlspecialchars($row['email']);
    $mobile = htmlspecialchars($row['mobile']);
    $profilePicture = htmlspecialchars($row['profile_picture']) ?: 'images/avatar.png';
    $EmpRole = htmlspecialchars($row['EmpRole']); // Updated variable
    $_SESSION['profile_picture'] = $profilePicture;
} else {
    echo "User data not found!";
    exit();
}
$stmt->close();
$conn->close();
?>
<style>
    /* Profile Section */
    .profile {
            text-align: center;
            padding: 20px;
        }

        .profile img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .profile h2 {
            margin: 10px 0;
            font-size: 24px;
            color: #333;
        }

        .profile p {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }

        .profile form {
            margin-top: 20px;
        }

        .profile input[type="file"] {
            display: none;
        }

        .profile label {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            font-size: 16px;
        }

        .profile .preview {
            margin-top: 10px;
        }

        .profile .preview img {
            border-radius: 8px;
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        /* Breadcrumbs Styling */
        .breadcrumb {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb li {
            display: inline;
            margin-right: 5px;
        }

        .breadcrumb li a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb li i {
            margin: 0 5px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

</style>

<main>
    <div class="head-title">
        <div class="left">
            <h1>Dashboard</h1>
            <ul class="breadcrumb">
                <li><a href="index.html">Dashboard</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li><a class="active" href="profile.php">PROFILE</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="profile">
            <h2>Welcome, <?php echo $username; ?>!</h2>
            <img src="<?php echo $profilePicture; ?>" id="profilePic" alt="Profile Picture">
            <p>Employee ID: <?php echo $empID; ?></p>
            <p>Email: <?php echo $email; ?></p>
            <p>Mobile: <?php echo $mobile; ?></p>
            <p>Employee Role: <?php echo $EmpRole; ?></p>

            <form action="upload.php" method="post" enctype="multipart/form-data">
                <label for="profile_picture">Change Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                    onchange="previewImage(event)">
                <input type="submit" value="Upload">
            </form>
            <div class="preview">
                <img id="imagePreview" src="#" alt="Image Preview" style="display: none;" />
            </div>
        </div>
    </div>
</main>
</section>
<!-- CONTENT -->
<script src="script.js"></script>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
</script>
</body>
</html>
