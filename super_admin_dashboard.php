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
		header("Location: admin_dashboard.php?error=Email or NIC already exists for another user.");
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
				header("Location: admin_dashboard.php?error=Only JPG, JPEG, PNG, and WEBP files are allowed.");
				exit();
			} elseif ($_FILES["profilePic"]["size"] > 15000000) { // 15MB max file size
				header("Location: admin_dashboard.php?error=File size should be less than 15MB.");
				exit();
			} else {
				// Move the uploaded file
				if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
					$profile_image = $target_file;
				} else {
					header("Location: admin_dashboard.php?error=Error uploading the image.");
					exit();
				}
			}
		}

		// Update user data
		$update_query = "UPDATE users SET full_name = ?, email = ?, nic = ?, phone_number = ?, image = ? WHERE id = ?";
		$update_stmt = mysqli_prepare($conn, $update_query);
		mysqli_stmt_bind_param($update_stmt, 'sssssi', $new_full_name, $new_email, $new_nic, $new_phone_number, $profile_image, $id);

		if (mysqli_stmt_execute($update_stmt)) {
			header("Location: admin_dashboard.php?success=Profile updated successfully.");
			exit();
		} else {
			header("Location: admin_dashboard.php?error=Error updating profile.");
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
			<li class="nav-item"><a class="nav-link" href="super_admin_dashboard.php">
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
			<h1>SUPER ADMIN PROFILE</h1>
		</div>

		<section class="section">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<h2 class="card-title">Super Admin Profile Information</h2>
							<?php
							if (isset($_GET['error'])) {
								echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
							}
							if (isset($_GET['success'])) {
								echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['success']) . "</div>";
							}
							?>

							<form method="post" action="" enctype="multipart/form-data">
								<div class="row mb-3">
									<label class="col-sm-2 col-form-label" for="name">Full Name</label>
									<div class="col-sm-10">
										<input type="text" id="name" name="full_name" class="form-control" value="<?= $full_name; ?>" required>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-sm-2 col-form-label" for="email">Email</label>
									<div class="col-sm-10">
										<input type="email" id="email" name="email" class="form-control" value="<?= $email; ?>" required>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-sm-2 col-form-label" for="nic">NIC</label>
									<div class="col-sm-10">
										<input type="text" id="nic" name="nic" class="form-control" value="<?= $nic; ?>" required>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-sm-2 col-form-label" for="phone">Phone Number</label>
									<div class="col-sm-10">
										<input type="text" id="phone" name="phone" class="form-control" value="<?= $phone_number; ?>" required>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-sm-2 col-form-label" for="profilePic">Profile Picture</label>
									<div class="col-sm-10">
										<img src="<?= $profile_image ?>" alt="Profile" class="rounded-circle" width="100" height="100">
										<br>
										<input type="file" id="profilePic" name="profilePic" class="form-control mt-2">
									</div>
								</div>



								<div class="row mb-3">
									<div class="col-sm-10 offset-sm-2">
										<button type="submit" name="submit" class="btn btn-primary btn-block">Save Changes</button>
									</div>
								</div>
							</form>
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