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



<?php
if (isset($_POST['submit'])) {

	// Customer ID (Retrieve from session or database)
	$customer_id = $id; // Replace with actual customer ID logic

	// File upload directory
	$timestamp = date('Ymd_His'); // Format: YYYYMMDD_HHMMSS
	$uploadDir = 'uploads/user_' . $customer_id . '_' . $nic . '_' . $timestamp . '/';

	// Create directory if not exists
	if (!file_exists($uploadDir)) {
		mkdir($uploadDir, 0777, true);
	}

	// Initialize an array to store document insertions
	$documentsToInsert = [];

	// Process Payslips
	if (!empty($_FILES['payslip']['name'][0])) {
		foreach ($_FILES['payslip']['name'] as $key => $payslip) {
			$payslipName = $uploadDir . 'payslip_' . uniqid() . '.' . pathinfo($payslip, PATHINFO_EXTENSION);
			if (move_uploaded_file($_FILES['payslip']['tmp_name'][$key], $payslipName)) {
				$documentsToInsert[] = "('$customer_id', '$payslipName')";
			}
		}
	}

	// Process Bank Statements
	if (!empty($_FILES['bankStatement']['name'][0])) {
		foreach ($_FILES['bankStatement']['name'] as $key => $bankStatement) {
			$bankStatementName = $uploadDir . 'bank_statement_' . uniqid() . '.' . pathinfo($bankStatement, PATHINFO_EXTENSION);
			if (move_uploaded_file($_FILES['bankStatement']['tmp_name'][$key], $bankStatementName)) {
				$documentsToInsert[] = "('$customer_id', '$bankStatementName')";
			}
		}
	}

	// Process Service Letters
	if (!empty($_FILES['serviceLetter']['name'][0])) {
		foreach ($_FILES['serviceLetter']['name'] as $key => $serviceLetter) {
			$serviceLetterName = $uploadDir . 'service_letter_' . uniqid() . '.' . pathinfo($serviceLetter, PATHINFO_EXTENSION);
			if (move_uploaded_file($_FILES['serviceLetter']['tmp_name'][$key], $serviceLetterName)) {
				$documentsToInsert[] = "('$customer_id', '$serviceLetterName')";
			}
		}
	}

	// Insert into database if there are documents
	if (!empty($documentsToInsert)) {
		$query = "INSERT INTO loan_requests_proofs (customer_id, document) VALUES " . implode(", ", $documentsToInsert);

		if (mysqli_query($conn, $query)) {
			// Update loan_requests table to mark proof as added
			echo$updateQuery = "UPDATE loan_requests SET proof_added = 'Y' WHERE customer_id = '$customer_id'";
			mysqli_query($conn, $updateQuery);
 
 			header('Location: loanmanagement.php?success=Documents uploaded successfully');
			exit;
		} else {
			$errorMsg = urlencode("Error: " . mysqli_error($conn));
 			header("Location: loanmanagement.php?error=$errorMsg");
			exit;
		}
	} else {
 		header("Location: loanmanagement.php?error=No files uploaded.");
		exit;
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

		</ul>
	</aside>
	<main id="main" class="main">
		<div class="pagetitle">
			<h1>UPLOAD PROOFS</h1>
		</div>
		<section class="section">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<h2 class="card-title">Upload Documents</h2>
							<?php
							if (isset($_GET['error'])) {
								echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
							}
							if (isset($_GET['success'])) {
								echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['success']) . "</div>";
							}
							?>
							<section class="section">
								<div class="row">
									<div class="col-12">
										<div class="card">
											<div class="card-body">
												<h2 class="card-title">Upload Documents</h2>


												<!-- Display Success or Error Message -->
												<?php
												if (isset($_GET['success'])) {
													echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['success']) . "</div>";
												}
												if (isset($_GET['error'])) {
													echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
												}
												?>

												<form method="post" action="" enctype="multipart/form-data">
													<div class="row mb-3">
														<label class="col-sm-2 col-form-label" for="payslip">Payslip (Last 3 Months)</label>
														<div class="col-sm-10">
															<input type="file" id="payslip" name="payslip[]" class="form-control mt-2" multiple required>
														</div>
													</div>

													<div class="row mb-3">
														<label class="col-sm-2 col-form-label" for="bankStatement">Bank Statement (Last 3 Months)</label>
														<div class="col-sm-10">
															<input type="file" id="bankStatement" name="bankStatement[]" class="form-control mt-2" multiple required>
														</div>
													</div>

													<div class="row mb-3">
														<label class="col-sm-2 col-form-label" for="serviceLetter">Service Letter</label>
														<div class="col-sm-10">
															<input type="file" id="serviceLetter" name="serviceLetter[]" class="form-control mt-2" multiple required>
														</div>
													</div>

													<div class="row mb-3">
														<div class="col-sm-10 offset-sm-2">
															<button type="submit" name="submit" class="btn btn-primary btn-block">Upload Documents</button>
														</div>
													</div>
												</form>



											</div>
										</div>
									</div>
								</div>
							</section>

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