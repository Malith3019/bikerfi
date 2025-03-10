<?php
session_start();
include("connection.php"); // Include database connection
error_reporting(0);
// Check if user is logged in
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
	header("location:logout.php");
	exit();
}
// if (isset($_GET['scoree'])) {
// 	$message = $_GET['message'];
// 	// echo "<h2>$scoree</h2>";
// }
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
			<li class="nav-item"><a class="nav-link collapsed" href="super_admin_dashboard.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>PERSONAL INFORMATION</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_eligibleloans.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>ELIGIBLE LOAN APPLICATIONS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_rejectedloans.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>REJECTED LOAN APPLICATIONS</span></a>
			</li>

			<li class="nav-item"><a class="nav-link " href="super_applications.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>LOAN REQUESTS AND PROOFS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_score_criteria.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>SCORE CRITERIA</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_customers.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>CUSTOMERS</span></a>
			</li>



		</ul>
	</aside>


	<main id="main" class="main">
		<div class="pagetitle">
			<h1>LOAN REQUESTS AND PROOFS</h1>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.html">MENU</a></li>
					<li class="breadcrumb-item active">LOAN REQUESTS AND PROOFS</li>
				</ol>
			</nav>
		</div><!-- End Page Title -->

		<section class="section dashboard">
			<div class="row">
				<div class="col-lg-12">
					<div class="row">
						<div class="col-12">
							<div class="card recent-sales overflow-auto">
								<div class="card-body">
									<h5 class="card-title">MENU > LOAN REQUESTS AND PROOFS</h5>
									<table class="table table-bordered custom-gray-table table-striped datatable" id="loanRequestsTable">
										<thead>
											<tr>
												<th>Loan ID</th>
												<th>Customer ID</th>
												<th>Proof Added</th>
												<th>Loan Amount</th>
												<th>Loan Tenure</th>
												<th>Purpose</th>
												<th>Age</th>
												<th>Marital Status</th>
												<th>Dependents</th>
												<th>Residential Status</th>
												<th>Time at Address</th>
												<th>Occupation</th>
												<th>Time at Employer</th>
												<th>Current Account</th>
												<th>Home Phone</th>
												<th>Work Phone</th>
												<th>Income Proof</th>
												<th>Score</th>
												<th>Loan Status</th>
												<th>Created At (Loan)</th>
												<th>Flag</th>
												<th>Remarks</th>
												<th>Documents</th>
												<th>Proof Created At</th>
												<th>Proof Updated At</th>
											</tr>
										</thead>
										<tbody>
											<?php
											// Update the SQL query to join the two tables on customer_id and group the proofs by loan_id
											$sql = "SELECT 
                                                lr.id AS loan_id, 
                                                lr.customer_id, 
                                                lr.proof_added, 
                                                lr.loan_amount, 
                                                lr.loan_tenure, 
                                                lr.purpose, 
                                                lr.age, 
                                                lr.marital_status, 
                                                lr.dependents, 
                                                lr.residential_status, 
                                                lr.time_at_address, 
                                                lr.occupation, 
                                                lr.time_at_employer, 
                                                lr.current_account, 
                                                lr.home_phone, 
                                                lr.work_phone, 
                                                lr.income_proof, 
                                                lr.score, 
                                                lr.status AS loan_status, 
                                                lr.created_at AS loan_created_at, 
                                                lr.flag, 
                                                lr.remarks, 
                                                GROUP_CONCAT(lrp.document ORDER BY lrp.created_at) AS documents, 
                                                MAX(lrp.created_at) AS proof_created_at, 
                                                MAX(lrp.updated_at) AS proof_updated_at
                                            FROM 
                                                loan_requests lr
                                            LEFT JOIN 
                                                loan_requests_proofs lrp ON lr.customer_id = lrp.customer_id
                                            GROUP BY 
                                                lr.id";
											$result = mysqli_query($conn, $sql);
											while ($row = mysqli_fetch_assoc($result)) {
												// Output each loan request and concatenate proofs in one row
												echo "<tr>";
												echo "<td>{$row['loan_id']}</td>";
												echo "<td>{$row['customer_id']}</td>";
												echo "<td>{$row['proof_added']}</td>";
												echo "<td>{$row['loan_amount']}</td>";
												echo "<td>{$row['loan_tenure']}</td>";
												echo "<td>{$row['purpose']}</td>";
												echo "<td>{$row['age']}</td>";
												echo "<td>{$row['marital_status']}</td>";
												echo "<td>{$row['dependents']}</td>";
												echo "<td>{$row['residential_status']}</td>";
												echo "<td>{$row['time_at_address']}</td>";
												echo "<td>{$row['occupation']}</td>";
												echo "<td>{$row['time_at_employer']}</td>";
												echo "<td>{$row['current_account']}</td>";
												echo "<td>{$row['home_phone']}</td>";
												echo "<td>{$row['work_phone']}</td>";
												echo "<td><button class='btn btn-info' onclick='openDocument(\"{$row['income_proof']}\")'>" . basename($row['income_proof']) . "</button></td>";
												echo "<td>{$row['score']}</td>";
												echo "<td>{$row['loan_status']}</td>";
												echo "<td>{$row['loan_created_at']}</td>";
												echo "<td>{$row['flag']}</td>";
												echo "<td>{$row['remarks']}</td>";

												// Display the concatenated document links with file name instead of 'View Document'
												$documents = explode(",", $row['documents']);
												echo "<td>";
												foreach ($documents as $doc) {
													// Extract file name from the document path
													$fileName = basename($doc); // Get file name from the path
													echo "<button class='btn btn-info' onclick='openDocument(\"$doc\")'>" . htmlspecialchars($fileName) . "</button><br>";
												}
												echo "</td>";

												echo "<td>{$row['proof_created_at']}</td>";
												echo "<td>{$row['proof_updated_at']}</td>";
												echo "</tr>";
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

		<script>
			// JavaScript function to open the document in a new window
			function openDocument(documentUrl) {
				window.open(documentUrl, '_blank');
			}
		</script>
	 
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
<script>
	// JavaScript function to open the document in a new window
	function openDocument(documentUrl) {
		window.open(documentUrl, '_blank');
	}
</script>

</html>