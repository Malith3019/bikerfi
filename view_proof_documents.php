<?php
session_start();
include("connection.php"); // Include database connection
error_reporting(0);
// Check if user is logged in
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
	header("location:logout.php");
	exit();
}
if (isset($_GET['scoree'])) {
	$scoree = $_GET['scoree'];
	// echo "<h2>$scoree</h2>";
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
			<li class="nav-item"><a class="nav-link collapsed" href="admin_dashboard.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>PERSONAL INFORMATION</span></a>
			</li>
			<li class="nav-item"><a class="nav-link  collapsed" href="eligibleloans.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>ELIGIBLE LOAN APPLICATIONS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="rejectedloans.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>REJECTED LOAN APPLICATIONS</span></a>
			</li>

		</ul>
	</aside>


	<main id="main" class="main">
		<div class="pagetitle">
			<h1>LOAN MANAGEMENT</h1>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.html">MENU</a></li>
					<li class="breadcrumb-item active">LOAN MANAGEMENT</li>
				</ol>
			</nav>
		</div><!-- End Page Title -->

		<section class="section dashboard">
			<div class="row">
				<!-- Left side columns -->
				<div class="col-lg-12">
					<div class="row">
						<div class="col-12">
							<div class="card recent-sales overflow-auto">
								<div class="card-body">
									<h5 class="card-title">MENU > LOAN DOCUMENT PROOFS</h5>
									<table class="table table-bordered custom-gray-table table-striped datatable" id="loanProofsTable">
										<thead class="thead-dark">
											<tr>
												<th>Proof ID</th>
												<th>Customer ID</th>
												<th>Document</th>
												<th>Uploaded Date</th>
												<th>Last Updated</th>
											</tr>
										</thead>
										<tbody>
											<?php
											// Fetch proof documents from the database
											$sql = "SELECT id, customer_id, document, created_at, updated_at FROM loan_requests_proofs";
											$result = mysqli_query($conn, $sql);

											if (mysqli_num_rows($result) > 0) {
												while ($row = mysqli_fetch_assoc($result)) {
													$proofId = htmlspecialchars($row['id']);
													$customerId = htmlspecialchars($row['customer_id']);
													$documentPath = htmlspecialchars($row['document']);
													$createdAt = htmlspecialchars($row['created_at']);
													$updatedAt = htmlspecialchars($row['updated_at']);

													echo "<tr>";
													echo "<td>$proofId</td>";
													echo "<td>$customerId</td>";

													// Open document in a new browser window
													if (!empty($documentPath)) {
														echo "<td>
                                                        <a href='$documentPath' target='_blank' onclick=\"window.open('$documentPath', 'popup', 'width=800,height=600'); return false;\" class='btn btn-primary btn-sm'>
                                                  $documentPath  
															</a>
                                                      </td>";
													} else {
														echo "<td>No document</td>";
													}

													echo "<td>$createdAt</td>";
													echo "<td>$updatedAt</td>";
													echo "</tr>";
												}
											} else {
												echo "<tr><td colspan='5' class='text-center'>No proof documents found.</td></tr>";
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><!-- End Recent Sales -->
		</section>


	</main><!-- End #main -->
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