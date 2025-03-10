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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_loan'])) {
	// Collect form data
	$loan_amount = $_POST['loan_amount'];
	$loan_tenure = $_POST['loan_tenure'];
	$purpose = $_POST['purpose'];
	$age_range = $_POST['age'];  // The age is now a range (e.g., "18-<21")
	$marital_status = $_POST['marital_status'];
	$dependents = $_POST['dependents'];
	$residential_status = $_POST['residential_status'];
	$time_at_address = $_POST['time_at_address'];
	$occupation = $_POST['occupation'];
	$time_at_employer = $_POST['time_at_employer'];
	$current_account = $_POST['current_account'];
	$home_phone = $_POST['home_phone'];
	$work_phone = $_POST['work_phone'];
	$income_proof = $_FILES['income_proof']['name']; // assuming file is uploaded

	$credit_score = 0;

	// Query the database to get the criteria dynamically
	$query = "SELECT * FROM credit_score_criteria";
	$result = mysqli_query($conn, $query);

	// Check if the query was successful
	if (!$result) {
		die('Error querying database: ' . mysqli_error($conn));
	}

	// Store criteria in an associative array for easy lookup
	$score_criteria = [];
	while ($row = mysqli_fetch_assoc($result)) {
		// Map data with criterion as key
		$score_criteria[$row['criterion']][] = $row;
	}

	// Check if the user selected an age, if not, apply a default score (incomplete age)
	if (isset($score_criteria['Age'])) {
		if (empty($age_range)) {  // If age is not selected or is empty
			// Find the incomplete age score in the table and add it to the credit score
			foreach ($score_criteria['Age'] as $age_range_db) {
				if ($age_range_db['value_range'] == 'Incomplete') {  // Assuming 'Incomplete' range exists
					$credit_score += $age_range_db['score']; // Add the incomplete score
					break;
				}
			}
		} else {  // If age is selected, match the range and calculate score
			foreach ($score_criteria['Age'] as $age_range_db) {
				if ($age_range == $age_range_db['value_range']) {
					$credit_score += $age_range_db['score']; // Add score based on age range
					break; // Once a matching range is found, stop the loop
				}
			}
		}
	}

	// Calculate marital status score
	if (isset($score_criteria['Marital Status']) && !empty($marital_status)) {
		foreach ($score_criteria['Marital Status'] as $status) {
			if ($status['value_range'] == $marital_status) {
				$credit_score += $status['score'];
				break;
			}
		}
	} else {
		// If marital status is not selected, apply incomplete score
		foreach ($score_criteria['Marital Status'] as $status) {
			if ($status['value_range'] == 'Incomplete') {
				$credit_score += $status['score'];
				break;
			}
		}
	}

	// Calculate Dependents score
	if (isset($score_criteria['Number of Dependents'])) {
		if ($dependents !== '') { // Ensure 0 is treated as valid and only empty values are incomplete
			foreach ($score_criteria['Number of Dependents'] as $dependent_range) {
				if ($dependent_range['value_range'] == $dependents) {
					$credit_score += $dependent_range['score'];
					break;
				}
			}
		} else {
			// Dependents is empty (not selected), apply incomplete score
			foreach ($score_criteria['Number of Dependents'] as $dependent_range) {
				if ($dependent_range['value_range'] == 'Incomplete') {
					$credit_score += $dependent_range['score'];
					break;
				}
			}
		}
	}

	// Calculate Residential Status score
	if (isset($score_criteria['Residential Status']) && !empty($residential_status)) {
		// Residential Status selected, proceed with normal scoring
		foreach ($score_criteria['Residential Status'] as $residential_option) {
			if ($residential_option['value_range'] == $residential_status) {
				echo $credit_score += $residential_option['score'];
				break;
			}
		}
	} else {
		// Residential Status is empty (not selected), apply incomplete score
		foreach ($score_criteria['Residential Status'] as $residential_option) {
			if ($residential_option['value_range'] == 'Incomplete') {
				$credit_score += $residential_option['score'];
				break;
			}
		}
	}

	// Calculate Time at Address score
	if (isset($score_criteria['Time at Address']) && !empty($time_at_address)) {
		// Time at Address selected, proceed with normal scoring
		foreach ($score_criteria['Time at Address'] as $address_range) {
			if ($address_range['value_range'] == $time_at_address) {
				$credit_score += $address_range['score'];
				break;
			}
		}
	} else {
		// Time at Address is empty (not selected), apply incomplete score
		foreach ($score_criteria['Time at Address'] as $address_range) {
			if ($address_range['value_range'] == 'Incomplete') {
				$credit_score += $address_range['score'];
				break;
			}
		}
	}

	// Calculate Occupation score
	if (isset($score_criteria['Occupation']) && !empty($occupation)) {
		// Occupation selected, proceed with normal scoring
		foreach ($score_criteria['Occupation'] as $occupation_option) {
			if ($occupation_option['value_range'] == $occupation) {
				$credit_score += $occupation_option['score'];
				break;
			}
		}
	} else {
		// Occupation is empty (not selected), apply incomplete score
		foreach ($score_criteria['Occupation'] as $occupation_option) {
			if ($occupation_option['value_range'] == 'Incomplete') {
				$credit_score += $occupation_option['score'];
				break;
			}
		}
	}

	// Calculate Time at Employer score
	if (isset($score_criteria['Time at Employer']) && !empty($time_at_employer)) {
		// Time at Employer selected, proceed with normal scoring
		foreach ($score_criteria['Time at Employer'] as $employer_range) {
			if ($employer_range['value_range'] == $time_at_employer) {
				$credit_score += $employer_range['score'];
				break;
			}
		}
	} else {
		// Time at Employer is empty (not selected), apply incomplete score
		foreach ($score_criteria['Time at Employer'] as $employer_range) {
			if ($employer_range['value_range'] == 'Incomplete') {
				$credit_score += $employer_range['score'];
				break;
			}
		}
	}

	// Calculate Current Account score
	if (isset($score_criteria['Current A/C']) && !empty($current_account)) {
		// Current Account selected, proceed with normal scoring
		foreach ($score_criteria['Current A/C'] as $account_status) {
			if ($account_status['value_range'] == $current_account) {
				$credit_score += $account_status['score'];
				break;
			}
		}
	} else {
		// Current Account is empty (not selected), apply incomplete score
		foreach ($score_criteria['Current A/C'] as $account_status) {
			if ($account_status['value_range'] == 'Incomplete') {
				$credit_score += $account_status['score'];
				break;
			}
		}
	}


	// Calculate Telephone score (Home & Work)
	if (isset($score_criteria['Telephone'])) {
		$has_home_phone = !empty($home_phone);
		$has_work_phone = !empty($work_phone);

		foreach ($score_criteria['Telephone'] as $phone_criteria) {
			if ($has_home_phone && $has_work_phone) {
				// Both numbers provided → Add this score
				$credit_score += $phone_criteria['score'];
				break;
			} elseif ($has_home_phone || $has_work_phone) {
				// Only one number provided → Add this score
				$credit_score += $phone_criteria['score'];
				break;
			}
		}

		// If both fields are empty, apply the "Incomplete" score
		if (!$has_home_phone && !$has_work_phone) {
			foreach ($score_criteria['Telephone'] as $phone_criteria) {
				if ($phone_criteria['value_range'] == 'Incomplete') {
					$credit_score += $phone_criteria['score'];
					break;
				}
			}
		}
	}

	// Output final credit score for debugging
	// $score = "Final Credit Score: $credit_score\n";
	// Check if the credit score is greater than 155
	if ($credit_score > 155) {
		// File upload for income proof (handling the file upload)
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["income_proof"]["name"]);
		if (move_uploaded_file($_FILES["income_proof"]["tmp_name"], $target_file)) {
			$income_proof_path = $target_file;
		} else {
			echo "Sorry, there was an error uploading your file.";
		}

		// Insert loan data into loan_requests table
		$customer_id = $id; // Assuming customer_id is known and provided during login or session
		$status = 'PENDING'; // Default status is "Pending"

		$sql = "INSERT INTO loan_requests (customer_id, loan_amount, loan_tenure, purpose,age, marital_status, dependents, residential_status, time_at_address, occupation, time_at_employer, current_account, home_phone, work_phone, income_proof, status, score)
            VALUES ('$customer_id', '$loan_amount', '$loan_tenure', '$purpose','$age_range', '$marital_status', '$dependents', '$residential_status', '$time_at_address', '$occupation', '$time_at_employer', '$current_account', '$home_phone', '$work_phone', '$income_proof_path', '$status', '$credit_score')";

		if (mysqli_query($conn, $sql)) {
			// Successful loan request submission
			$score = "You are eligible for this loan. Please wait until manager approval. Credit Score: $credit_score";
			// Redirect to another page (for example, loan_status.php) with the score as a query parameter
			header("Location: loanmanagement.php?score=" . urlencode($score));
			exit; // Always call exit after header() to ensure the script stops here
		} else {
			// Error in inserting the loan request
			$score = "Error: " . mysqli_error($conn);
			// Redirect to another page (for example, loan_status.php) with the error message as a query parameter
			header("Location: loanmanagement.php?score=" . urlencode($score));
			exit;
		}
	} else {
		// If not eligible, change the status to "REJECTED"
		$customer_id = $id; // Assuming customer_id is known and provided during login or session
		$status = 'REJECTED'; // Status is set to rejected for ineligible users

		// Insert loan data with rejected status
		$sql = "INSERT INTO loan_requests (customer_id, loan_amount, loan_tenure, purpose,age, marital_status, dependents, residential_status, time_at_address, occupation, time_at_employer, current_account, home_phone, work_phone, income_proof, status, score)
            VALUES ('$customer_id', '$loan_amount', '$loan_tenure', '$purpose','$age_range','$marital_status', '$dependents', '$residential_status', '$time_at_address', '$occupation', '$time_at_employer', '$current_account', '$home_phone', '$work_phone', '$income_proof_path', '$status', '$credit_score')";

		if (mysqli_query($conn, $sql)) {
			// Successful loan request submission
			$scoree = "You are not eligible for this loan. Credit Score: $credit_score";
			// Redirect to the same page and pass $scoree as a query string
			header("Location: " . $_SERVER['PHP_SELF'] . "?scoree=" . urlencode($scoree));
			exit; // Always call exit after header() to ensure the script stops here
		} else {
			// Error in inserting the loan request
			$scoree = "Error: " . mysqli_error($conn);
			// Redirect to the same page and pass the error message as a query string
			header("Location: " . $_SERVER['PHP_SELF'] . "?scoree=" . urlencode($scoree));
			exit;
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
			<li class="nav-item"><a class="nav-link " href="applyforaloan.php">
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
			<h1>APPLY FOR A LOAN</h1>
		</div>

		<section class="section">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<h2 class="card-title">Application -
								<span style="color: green;font-size: 24px ;font-weight: 900;">
									<?= isset($score) ? $score : '' ?>
								</span>
								<span style="color: red;font-size: 24px ;font-weight: 900;">
									<?= isset($scoree) ? $scoree : '' ?>
								</span>
							</h2>

							<?php
							if (isset($_GET['error'])) {
								echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
							}
							if (isset($_GET['success'])) {
								echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['success']) . "</div>";
							}
							?>


							<?php

							$sql = "SELECT * FROM `loan_requests` WHERE `customer_id` = '$id'";
							$result = mysqli_query($conn, $sql);

							if (mysqli_num_rows($result) > 0) {

								echo "<h3 style='color:red; font-weight:bold;'>You have already applied for a loan. Please check the Loan Management page for details.</h3>";
							} else {

							?>



								<form method="post" action="" enctype="multipart/form-data">
									<!-- Loan Amount -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="loan_amount">Loan Amount</label>
										<div class="col-sm-10">
											<input type="number" id="loan_amount" name="loan_amount" class="form-control" required>
										</div>
									</div>

									<!-- Loan Tenure -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="loan_tenure">Loan Tenure (Months)</label>
										<div class="col-sm-10">
											<select id="loan_tenure" name="loan_tenure" class="form-control" required>
												<option hidden value="">Select Loan Tenure</option>
												<option value="12">12 Months</option>
												<option value="24">24 Months</option>
												<option value="36">36 Months</option>
												<option value="48">48 Months</option>
												<option value="60">60 Months</option>
											</select>
										</div>
									</div>


									<!-- Purpose of Loan -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="purpose">Purpose of Loan</label>
										<div class="col-sm-10">
											<select id="purpose" name="purpose" class="form-control" required>
												<option value="" hidden selected>Select Purpose</option>
												<option value="used_bike">Used Bike</option>
												<option value="new_bike">New Bike</option>
											</select>
										</div>
									</div>

									<!-- Age -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="age">Age</label>
										<div class="col-sm-10">
											<select id="age" name="age" class="form-control">
												<option hidden value="">Select Age:</option>

												<option value="18-<21">18 - <21 </option>
												<option value="21-<25">21 - <25 </option>
												<option value="25-<30">25 - <30 </option>
												<option value="30-<40">30 - <40 </option>
												<option value="40-<50">40 - <50 </option>
												<option value="50+">50+</option>
											</select>
										</div>
									</div>


									<!-- Marital Status -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="marital_status">Marital Status</label>
										<div class="col-sm-10">
											<select id="marital_status" name="marital_status" class="form-control">
												<option hidden value="">Select Marital Status:</option>
												<option value="Single">Single</option>
												<option value="Married">Married</option>
												<option value="Divorced">Divorced</option>
												<option value="Other">Other</option>
											</select>
										</div>
									</div>

									<!-- Number of Dependents -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="dependents">Number of Dependents</label>
										<div class="col-sm-10">
											<select id="dependents" name="dependents" class="form-control">
												<option hidden value="">Select Dependents:</option>
												<option value="0">0</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3-4">3-4</option>
												<option value="4+">4 or more</option>
											</select>
										</div>
									</div>

									<!-- Residential Status -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="residential_status">Residential Status</label>
										<div class="col-sm-10">
											<select id="residential_status" name="residential_status" class="form-control">
												<option hidden value="">Select Residential Status:</option>
												<option value="Own">Own</option>
												<option value="Rent">Rent</option>
												<option value="Parents">Living with Parents</option>
												<option value="Company">Company Provided</option>
											</select>
										</div>
									</div>

									<!-- Time at Address -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="time_at_address">Time at Address (Years)</label>
										<div class="col-sm-10">
											<select id="time_at_address" name="time_at_address" class="form-control">
												<option hidden value="">Select Time At Address:</option>
												<option value="<1"> Less than 1</option>
												<option value="1-<3">1 - <3 </option>
												<option value="3-<6">3 - <6 </option>
												<option value="6-<10">6 - <10 </option>
												<option value="10-<15">10 - <15 </option>
												<option value="15+">15+</option>
											</select>
										</div>
									</div>

									<!-- Occupation -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="occupation">Occupation</label>
										<div class="col-sm-10">
											<select id="occupation" name="occupation" class="form-control">
												<option hidden value="">Select Occupation :</option>
												<option value="Prof / Ret">Professional / Retired</option>
												<option value="Skilled">Skilled</option>
												<option value="Office Staff">Office Staff</option>
												<option value="Unskilled">Unskilled</option>
												<option value="Self-Emp">Self-Employed</option>
												<option value="Others">Others</option>
											</select>
										</div>
									</div>


									<!-- Time at Employer -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="time_at_employer">Time at Employer (Years)</label>
										<div class="col-sm-10">
											<select id="time_at_employer" name="time_at_employer" class="form-control">
												<option hidden value="">Select Time At Employer:</option>
												<option value="<0.5">Less than 1/2</option>
												<option value="0.5-<2.5">0.5 - <2.5 </option>
												<option value="2.5-<5">2.5 - <5 </option>
												<option value="5-<8">5 - <8 </option>
												<option value="8+">8+</option>
											</select>
										</div>
									</div>

									<!-- Current Account -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="current_account">Current Account</label>
										<div class="col-sm-10">
											<select id="current_account" name="current_account" class="form-control">
												<option hidden value="">Select :</option>
												<option value="Yes">Yes</option>
												<option value="No">No</option>
											</select>
										</div>
									</div>

									<!-- Home Telephone -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="home_phone">Home Telephone</label>
										<div class="col-sm-10">
											<input type="text" id="home_phone" name="home_phone" class="form-control">
										</div>
									</div>

									<!-- Work Telephone -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="work_phone">Work Telephone</label>
										<div class="col-sm-10">
											<input type="text" id="work_phone" name="work_phone" class="form-control">
										</div>
									</div>

									<!-- Upload Income Proof -->
									<div class="row mb-3">
										<label class="col-sm-2 col-form-label" for="income_proof">Upload Income Proof</label>
										<div class="col-sm-10">
											<input type="file" id="income_proof" name="income_proof" class="form-control mt-2" required>
										</div>
									</div>

									<!-- Submit Button -->
									<div class="row mb-3">
										<div class="col-sm-10 offset-sm-2">
											<button type="submit" name="submit_loan" class="btn btn-primary btn-block">Apply for Loan</button>
										</div>
									</div>
								</form>


							<?php } ?>

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