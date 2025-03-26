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

 
// Fetch loan request status from the database
$query2 = "SELECT customer_id, status FROM loan_requests WHERE customer_id = ?";
$stmt2 = mysqli_prepare($conn, $query2);
mysqli_stmt_bind_param($stmt2, 'i', $id);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);

if ($row = mysqli_fetch_assoc($result2)) {
    $customer_id = $row['customer_id'];
    $status = $row['status'];
} else {
    $status = "No loan request found"; // Handle case when no loan request exists
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
<style>
	.large-badge {
		font-size: 1.5rem;
		padding: 0.8rem 1.5rem;
	}
</style>

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
			<li class="nav-item"><a class="nav-link " href="status.php">
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
			<li class="nav-item"><a class="nav-link collapsed" href="process.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>READ PROCESS</span></a>
			</li>

		</ul>
	</aside>

	<main id="main" class="main">
		<div class="pagetitle">
			<h1>STATUS</h1>
		</div>

		<section class="section">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<h2 class="card-title">Application Status</h2>


							<!-- Status Display Section -->
							<div class="row mb-3">
								<label class="col-sm-2 col-form-label" for="status">Current Status</label>
								<div class="col-sm-10">
									<?php
									// Assuming the status variable is coming from the database or session
 
									// Display different content based on the application status
									if ($status == 'Pending') {
										echo "<span class='badge bg-warning large-badge'>Pending</span>";
										echo "<p>Your application is still under review. Please wait for the next update.</p>";
									} elseif ($status == 'Level One Approved') {
										echo "<span class='badge bg-success large-badge'>Level One Approved</span>";
										echo "<p>Your application has been approved at Level One and is awaiting the next approval stage.</p>";
									} elseif ($status == 'Level Two Approved') {
										echo "<span class='badge bg-info large-badge'>Level Two Approved</span>";
										// echo "<p>Your application has been approved at Level Two. Funds will be transferred soon.</p>";
									} elseif ($status == 'Rejected') {
										echo "<span class='badge bg-danger large-badge'>Rejected</span>";
										echo "<p>Unfortunately, your application has been rejected. Please check the instructions for next steps.</p>";
									} else {
										echo "<span class='badge bg-secondary large-badge'>No Status</span>";
										echo "<p>No update available. Please contact support for more information.</p>";
									}
									?>
								</div>
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