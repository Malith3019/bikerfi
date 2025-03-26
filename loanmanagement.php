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
			<li class="nav-item"><a class="nav-link " href="loanmanagement.php">
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
									<h5 class="card-title">MENU > LOAN MANAGEMENT</h5>
									<table class="table table-bordered custom-gray-table table-striped  " id="loanDataTable">
										<thead class="thead-dark">
											<tr>
												<th>Field</th>
												<th>Details</th>
											</tr>
										</thead>
										<tbody>
											<?php
											// Fetch loan request details
											$sql = "SELECT id, proof_added, customer_id, loan_amount, loan_tenure, purpose,
                age, marital_status, dependents, residential_status, time_at_employer,
                current_account, home_phone, work_phone, score, status, created_at, income_proof
                FROM loan_requests WHERE customer_id = '$id'";

											$result = mysqli_query($conn, $sql);

											if (mysqli_num_rows($result) > 0) {
												while ($row = mysqli_fetch_assoc($result)) {
													$loanId = htmlspecialchars($row['id']);
													$status = htmlspecialchars($row['status']);
													$proofAdded = htmlspecialchars($row['proof_added']);
													$score = htmlspecialchars($row['score']);
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

													// Loop through each field and display them as rows
													echo "<tr>";
													echo "<td><strong>Credit Score</strong></td><td>$score</td>";
													echo "</tr>";

													// Status row with conditional styling
													echo "<tr>";
													echo "<td><strong>Status</strong></td><td><b style='color: ";
													if ($status == 'Pending') {
														echo "orange";
													} elseif ($status == 'Rejected') {
														echo "red";
													} elseif ($status == 'Level One Approved') {
														echo "blue";
													} elseif ($status == 'Level Two Approved') {
														echo "green";
													} else {
														echo "black"; // Default color
													}
													echo ";'>$status</b></td>";
													echo "</tr>";

													// Proof status row
													echo "<tr>";
													echo "<td><strong>Next Process</strong></td><td>";
													if ($proofAdded == 'Y') {
														if ($status == 'Level One Approved') {
															echo "<span class='text-success'><b>Your request has passed to the next level of approval.</b></span>";
														} elseif ($status == 'Level Two Approved') {
															echo "<a href='view_agreement.php?loan_id=$loanId' class='btn btn-info btn-sm'>View Agreement</a>";
														} else {
															echo "<span class='text-muted'>No update</span>";
														}
													} else {
														if ($status == 'Pending') {
															echo "<a href='upload_proof.php?loan_id=$loanId' class='btn btn-primary btn-sm'>Submit Proof</a>";
														} elseif ($status == 'Rejected') {
															echo "<span class='text-danger'><b>Your request has been rejected.</b></span>";
														} else {
															echo "<span class='text-muted'>No update</span>";
														}
													}
													echo "</td>";
													echo "</tr>";


													// Display other details in a similar way
													echo "<tr><td><strong>Applied Date</strong></td><td>$createdAt</td></tr>";
													echo "<tr><td><strong>Loan Amount</strong></td><td>$loanAmount</td></tr>";
													echo "<tr><td><strong>Tenure (Months)</strong></td><td>$loanTenure</td></tr>";
													echo "<tr><td><strong>Purpose</strong></td><td>$purpose</td></tr>";
													echo "<tr><td><strong>Age</strong></td><td>$age</td></tr>";
													echo "<tr><td><strong>Marital Status</strong></td><td>$maritalStatus</td></tr>";
													echo "<tr><td><strong>Dependents</strong></td><td>$dependents</td></tr>";
													echo "<tr><td><strong>Residential Status</strong></td><td>$residentialStatus</td></tr>";
													echo "<tr><td><strong>Employment Time (Years)</strong></td><td>$timeAtEmployer</td></tr>";
													echo "<tr><td><strong>Current Account</strong></td><td>$currentAccount</td></tr>";
													echo "<tr><td><strong>Home Phone</strong></td><td>$homePhone</td></tr>";
													echo "<tr><td><strong>Work Phone</strong></td><td>$workPhone</td></tr>";

													// Display income proof document row
													echo "<tr><td><strong>Income Proof</strong></td><td>";
													if (!empty($incomeProof)) {
														echo "<a href='$incomeProof' target='_blank' onclick=\"window.open('$incomeProof', 'popup', 'width=600,height=400'); return false;\">View</a>";
													} else {
														echo "No document";
													}
													echo "</td></tr>";
												}
											} else {
												echo "<tr><td colspan='2' class='text-center'>No loan applications found.</td></tr>";
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