<?php
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

    // Example: Display collected data
    echo "<h1>Form Submission Details</h1>";
    echo "<p>First Name: $firstName</p>";
    echo "<p>Last Name: $lastName</p>";
    echo "<p>Gender: $gender</p>";
    echo "<p>Address Line 1: $address1</p>";
    echo "<p>Address Line 2: $address2</p>";
    echo "<p>City: $city</p>";
    echo "<p>State/Province/Region: $state</p>";
    echo "<p>Country of Residence: $countryOfResidence</p>";
    echo "<p>Zip/Postal Code: $zip</p>";
    echo "<p>Mobile: $mobile</p>";
    echo "<p>Email: $email</p>";
    echo "<p>Date Of Birth: $dateOfBirth</p>";
    echo "<p>ID Type: $idType</p>";
    echo "<p>ID Number: $idNumber</p>";
    echo "<p>Terms Agreed: " . ($termsAgree ? 'Yes' : 'No') . "</p>";
} else {
    // Display the form if not submitted
    ?>
  <?php include 'header.php'; ?>
  <style>
      /* kyc */
      * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 800px;
                margin: 20px auto;
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .kyc-form {
                max-width: 600px;
                margin: auto;
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .form-section {
                margin-bottom: 20px;
            }
            .form-section h2 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
            input[type="text"], input[type="date"], select, textarea {
                width: 100%;
                padding: 8px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 14px;
            }
            .radio-group {
                margin-top: 5px;
            }
            .radio-group label {
                margin-right: 20px;
            }
            input[type="file"] {
                margin-top: 5px;
                font-size: 14px;
            }
            button[type="submit"] {
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
            }
            button[type="submit"]:hover {
                background-color: #45a049;
            }
  </style>

            <!-- MAIN -->
            <main>
                <div class="head-title">
                    <div class="left">
                        <h1>Dashboard</h1>
                        <ul class="breadcrumb">
                            <li>
                                <a href="#">Dashboard</a>
                            </li>
                            <li><i class='bx bx-chevron-right'></i></li>
                            <li>
                                <a class="active" href="KYC.html">KYC</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="container">
                    <form action="kyc.php" method="post" enctype="multipart/form-data">
                        <!-- Personal Information Section -->
                        <div class="form-section" id="personalInformation">
                            <h2>Personal Information</h2>
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <div class="radio-group">
                                    <label>
                                        <input type="radio" name="gender" value="male" required> Male
                                    </label>
                                    <label>
                                        <input type="radio" name="gender" value="female" required> Female
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address1">Address Line 1</label>
                                <input type="text" id="address1" name="address1" required>
                            </div>
                            <div class="form-group">
                                <label for="address2">Address Line 2</label>
                                <input type="text" id="address2" name="address2">
                            </div>
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State/Province/Region</label>
                                <input type="text" id="state" name="state" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Country of Residence</label>
                                <select id="country" name="countryOfResidence" required>
                                    <option value="">Select Country</option>
                                    <option value="India">India</option>
                                    <!-- Add more countries as needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="zip">Zip/Postal Code</label>
                                <input type="text" id="zip" name="zip" required>
                            </div>
                            <div class="form-group">
                                <label for="mobile">Mobile (With Country Code)</label>
                                <input type="text" id="mobile" name="mobile" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="dateOfBirth">Date Of Birth</label>
                                <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                            </div>
                        </div>

                        <!-- Required Documents and Terms Section -->
                        <div class="form-section" id="requiredDocuments">
                            <h2>Required Documents and Terms</h2>
                            <div class="form-group">
                                <label for="idType">ID Type</label>
                                <select id="idType" name="idType" required>
                                    <option value="">Select ID Type</option>
                                    <option value="Voter ID">Voter ID</option>
                                    <option value="Aadhar Card">Aadhar Card</option>
                                    <option value="Pan Card">Pan Card</option>
                                    <!-- Add more ID types as needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="idNumber">ID Number</label>
                                <input type="text" id="idNumber" name="idNumber" required>
                            </div>
                            <div class="form-group">
                                <label for="idFront">ID Card Front (Upload)</label>
                                <input type="file" id="idFront" name="idFront" accept=".jpg,.jpeg,.png,.gif" required>
                            </div>
                            <div class="form-group">
                                <label for="idBack">ID Card Back (Upload)</label>
                                <input type="file" id="idBack" name="idBack" accept=".jpg,.jpeg,.png,.gif" required>
                            </div>
                            <div class="form-group">
                                <label for="termsAgree">
                                    <input type="checkbox" id="termsAgree" name="termsAgree" required>
                                    I agree to the Terms and Conditions
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </main>
            <!-- MAIN -->
        </section>
        <!-- CONTENT -->
        <script src="script.js"></script>
    </body>
    </html>
    <?php
}
?>
