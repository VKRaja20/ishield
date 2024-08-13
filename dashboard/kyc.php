<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $gender = htmlspecialchars($_POST['gender']);
    $address1 = htmlspecialchars($_POST['address1']);
    $address2 = htmlspecialchars($_POST['address2']);
    $city = htmlspecialchars($_POST['city']);
    $state = htmlspecialchars($_POST['state']);
    $countryOfResidence = htmlspecialchars($_POST['countryOfResidence']);
    $zip = htmlspecialchars($_POST['zip']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $email = htmlspecialchars($_POST['email']);
    $dateOfBirth = htmlspecialchars($_POST['dateOfBirth']);
    $idType = htmlspecialchars($_POST['idType']);
    $idNumber = htmlspecialchars($_POST['idNumber']);
    $termsAgree = isset($_POST['termsAgree']) ? true : false;

    // Handle file uploads
    $idFrontPath = '';
    $idBackPath = '';

    if (isset($_FILES['idFront']) && $_FILES['idFront']['error'] == UPLOAD_ERR_OK) {
        $idFrontTmp = $_FILES['idFront']['tmp_name'];
        $idFrontName = $_FILES['idFront']['name'];
        $idFrontPath = 'uploads/' . basename($idFrontName);
        move_uploaded_file($idFrontTmp, $idFrontPath);
    }

    if (isset($_FILES['idBack']) && $_FILES['idBack']['error'] == UPLOAD_ERR_OK) {
        $idBackTmp = $_FILES['idBack']['tmp_name'];
        $idBackName = $_FILES['idBack']['name'];
        $idBackPath = 'uploads/' . basename($idBackName);
        move_uploaded_file($idBackTmp, $idBackPath);
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO user_kyc (firstName, lastName, gender, address1, address2, city, state, countryOfResidence, zip, mobile, email, dateOfBirth, idType, idNumber, idFrontPath, idBackPath, termsAgree) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssssb", $firstName, $lastName, $gender, $address1, $address2, $city, $state, $countryOfResidence, $zip, $mobile, $email, $dateOfBirth, $idType, $idNumber, $idFrontPath, $idBackPath, $termsAgree);
    
    if ($stmt->execute()) {
        echo "KYC data submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
