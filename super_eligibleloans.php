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



if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Get the form data
	$loanId = isset($_POST['loan_id']) ? intval($_POST['loan_id']) : 0;
	$status = isset($_POST['status']) ? $_POST['status'] : '';
	$remarks = isset($_POST['remarks']) ? mysqli_real_escape_string($conn, $_POST['remarks']) : '';

	// Validate inputs
	if ($loanId > 0 && ($status == "Level Two Approved" || $status == "Rejected")) {
		// Update the loan status in the database
		echo $sql = "UPDATE loan_requests SET status = '$status', remarks = '$remarks' WHERE id = $loanId";
		$result = mysqli_query($conn, $sql);


		include 'db_connection.php'; // Include your database connection file

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$loanId = isset($_POST['loan_id']) ? intval($_POST['loan_id']) : 0;
			$status = isset($_POST['status']) ? $_POST['status'] : '';
			$remarks = isset($_POST['remarks']) ? mysqli_real_escape_string($conn, $_POST['remarks']) : '';

			if ($loanId > 0 && ($status == "Level Two Approved" || $status == "Rejected")) {
				$sql = "UPDATE loan_requests SET status = '$status', remarks = '$remarks' WHERE id = $loanId";
				$result = mysqli_query($conn, $sql);

				if ($result) {
					// Redirect with success message
					header("Location: super_eligibleloans.php?message=Loan application status updated successfully&type=success");
					exit();
				} else {
					// Redirect with error message
					header("Location: super_eligibleloans.php?message=Error updating loan status&type=error");
					exit();
				}
			} else {
				// Redirect with validation error message
				header("Location: super_eligibleloans.php?message=Invalid input. Please try again.&type=error");
				exit();
			}
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
			<li class="nav-item"><a class="nav-link collapsed" href="super_admin_dashboard.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>PERSONAL INFORMATION</span></a>
			</li>
			<li class="nav-item"><a class="nav-link " href="super_eligibleloans.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>ELIGIBLE LOAN APPLICATIONS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_rejectedloans.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>REJECTED LOAN APPLICATIONS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_applications.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>LOAN REQUESTS AND PROOFS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_score_criteria.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>SCORE CRITERIA</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_interest.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>INTREST RATES</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_customers.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>CUSTOMERS</span></a>
			</li>
		</ul>


		<ul class="sidebar-nav" id="sidebar-nav">
			<li class="nav-heading">REPORTS</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_report1.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>LEVEL 1 APPROVED LIST </span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_report2.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>LEVEL 2 APPROVED LIST </span></a>
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

			<?php
			if (isset($_GET['message'])) {
				$message = htmlspecialchars($_GET['message']);
				echo "<p id='message' class='fs-4'><strong>$message</strong></p>";
			}
			?>
			<script>
				// Wait for the document to load
				window.onload = function() {
					// Set a timeout of 6 seconds (6000 milliseconds) to hide the message
					setTimeout(function() {
						var message = document.getElementById('message');
						if (message) {
							message.style.display = 'none'; // Hide the message
						}
					}, 6000); // 6000 milliseconds = 6 seconds
				}
			</script>


			<div class="row">
				<!-- Left side columns -->
				<div class="col-lg-12">
					<div class="row">
						<div class="col-12">
							<div class="card recent-sales overflow-auto">
								<div class="card-body">
									<h5 class="card-title">MENU > ELIGIBLE LOAN APPLICATIONS</h5>
									<table class="table table-bordered custom-gray-table table-striped datatable" id="loanDataTable">
										<thead class="thead-dark">
											<tr>
												<th>Credit Score</th>
												<th>Status</th>
												<th>Check Proof</th>
												<th>Next Process </th>
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
                                            FROM loan_requests WHERE status = 'Level One Approved'";

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

													// Status color
													$statusColor = 'black';
													if ($status == 'Pending') {
														$statusColor = 'orange';
													} elseif ($status == 'Rejected') {
														$statusColor = 'red';
													} elseif ($status == 'Level One Approved') {
														$statusColor = 'blue';
													} elseif ($status == 'Level Two Approved') {
														$statusColor = 'green';
													}
													echo "<td><b style='color: $statusColor;'>$status</b></td>";

													// View Proof & Approve Button
													echo "<td>";
													if ($proofAdded == 'Y') {
														echo "<a href='super_view_proof_documents.php?id=$customer_id' class='btn btn-warning btn-sm' target='_blank'>View Proof</a> ";
													} else {
														echo "<span class='text-muted'>No update</span>";
													}
													echo "</td>";


													echo "<td>";
													if ($proofAdded == 'Y') {
														echo "<button class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#approveModal' data-loanid='$loanId'>Approve</button>";
													} else {
														echo "<span class='text-muted'>No update</span>";
													}
													echo "</td>";

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
														echo "<td><a href='$incomeProof' target='_blank' onclick=\"window.open('$incomeProof', 'popup', 'width=800,height=600'); return false;\">View</a></td>";
													} else {
														echo "<td>No document</td>";
													}

													echo "</tr>";
												}
											} else {
												echo "<tr><td colspan='16' class='text-center'>No loan applications found.</td></tr>";
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

		<!-- Approve Loan Modal -->
		<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="approveModalLabel">Approve Loan Application</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="approveLoanForm" action="" method="POST">
							<input type="hidden" id="loanIdInput" name="loan_id">
							<div class="mb-3">
								<label for="approveStatus" class="form-label">Approval Status</label>
								<select class="form-control" id="approveStatus" name="status">
									<option value="Level Two Approved">Level Two Approved</option>
									<option value="Rejected">Rejected</option>
								</select>
							</div>
							<div class="mb-3">
								<label for="remarks" class="form-label">Remarks</label>
								<textarea class="form-control" id="remarks" name="remarks" rows="3" required></textarea>
							</div>
							<button type="submit" class="btn btn-primary">Submit </button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- JavaScript to handle modal data -->
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				var approveModal = document.getElementById('approveModal');
				approveModal.addEventListener('show.bs.modal', function(event) {
					var button = event.relatedTarget;
					var loanId = button.getAttribute('data-loanid');
					document.getElementById('loanIdInput').value = loanId;
				});
			});
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

</html>