<?php
// Database connection (replace with your actual credentials)
include_once("connection.php");
session_start();

$msg1 = $_GET['msg'] ?? ""; // Get message from URL if available

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input from form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $nic = mysqli_real_escape_string($conn, $_POST['nic']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

    // Check if the password and confirm password match
    if ($password !== $confirmPassword) {
        $msg1 = "Passwords do not match!";
        header("Location: register.php?msg=$msg1");
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the NIC exists in the `nic`
    $checkNICQuery = "SELECT * FROM nic WHERE nic = '$nic'";
    $resultNIC = mysqli_query($conn, $checkNICQuery);

    if (mysqli_num_rows($resultNIC) === 0) {
        // NIC is not found in the `nic`
        $msg1 = "Your NIC is not government-approved. Please provide a valid NIC and retry registering.";
        header("Location: register.php?msg=$msg1");
        exit;
    }

    // Check if email already exists in the database
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $resultEmail = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($resultEmail) > 0) {
        // Email already exists
        $msg1 = "Email is already taken!";
        header("Location: register.php?msg=$msg1");
        exit;
    }

    // Check if NIC already exists in the database
    $checkExistingNICQuery = "SELECT * FROM users WHERE nic = '$nic'";
    $resultExistingNIC = mysqli_query($conn, $checkExistingNICQuery);

    if (mysqli_num_rows($resultExistingNIC) > 0) {
        // NIC already exists
        $msg1 = "NIC is already registered!";
        header("Location: register.php?msg=$msg1");
        exit;
    }

    // If everything is fine, insert the new user into the database
    $insertQuery = "INSERT INTO users (full_name, email, phone_number, nic, password, role) 
                    VALUES ('$name', '$email', '$phone', '$nic', '$hashedPassword', 'C')";

    if (mysqli_query($conn, $insertQuery)) {
        // Success message
        $msg2 = "Registration successful! Please log in.";
        header("Location: index.php?msg2=$msg2");
        exit;
    } else {
        // Error message if the query fails
        $msg1 = "Error in registration! Please try again.";
        header("Location: register.php?msg=$msg1");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>BIKER FI - Register</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="index.html" class="logo d-flex align-items-center w-auto">
                                    <span class="d-none d-lg-block">BIKER FI <img src="assets/img/logo2.png" alt=""></span>
                                </a>
                            </div><!-- End Logo -->

                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Register Your Account</h5>
                                        <p style="color: crimson" id="alert"><strong><?= $msg1; ?></strong></p>
                                    </div>

                                    <form class="row g-3 needs-validation" method="post" action="register.php" novalidate>

                                        <div class="col-12">
                                            <label for="yourName" class="form-label">Full Name</label>
                                            <input type="text" name="name" class="form-control" id="yourName" required>
                                            <div class="invalid-feedback">Please enter your full name.</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourEmail" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="yourEmail" required>
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPhone" class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control" id="yourPhone" required>
                                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourNIC" class="form-label">NIC</label>
                                            <input type="text" name="nic" class="form-control" id="yourNIC" required>
                                            <div class="invalid-feedback">Please enter a NIC.</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                                            <div class="invalid-feedback">Please enter your password.</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                                            <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" required>
                                            <div class="invalid-feedback">Please confirm your password.</div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Register</button>
                                        </div>

                                    </form>

                                </div>
                            </div>

                            <div class="credits">
                                <a href="index.php">Already have an account? Login Here</a>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/upper.js"></script>
    <script src="assets/js/jquery-2.1.4.min.js"></script>

</body>

</html>
