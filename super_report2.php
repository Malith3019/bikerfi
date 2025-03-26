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
			<li class="nav-item"><a class="nav-link collapsed" href="super_admin_dashboard.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>PERSONAL INFORMATION</span></a>
			</li>
			<li class="nav-item"><a class="nav-link collapsed" href="super_eligibleloans.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>ELIGIBLE LOAN APPLICATIONS</span></a>
			</li>
			<li class="nav-item"><a class="nav-link  collapsed" href="super_rejectedloans.php">
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
			<li class="nav-item"><a class="nav-link " href="super_report2.php">
					<i class="bi bi-arrow-right-square-fill"></i>
					<span>LEVEL 2 APPROVED LIST </span></a>
			</li>
		</ul>
	</aside>


	<main id="main" class="main">
		<div class="pagetitle">
			<h1>REPORTS</h1>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.html">REPORTS</a></li>
					<li class="breadcrumb-item active">LEVEL 2 APPROVED LIST - REPORT</li>
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
                            <h5 class="card-title">REPORTS > LEVEL 2 APPROVED LIST - REPORT</h5>
                            
                            <!-- Search Form -->
                            <form method="GET">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="from_date" class="form-label">From Date</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="to_date" class="form-label">To Date</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                            
							<!-- Button to Download CSV -->
							<button onclick="downloadCSV()" class="btn btn-success">Download CSV Report</button>

						

                            <table class="table table-bordered custom-gray-table table-striped datatable" id="loanDataTable">
							<thead class="thead-dark">
                                    <tr>
									<th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>NIC</th>
                                        <th>Credit Score</th>
                                        <th>Status</th>                                      
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
                                       
                                    </tr>
                                </thead>
								<tbody>
                                    <?php
                                    $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
                                    $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
                                    
                                    $sql = "SELECT lr.id, lr.proof_added, lr.customer_id, lr.loan_amount, lr.loan_tenure, lr.purpose,
                                                    lr.age, lr.marital_status, lr.dependents, lr.residential_status, lr.time_at_employer,
                                                    lr.current_account, lr.home_phone, lr.work_phone, lr.score, lr.status, lr.created_at, lr.income_proof,
                                                    u.full_name, u.email, u.phone_number, u.nic 
                                            FROM loan_requests lr
                                            JOIN users u ON lr.customer_id = u.id
                                            WHERE lr.status = 'Level Two Approved'";
                                    
                                    if (!empty($from_date) && !empty($to_date)) {
                                        $sql .= " AND lr.created_at BETWEEN '$from_date' AND '$to_date'";
                                    }
                                    
                                    $result = mysqli_query($conn, $sql);
                                    
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
											echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nic']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['score']) . "</td>";                                           
                                            $status = htmlspecialchars($row['status']);                                        
                                            echo "<td><b>$status</b></td>";                                                                                                                             
                                            echo "<td>" . htmlspecialchars($row['loan_amount']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['loan_tenure']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['purpose']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['marital_status']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['dependents']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['residential_status']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['time_at_employer']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['current_account']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['home_phone']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['work_phone']) . "</td>";                                                                                                                                
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='20' class='text-center'>No loan applications found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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
<script>
    		function downloadCSV() {
			let table = document.getElementById("loanDataTable"); // Get the table
			let rows = table.querySelectorAll("tr");
			let csvContent = [];
                     
			rows.forEach(row => {  
				let rowData = [];
				row.querySelectorAll("th, td").forEach(cell => {
					rowData.push(cell.innerText.replace(/,/g, "")); // Remove commas to avoid CSV issues
				});
				csvContent.push(rowData.join(",")); // Join row values with commas
			});
     
			let csvFile = new Blob([csvContent.join("\n")], { type: "text/csv" }); // Create a Blob
			let tempLink = document.createElement("a");
			tempLink.href = URL.createObjectURL(csvFile);
			tempLink.download = "<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?> - <?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?> - LEVEL 2 APPROVED.csv"; // Set file name
			tempLink.click();
		}
		</script>
</html>              