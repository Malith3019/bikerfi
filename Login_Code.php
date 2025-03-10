<?php
include_once("connection.php");
session_start();
$message = "";

if (isset($_POST['submit'])) {
    if (count($_POST) > 0) {

        // Retrieve user details from the database
        $query = "SELECT * FROM users WHERE email='" . mysqli_real_escape_string($conn, $_POST["user"]) . "'"; // Sanitize the input
        $result = mysqli_query($conn, $query);

        // Check if the query was successful
        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $row = mysqli_fetch_array($result);

        // Verify the password
        if (is_array($row)) {
            // Use password_verify to check hashed password
            if (password_verify($_POST["password"], $row['password'])) {
                $_SESSION["id"] = $row['id'];
                $_SESSION["email"] = $row['email'];
                $_SESSION["full_name"] = $row['full_name'];
                $_SESSION["nic"] = $row['nic'];
                $_SESSION["role"] = $row['role']; // Store the role in the session
                $_SESSION["phone_number"] = $row['phone_number']; // Store the phone number in the session

                // Check if NIC is present in the 'crib' table
                $nic = $row['nic'];
                $crib_check_query = "SELECT id FROM crib WHERE nic='" . mysqli_real_escape_string($conn, $nic) . "'";
                $crib_check_result = mysqli_query($conn, $crib_check_query);

                // If the NIC is found in the crib table
                if (mysqli_num_rows($crib_check_result) > 0) {
                    $msg1 = "Your NIC is in the Crib. You cannot log in or apply for a loan.";
                    header("Location: index.php?msg=$msg1");
                    exit;
                }

                // Check the role and redirect accordingly
                if ($row['role'] == 'C') {
                    // Customer role
                    header("Location: status.php");
                } elseif ($row['role'] == 'S') {
                    // Admin role
                    header("Location: super_admin_dashboard.php");
                } elseif ($row['role'] == 'A') {
                    // Staff role
                    header("Location: admin_dashboard.php");
                } else {
                    // Handle cases where the role is neither 'C', 'A', nor 'S'
                    $msg1 = "Invalid user role!";
                    header("Location: index.php?msg=$msg1");
                }
                exit;
            } else {
                $msg1 = "Incorrect password!";
                header("Location: index.php?msg=$msg1");
                exit;
            }
        } else {
            $msg1 = "No user found with that email!";
            header("Location: index.php?msg=$msg1");
            exit;
        }
    }
}

// If the session is already set, you can redirect them to the home page
if (isset($_SESSION["id"])) {
    // Check the role again to ensure the correct dashboard is loaded
    if ($_SESSION["role"] == 'C') {
        header("Location: profile.php");
    } elseif ($_SESSION["role"] == 'S') {
        header("Location: super_admin_dashboard.php");
    } elseif ($_SESSION["role"] == 'A') {
        header("Location: admin_dashboard.php");
    }
    exit;
}
?>
