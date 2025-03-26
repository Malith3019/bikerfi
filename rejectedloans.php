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
			<li class="nav-item"><a class="nav-link " href="rejectedloans.php">
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
									<h5 class="card-title">MENU >REJECTED LOAN APPLICATIONS</h5>
									<table class="table table-bordered custom-gray-table table-striped datatable" id="loanDataTable">
										<thead class="thead-dark">
											<tr>
												<th>Credit Score</th>
												<th>Status</th>
												<th>Next Process</th>
												<th>Applied Date</th>
												<th>Loan Amount</th>
												<th>Tenure (Months)</th>
												<th>Purpose</th>
												<th>Age</th>
												<th>Marital Status</th>
												<th>Dependents</th>
												<th>Residential Status</th>
												<th>Employment Time (Years)</th>
												<th>Current Account</th>
												<th>Home Phone</th>
												<th>Work Phone</th>
												<th>Income Proof</th>
											</tr>
										</thead>
										<tbody>
											<?php
											// Fetch loan request details
											$sql = "SELECT id, proof_added, customer_id, loan_amount, loan_tenure, purpose,
													age, marital_status, dependents, residential_status, time_at_employer,
													current_account, home_phone, work_phone, score, status, created_at, income_proof
													FROM loan_requests where status = 'Rejected'";

											$result = mysqli_query($conn, $sql);

											if (mysqli_num_rows($result) > 0) {
												while ($row = mysqli_fetch_assoc($result)) {
													$loanId = htmlspecialchars($row['id']);
													$status = htmlspecialchars($row['status']);
													$proofAdded = htmlspecialchars($row['proof_added']);
													$score = htmlspecialchars($row['score']);
													$customer_id = htmlspecialchars($row['customer_id']);
													$createdAt = htmlspecialchars($row['created_at']);
													$loanAmount = htmlspecialchars($row['loan_amount']);
													$loanTenure = htmlspecialchars($row['loan_tenure']);
													$purpose = htmlspecialchars($row['purpose']);
													$age = htmlspecialchars($row['age']);
													$maritalStatus = htmlspecialchars($row['marital_status']);
													$dependents = htmlspecialchars($row['dependents']);
													$residentialStatus = htmlspecialchars($row['residential_status']);
													$timeAtEmployer = htmlspecialchars($row['time_at_employer']);
													$currentAccount = htmlspecialchars($row['current_account']);
													$homePhone = htmlspecialchars($row['home_phone']);
													$workPhone = htmlspecialchars($row['work_phone']);
													$incomeProof = htmlspecialchars($row['income_proof']);

													echo "<tr>";
													echo "<td>$score</td>";
													// Determine status color
													$statusColor = 'black'; // Default color

													if ($status == 'Pending') {
														$statusColor = 'orange'; // Changed from green to orange for better visibility
													} elseif ($status == 'Rejected') {
														$statusColor = 'red';
													} elseif ($status == 'Approved') {
														$statusColor = 'blue';
													} elseif ($status == 'Completed') {
														$statusColor = 'green';
													}

													echo "<td><b style='color: $statusColor;'>$status</b></td>";

													// Display the correct proof submission status
													if ($proofAdded == 'Y') {
														echo "<td>
																<a href='view_proof_documents.php?id=" . $customer_id . "' class='btn btn-warning btn-sm'>
																	View Proof
																</a>
															</td>";
													} else {
														echo "<td><span class='text-muted'>No update</span></td>"; // Default case for unknown statuses
													}


													echo "<td>$createdAt</td>";
													echo "<td>$loanAmount</td>";
													echo "<td>$loanTenure</td>";
													echo "<td>$purpose</td>";
													echo "<td>$age</td>";
													echo "<td>$maritalStatus</td>";
													echo "<td>$dependents</td>";
													echo "<td>$residentialStatus</td>";
													echo "<td>$timeAtEmployer</td>";
													echo "<td>$currentAccount</td>";
													echo "<td>$homePhone</td>";
													echo "<td>$workPhone</td>";

													// Display income proof document
													if (!empty($incomeProof)) {
														echo "<td><a href='$incomeProof' target='_blank' onclick=\"window.open('$incomeProof', 'popup', 'width=600,height=400'); return false;\">View</a></td>";
													} else {
														echo "<td>No document</td>";
													}

													echo "</tr>";
												}
											} else {
												echo "<tr><td colspan='15' class='text-center'>No loan applications found.</td></tr>";
											}
											?>

										</tbody>

									</table>
								</div>
							</div>
						</div>

					</div>
					<?php
					oci_free_statement($stmt);
					?>
				</div>
			</div><!-- End Recent Sales -->
			</div>
			</div><!-- End Left side columns -->
			<!-- Right side columns -->
			</div>
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