<?php
session_start();
include("connection.php"); // Include database connection

// Check if user is logged in
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
	header("location:logout.php");
	exit();
}

$id = $_SESSION['id'];

// Fetch user data from the database
$query = "SELECT full_name, email, nic, phone_number, image FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
	$full_name = $row['full_name'];
	$email = $row['email'];
	$nic = $row['nic'];
	$phone_number = $row['phone_number'];
	$profile_image = $row['image'] ?: "assets/img/user.webp"; // Default profile image
} else {
	header("location:logout.php");
	exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
	$new_full_name = $_POST['full_name'];
	$new_email = $_POST['email'];
	$new_nic = $_POST['nic'];
	$new_phone_number = $_POST['phone'];

	// Check if email or NIC already exists (excluding current user)
	$check_query = "SELECT id FROM users WHERE (email = ? OR nic = ?) AND id != ?";
	$check_stmt = mysqli_prepare($conn, $check_query);
	mysqli_stmt_bind_param($check_stmt, 'ssi', $new_email, $new_nic, $id);
	mysqli_stmt_execute($check_stmt);
	$check_result = mysqli_stmt_get_result($check_stmt);

	if (mysqli_num_rows($check_result) > 0) {
		header("Location: profile.php?error=Email or NIC already exists for another user.");
		exit();
	} else {
		// Handle file upload
		if (!empty($_FILES['profilePic']['name'])) {
			$target_dir = "uploads/profile_pictures/";
			$file_name = basename($_FILES["profilePic"]["name"]);
			$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			$new_filename = "user_" . $id . "." . $file_extension;
			$target_file = $target_dir . $new_filename;

			// Validate file type
			$allowed_extensions = ["jpg", "jpeg", "png", "webp"];
			if (!in_array($file_extension, $allowed_extensions)) {
				header("Location: profile.php?error=Only JPG, JPEG, PNG, and WEBP files are allowed.");
				exit();
			} elseif ($_FILES["profilePic"]["size"] > 15000000) { // 15MB max file size
				header("Location: profile.php?error=File size should be less than 15MB.");
				exit();
			} else {
				// Move the uploaded file
				if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
					$profile_image = $target_file;
				} else {
					header("Location: profile.php?error=Error uploading the image.");
					exit();
				}
			}
		}

		// Update user data
		$update_query = "UPDATE users SET full_name = ?, email = ?, nic = ?, phone_number = ?, image = ? WHERE id = ?";
		$update_stmt = mysqli_prepare($conn, $update_query);
		mysqli_stmt_bind_param($update_stmt, 'sssssi', $new_full_name, $new_email, $new_nic, $new_phone_number, $profile_image, $id);

		if (mysqli_stmt_execute($update_stmt)) {
			header("Location: profile.php?success=Profile updated successfully.");
			exit();
		} else {
			header("Location: profile.php?error=Error updating profile.");
			exit();
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title>BIKER FI</title>
	<meta content="" name="description">
	<meta content="" name="keywords">
	<link href="assets/img/logo2.png" rel="icon">
	<link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
	<link href="https://fonts.gstatic.com" rel="preconnect">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
	<link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
	<link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
	<link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
	<link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
	<link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
	<link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

	<header id="header" class="header fixed-top d-flex align-items-center">
		<div class="d-flex align-items-center justify-content-between">
			<a href="" class="logo d-flex align-items-center">
				<img src="assets/img/logo2.png" alt="">
				<span class="d-none d-lg-block">BIKER FI</span>
			</a>
			<i class="bi bi-list toggle-sidebar-btn"></i>
		</div>

		<nav class="header-nav ms-auto">
			<ul class="d-flex align-items-center">
				<li class="nav-item dropdown pe-3">
					<a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
						<img src="<?= $profile_image ?>" alt="Profile" class="rounded-circle" width="30" height="30">
					</a>

					<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
						<li class="dropdown-header">
							<h6><?= strtoupper($full_name); ?></h6>
						</li>
						<li>
							<hr class="dropdown-divider">
						</li>
						<li><a class="dropdown-item d-flex align-items-center" href="logout.php">
								<i class="bi bi-box-arrow-right"></i>
								<span>SIGN OUT</span></a>
						</li>
					</ul>
				</li>
			</ul>
		</nav>
	</header>

	<aside id="sidebar" class="sidebar">
		<ul class="sidebar-nav" id="sidebar-nav">
			<li class="nav-heading">MENU</li>
			<li class="nav-item"><a class="nav-link collapsed" href="status.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>MY STATUS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="profile.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>PERSONAL INFORMATION</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="applyforaloan.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>APPLY FOR A LOAN</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="loanmanagement.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>LOAN MANAGEMENT</span></a>
			</li>
			<li class="nav-item"><a class="nav-link " href="process.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>READ PROCESS</span></a>
			</li>

		</ul>
	</aside>

	<main id="main" class="main">
		<div class="pagetitle">
			<h1>PROCESS</h1>
		</div>

		<section class="section">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<h2 class="card-title">User Manual - Loan Application Process</h2>

							<div class="card p-4">
								<h4>Step 1: Self-Evaluation</h4>
								<p>Before applying, you will go through a self-evaluation using our Score Card. Based on your personal details, a credit score will be calculated.</p>

								<h4>Step 2: Submit Your Loan Application</h4>
								<p>After self-evaluation, you can proceed to submit your loan application. Your initial score will be calculated automatically.</p>

								<h4>Step 3: Upload Required Documents</h4>
								<p>To proceed with your loan application, you need to upload the necessary proof documents, such as identification, income proof, and address verification.</p>

								<h4>Step 4: Admin Review</h4>
								<p>Once submitted, your documents will be reviewed by an Admin. If your documents meet the criteria, your application status will be updated to <b>Level One Approved</b>.</p>

								<h4>Step 5: Super Admin Final Verification</h4>
								<p>After Admin approval, your application goes through a final verification by a Super Admin. If approved at this stage, your application status will be marked as <b>Level Two Approved</b>.</p>

								<h4>Step 6: View & Accept Agreement</h4>
								<p>Once Level Two Approval is granted, you will see a new page where you can <b>view the loan agreement</b>. You must review and accept the terms and conditions before proceeding.</p>

								<h4>Final Decision & Loan Disbursement</h4>
								<p>After accepting the agreement, you will receive final confirmation, and the loan disbursement process will begin. You will also be provided with detailed information about your monthly repayment schedule.</p>

								<p class="mt-3"><b>Note:</b> If your application is rejected at any stage, you may receive instructions to update or provide additional documents.</p>
							</div>

						</div>
					</div>
				</div>
			</div>
		</section>
	</main>

	<footer id="footer" class="footer">
		<div class="copyright">
			&copy; Copyright <strong><span>BIKER FI | TSD GROUP 10</span></strong>. All Rights Reserved
		</div>
	</footer>
	<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
	<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
	<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="assets/vendor/chart.js/chart.umd.js"></script>
	<script src="assets/vendor/echarts/echarts.min.js"></script>
	<script src="assets/vendor/quill/quill.js"></script>
	<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
	<script src="assets/vendor/tinymce/tinymce.min.js"></script>
	<script src="assets/vendor/php-email-form/validate.js"></script>
	<script src="assets/js/main.js"></script>
	<script src="assets/js/upper.js"></script>
</body>

</html>