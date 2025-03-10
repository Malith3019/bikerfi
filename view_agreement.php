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
$query = "SELECT full_name, email, nic, phone_number, image, agreement	 FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
	$full_name = $row['full_name'];
	$email = $row['email'];
	$nic = $row['nic'];
	$phone_number = $row['phone_number'];
	$agreement = $row['agreement'];
	$profile_image = $row['image'] ?: "assets/img/user.webp"; // Default profile image
} else {
	header("location:logout.php");
	exit();
}


?>
<?php
// Check if form is submitted and agreement checkbox is checked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['acceptTerms']) && $_POST['acceptTerms'] === 'Y') {
		// Update the agreement flag in the database for the current user
		$sql = "UPDATE users SET agreement = 'Y' WHERE id = $id";  // Directly embedding the id for simplicity

		if ($conn->query($sql)) {
			// Success - Redirect with success message in query string
			header('Location: view_agreement.php?message=Agreement accepted successfully!');
			exit();
		} else {
			// Error - Redirect with error message in query string
			header('Location: view_agreement.php?message=Error updating agreement status.');
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
			<li class="nav-item"><a class="nav-link collapsed" href="process.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>READ PROCESS</span></a>
			</li>
		</ul>
	</aside>


	<main id="main" class="main">
		<div class="pagetitle">
			<h1>AGREEMENT</h1>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.html">MENU</a></li>
					<li class="breadcrumb-item active">AGREEMENT</li>
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
									<h5 class="card-title">MENU > LOAN MANAGEMENT > AGREEMENT</h5>
									<?php
									// Display message if it is set in the query string
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
									<?php
									if ($agreement	 == 'Y') {
										// If the agreement is already 'Y', show a message instead of the form
										echo "<p class='fs-4 text-center'><strong>You have already agreed to the terms and conditions.</strong></p>";
										echo "<p class='fs-5 text-center'>Your payment plan and fund collection process will be informed via email.</p>";
									} else {
										// If the agreement is not 'Y', show the agreement form
									?>

										<form id="loanAgreementForm" action="" method="POST">
											<table class="table table-bordered custom-gray-table table-striped" id="loanDataTable">
												<thead class="thead-dark">
													<tr>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td colspan="2">
															<h4 class="text-center">Loan Agreement</h4>
														</td>
													</tr>
													<tr>
														<td colspan="2" class="agreement-text">
															By accepting this agreement, you acknowledge that:<br>
															1. You have reviewed and understood the loan terms, including the loan amount, interest rate, tenure, and repayment schedule.<br>
															2. You agree to make timely monthly payments as per the provided schedule.<br>
															3. Failure to make payments on time may result in penalties, legal actions, or negative credit impact.<br>
															4. The lender reserves the right to update terms in case of regulatory changes, with prior notice to you.<br>
															5. You authorize the lender to process and store your personal and financial data for loan processing and legal compliance.<br>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<div class="form-check">
																<input type="checkbox" id="acceptTerms" class="form-check-input" name="acceptTerms" value="Y" required>
																<label for="acceptTerms" class="form-check-label">I agree to the loan terms and repayment schedule.</label>
															</div>
														</td>
													</tr>
													<tr>
														<td colspan="2" class="text-center">
															<button class="btn btn-success" type="submit" id="acceptBtn">Accept & Proceed</button>
														</td>
													</tr>
												</tbody>
											</table>
										</form>

								 <?php } ?>

								 

								</div>
							</div>
						</div>
					</div>
				</div>
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