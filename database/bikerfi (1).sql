-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 06:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bikerfi`
--

-- --------------------------------------------------------

--
-- Table structure for table `credit_score_criteria`
--

CREATE TABLE `credit_score_criteria` (
  `id` int(11) NOT NULL,
  `criterion` varchar(255) DEFAULT NULL,
  `value_range` varchar(255) DEFAULT NULL,
  `score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_score_criteria`
--

INSERT INTO `credit_score_criteria` (`id`, `criterion`, `value_range`, `score`) VALUES
(1, 'Age', '18-<21', 5),
(2, 'Age', '21-<25', 10),
(3, 'Age', '25-<30', 20),
(4, 'Age', '30-<40', 30),
(5, 'Age', '40-<50', 35),
(6, 'Age', '50+', 45),
(7, 'Age', 'Incomplete', 5),
(8, 'Marital Status', 'Single', 15),
(9, 'Marital Status', 'Married', 20),
(10, 'Marital Status', 'Divorced', 5),
(11, 'Marital Status', 'Other', 15),
(12, 'Marital Status', 'Incomplete', 5),
(13, 'Number of Dependents', '0', 15),
(14, 'Number of Dependents', '1', 15),
(15, 'Number of Dependents', '2', 35),
(16, 'Number of Dependents', '3-4', 10),
(17, 'Number of Dependents', '4+', 5),
(18, 'Number of Dependents', 'Incomplete', 5),
(19, 'Residential Status', 'Own', 40),
(20, 'Residential Status', 'Rent', 20),
(21, 'Residential Status', 'Parents', 20),
(22, 'Residential Status', 'Company', 25),
(23, 'Residential Status', 'Incomplete', 15),
(24, 'Time at Address', '<1', 15),
(25, 'Time at Address', '1-<3', 20),
(26, 'Time at Address', '3-<6', 25),
(27, 'Time at Address', '6-<10', 30),
(28, 'Time at Address', '10-<15', 35),
(29, 'Time at Address', '15+', 40),
(30, 'Time at Address', 'Incomplete', 15),
(31, 'Occupation', 'Prof / Ret', 35),
(32, 'Occupation', 'Skilled', 30),
(33, 'Occupation', 'Office Staff', 30),
(34, 'Occupation', 'Unskilled', 15),
(35, 'Occupation', 'Self-Emp', 10),
(36, 'Occupation', 'Others', 30),
(37, 'Occupation', 'Incomplete', 10),
(38, 'Time at Employer', '<0.5', 10),
(39, 'Time at Employer', '0.5-<2.5', 20),
(40, 'Time at Employer', '2.5-<5', 30),
(41, 'Time at Employer', '5-<8', 35),
(42, 'Time at Employer', '8+', 40),
(43, 'Time at Employer', 'Incomplete', 10),
(44, 'Current A/C', 'Yes', 30),
(45, 'Current A/C', 'No', 10),
(46, 'Current A/C', 'Incomplete', 10),
(47, 'Telephone', 'Both', 30),
(48, 'Telephone', 'Home', 25),
(49, 'Telephone', 'Work', 10),
(50, 'Telephone', 'None', 5),
(51, 'Telephone', 'Incomplete', 5);

-- --------------------------------------------------------

--
-- Table structure for table `crib`
--

CREATE TABLE `crib` (
  `id` int(255) NOT NULL,
  `nic` varchar(13) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `due_duration` varchar(1000) DEFAULT NULL COMMENT 'days'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crib`
--

INSERT INTO `crib` (`id`, `nic`, `description`, `due_duration`) VALUES
(1, '000000000V', 'crib', '91'),
(2, '444444444V', 'crib', '89');

-- --------------------------------------------------------

--
-- Table structure for table `loan_interest_rates`
--

