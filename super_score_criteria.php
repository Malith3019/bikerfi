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


// Handle score update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_score'])) {
	$score_id = $_POST['score_id'];
	$criterion = mysqli_real_escape_string($conn, $_POST['criterion']);
	$value_range = mysqli_real_escape_string($conn, $_POST['value_range']);
	$score = intval($_POST['score']);

	$sql = "UPDATE credit_score_criteria SET criterion='$criterion', value_range='$value_range', score=$score WHERE id=$score_id";
	$result = mysqli_query($conn, $sql);

	if ($result) {
		header("Location: super_score_criteria.php?message=Score updated successfully&type=success");
	} else {
		header("Location: super_score_criteria.php?message=Error updating score&type=error");
	}
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
			<li class="nav-item"><a class="nav-link collapsed" href="super_applications.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>LOAN REQUESTS AND PROOFS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link " href="super_score_criteria.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>SCORE CRITERIA</span></a>
			</li>
			<li class="nav-item"><a class="nav-link  collapsed" href="super_interest.php">
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
			<h1>SCORE CRITERIA</h1>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.html">MENU</a></li>
					<li class="breadcrumb-item active">SCORE CRITERIA</li>
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
									<h5 class="card-title">MENU > SCORE CRITERIA</h5>
									<table class="table table-bordered custom-gray-table table-striped datatable" id="creditScoreTable">
										<thead>
											<tr>
												<th>ID</th>
												<th>Criterion</th>
												<th>Value Range</th>
												<th>Score</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$sql = "SELECT id, criterion, value_range, score FROM credit_score_criteria";
											$result = mysqli_query($conn, $sql);
											while ($row = mysqli_fetch_assoc($result)) {
												echo "<tr>";
												echo "<td>{$row['id']}</td>";
												echo "<td>{$row['criterion']}</td>";
												echo "<td>{$row['value_range']}</td>";
												echo "<td>{$row['score']}</td>";
												echo "<td><button class='btn btn-primary btn-edit' data-id='{$row['id']}' data-criterion='{$row['criterion']}' data-value='{$row['value_range']}' data-score='{$row['score']}' data-bs-toggle='modal' data-bs-target='#editModal'>Edit</button></td>";
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
<!-- Edit Score Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Score</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form method="POST">
					<input type="hidden" name="score_id" id="scoreId">
					<div class="mb-3">
						<label for="criterion" class="form-label">Criterion</label>
						<input type="text" class="form-control" name="criterion" id="criterion" readonly>
					</div>
					<div class="mb-3">
						<label for="value_range" class="form-label">Value Range</label>
						<input type="text" class="form-control" name="value_range" id="valueRange" readonly>
					</div>
					<div class="mb-3">
						<label for="score" class="form-label">Score</label>
						<input type="number" class="form-control" name="score" id="score" required>
					</div>
					<button type="submit" name="update_score" class="btn btn-success">Update</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		const editButtons = document.querySelectorAll(".btn-edit");
		editButtons.forEach(button => {
			button.addEventListener("click", function() {
				document.getElementById("scoreId").value = this.getAttribute("data-id");
				document.getElementById("criterion").value = this.getAttribute("data-criterion");
				document.getElementById("valueRange").value = this.getAttribute("data-value");
				document.getElementById("score").value = this.getAttribute("data-score");
			});
		});
	});
</script>

</html>