CREATE TABLE `loan_interest_rates` (
  `id` int(11) NOT NULL,
  `loan_tenure` int(11) DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_interest_rates`
--

INSERT INTO `loan_interest_rates` (`id`, `loan_tenure`, `interest_rate`) VALUES
(1, 12, 10.00),
(2, 24, 20.00),
(3, 36, 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `loan_requests`
--

CREATE TABLE `loan_requests` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `proof_added` varchar(1000) DEFAULT NULL,
  `loan_amount` decimal(10,2) DEFAULT NULL,
  `loan_tenure` int(11) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `age` varchar(200) DEFAULT NULL,
  `marital_status` enum('Single','Married','Divorced','Other') DEFAULT NULL,
  `dependents` enum('0','1','2','3-4','4+') DEFAULT NULL,
  `residential_status` enum('Own','Rent','Parents','Company') DEFAULT NULL,
  `time_at_address` varchar(200) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `time_at_employer` varchar(200) DEFAULT NULL,
  `current_account` enum('Yes','No') DEFAULT NULL,
  `home_phone` varchar(20) DEFAULT NULL,
  `work_phone` varchar(20) DEFAULT NULL,
  `income_proof` varchar(255) DEFAULT NULL,
  `score` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Level One Approved','Level Two Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `flag` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `remarks` varchar(10000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_requests`
--

INSERT INTO `loan_requests` (`id`, `customer_id`, `proof_added`, `loan_amount`, `loan_tenure`, `purpose`, `age`, `marital_status`, `dependents`, `residential_status`, `time_at_address`, `occupation`, `time_at_employer`, `current_account`, `home_phone`, `work_phone`, `income_proof`, `score`, `status`, `created_at`, `flag`, `remarks`) VALUES
(248, 900010023, 'Y', 90000.00, 12, 'used_bike', '21-<25', 'Single', '2', 'Own', '15+', 'Office Staff', '2.5-<5', 'Yes', '03422344565', '03422344565', 'uploads/Screenshot 2024-12-16 165833.png', '260', 'Level Two Approved', '2025-03-25 17:11:03', 'Active', 'ok'),
(249, 900010024, NULL, 5000000.00, 36, 'new_bike', '25-<30', 'Married', '1', 'Own', '1-<3', 'Skilled', '0.5-<2.5', 'Yes', '0343434633', '0343434633', 'uploads/Screenshot 2024-12-16 164221.png', '225', 'Pending', '2025-03-25 17:13:34', 'Active', NULL),
(250, 900010025, 'Y', 1000000.00, 12, 'used_bike', '50+', 'Married', '1', 'Own', '15+', 'Prof / Ret', '8+', 'Yes', '0342236873', '0342236873', 'uploads/Screenshot 2024-12-16 164659.png', '295', 'Level One Approved', '2025-03-25 17:16:33', 'Active', 'ok');

-- --------------------------------------------------------

--
-- Table structure for table `loan_requests_proofs`
--

CREATE TABLE `loan_requests_proofs` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `document` varchar(10000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_requests_proofs`
--

INSERT INTO `loan_requests_proofs` (`id`, `customer_id`, `document`, `created_at`, `updated_at`) VALUES
(66, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/payslip_67e2e3c3dc360.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(67, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/payslip_67e2e3c3dc61d.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(68, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/payslip_67e2e3c3dc886.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(69, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/payslip_67e2e3c3dcde7.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(70, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/bank_statement_67e2e3c3e1173.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(71, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/bank_statement_67e2e3c3e1714.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(72, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/bank_statement_67e2e3c3e1c5a.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(73, 900010023, 'uploads/user_900010023_980880648V_20250325_181131/service_letter_67e2e3c3e20be.png', '2025-03-25 17:11:31', '2025-03-25 17:11:31'),
(74, 900010025, 'uploads/user_900010025_980880647V_20250325_181654/payslip_67e2e506ae19e.png', '2025-03-25 17:16:54', '2025-03-25 17:16:54'),
(75, 900010025, 'uploads/user_900010025_980880647V_20250325_181654/bank_statement_67e2e506ae395.png', '2025-03-25 17:16:54', '2025-03-25 17:16:54'),
(76, 900010025, 'uploads/user_900010025_980880647V_20250325_181654/service_letter_67e2e506ae78a.png', '2025-03-25 17:16:54', '2025-03-25 17:16:54');

-- --------------------------------------------------------

--
-- Table structure for table `nic`
--

CREATE TABLE `nic` (
  `id` int(1) NOT NULL,
  `nic` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nic`
--

INSERT INTO `nic` (`id`, `nic`) VALUES
(1, '980880648V'),
(2, '980880649V'),
(4, '980880647V');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(255) NOT NULL,
  `role` varchar(30) DEFAULT NULL,
  `flag` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`, `flag`) VALUES
(1, 'CUSTOMER', 'C'),
(2, 'ADMIN', 'A'),
(3, 'SUPER ADMIN', 'S');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `role` varchar(1) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `nic` varchar(13) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(500) DEFAULT NULL,
  `agreement` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`role`, `id`, `full_name`, `email`, `phone_number`, `nic`, `password`, `created_at`, `updated_at`, `image`, `agreement`) VALUES
('A', 900010001, 'ADMIN', 'admin@gmail.com', '0769433940', '12345678', '$2y$10$BoTUWsoKcnr8.TD4PYKOb.IN6kVk7Au7ch9vgYjROL4DCfBEJcBju', '2025-02-24 05:03:19', '2025-03-25 16:58:45', 'assets/img/user.webp', ''),
('S', 900010002, 'SUPERADMIN', 'superadmin@gmail.com', '0769433940', '123456789', '$2y$10$BoTUWsoKcnr8.TD4PYKOb.IN6kVk7Au7ch9vgYjROL4DCfBEJcBju', '2025-02-24 05:03:19', '2025-03-25 16:58:22', 'assets/img/user.webp', ''),
('C', 900010023, 'Malith Ranaweera', 'malithranaweera@gmail.com', '0771111111', '980880648V', '$2y$10$UziB6ALsm8Udv4Y/YTmO6O9CFUSzsKw1veFwdvNpLtKJ7WLfrP3t2', '2025-03-25 17:09:39', '2025-03-25 17:22:14', NULL, 'Y'),
('C', 900010024, 'Deshan', 'deshanranaweera@gmail.com', '0771231235', '980880649V', '$2y$10$Nll2cMFBVprtm7OaTgYig.Mc.PJKh65m2dgVw.hj9eWL4dvLXUdhC', '2025-03-25 17:12:29', '2025-03-25 17:12:29', NULL, NULL),
('C', 900010025, 'saman ranaweera', 'samanranaweera@hotmail.com', '0776193850', '980880647V', '$2y$10$tP91ui1MZOsoyH6r1U0hRurTs9cshNDEYngQ7NBimB2R1QXwK/Pdm', '2025-03-25 17:15:34', '2025-03-25 17:15:34', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `credit_score_criteria`
--
ALTER TABLE `credit_score_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crib`
--
ALTER TABLE `crib`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_interest_rates`
--
ALTER TABLE `loan_interest_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_requests`
--
ALTER TABLE `loan_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `loan_requests_proofs`
--
ALTER TABLE `loan_requests_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `nic`
--
ALTER TABLE `nic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nic` (`nic`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `credit_score_criteria`
--
ALTER TABLE `credit_score_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `crib`
--
ALTER TABLE `crib`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loan_interest_rates`
--
ALTER TABLE `loan_interest_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_requests`
--
ALTER TABLE `loan_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `loan_requests_proofs`
--
ALTER TABLE `loan_requests_proofs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `nic`
--
ALTER TABLE `nic`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=900010026;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loan_requests`
--
ALTER TABLE `loan_requests`
  ADD CONSTRAINT `loan_requests_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `loan_requests_proofs`
--
ALTER TABLE `loan_requests_proofs`
  ADD CONSTRAINT `loan_requests_proofs_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
