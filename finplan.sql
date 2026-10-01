-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2026 at 05:28 AM
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
-- Database: `finplan`
--

-- --------------------------------------------------------

--
-- Table structure for table `adv_reg`
--

CREATE TABLE `adv_reg` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `qual` varchar(50) NOT NULL,
  `yoe` varchar(30) NOT NULL,
  `photo` varchar(50) NOT NULL,
  `certificate` varchar(255) DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  `stat` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adv_reg`
--

INSERT INTO `adv_reg` (`id`, `name`, `email`, `password`, `phone`, `qual`, `yoe`, `photo`, `certificate`, `description`, `stat`) VALUES
(32, 'Nidhin', 'nidhin@gmail.com', '', '9567991716', 'MBA', '6', '1769180259_team-4.jpg', '1771251618_cert_nidhin.png', 'Passionate financial advisor focused on guiding individuals toward smarter saving, investing, and fi', '2'),
(33, 'Farhan', 'farhan@gmail.com', '', '8589946755', 'MCA', '7', '1769283625_testimonial-1.jpg', '1771251589_cert_farhan.png', 'hi', '2'),
(35, 'Anandha Gopal', 'anandhu@gmail.com', '', '9876543210', 'MBA', '5', '1771251219_profile-img.jpg', '1771251219_cert_gopal.png', 'Hi', '2');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `advisor_id` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `additional_info` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `room_id` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `session_active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `advisor_id`, `date`, `time`, `purpose`, `additional_info`, `user_id`, `created_at`, `status`, `room_id`, `payment_status`, `session_active`) VALUES
(33, '32', '2026-03-09', '07:30 PM', 'loan related', 'hi iam thahina', '34', '2026-03-09 14:55:15', 'Approved', 'a605fda268edd36d', 'Verified', 0),
(34, '33', '2026-03-09', '08:44 PM', 'finacial engiury', 'hi ', '34', '2026-03-09 15:13:47', 'completed', '157e9f4e6e3a4270', 'Verified', 0),
(35, '32', '2026-03-09', '09:59 PM', 'loan related', 'hlo', '34', '2026-03-09 16:28:42', 'completed', '3fef063ba4733483', 'Verified', 0),
(36, '35', '2026-03-10', '09:27 AM', 'realestate', 'asdfghjkl', '34', '2026-03-10 03:57:27', 'completed', '2ec7940be0ed099c', 'Verified', 0);

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `account_id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `account_title` varchar(255) DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`account_id`, `user_email`, `account_title`, `account_type`, `account_number`, `balance`) VALUES
(5, NULL, 'abc', '1', '1699101038473', 1000.00),
(6, NULL, 'abc', '2', '10002000', 2000.00),
(7, NULL, 'abc', '3', '1234567', 15000.00),
(12, 'thahina@gmail.com', 'Canara Bank', 'Savings', '1699101038847', 138600.00);

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `additional_info` text NOT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expence`
--

CREATE TABLE `expence` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `monthly_income` decimal(15,2) NOT NULL,
  `monthly_expences` decimal(15,2) NOT NULL,
  `current_savings` decimal(15,2) NOT NULL,
  `financial_goal` varchar(255) NOT NULL,
  `target_amount` decimal(15,2) NOT NULL,
  `target_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expence`
--

INSERT INTO `expence` (`id`, `user_id`, `monthly_income`, `monthly_expences`, `current_savings`, `financial_goal`, `target_amount`, `target_date`, `created_at`) VALUES
(8, 34, 100000.00, 50000.00, 50000.00, 'car', 1500000.00, '2027-07-26', '2026-02-19 06:14:10'),
(9, 34, 100000.00, 50000.00, 50000.00, 'Laptop', 150000.00, '2026-11-17', '2026-03-07 08:04:06');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_plans`
--

CREATE TABLE `financial_plans` (
  `plan_id` int(11) NOT NULL,
  `advisor_id` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_plans`
--

INSERT INTO `financial_plans` (`plan_id`, `advisor_id`, `image`, `description`, `created_at`) VALUES
(1, 'ramu@gmail.com', 'uploads/advisors/fin_1.jpg', 'The 50/30/20 rule allocates 50% of income to essentials, 30% to discretionary spending, and 20% to savings and debt repayment. This plan helps in balanced budgeting for overall financial health.', '2024-11-10 20:28:40'),
(2, 'ramu@gmail.com', 'uploads/advisors/fin_2.png', 'With the envelope system, allocate funds into categories like groceries, rent, and entertainment using envelopes. This helps prevent overspending by restricting spending within budget limits.', '2024-11-10 20:32:53'),
(7, '32', 'uploads/advisors/finplan4.png', 'Financial planning is the process of setting financial goals and creating strategies to manage income, savings, investments, and expenses effectively. It helps individuals achieve long-term stability, prepare for emergencies, and secure their future with confidence.', '2026-01-24 19:30:31');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `goal_name` varchar(255) NOT NULL,
  `target_amount` float NOT NULL,
  `current_amount` decimal(10,2) DEFAULT 0.00,
  `start_date` date NOT NULL,
  `target_date` date NOT NULL,
  `monthly_contribution` float NOT NULL,
  `priority_level` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `user_id` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id`, `goal_name`, `target_amount`, `current_amount`, `start_date`, `target_date`, `monthly_contribution`, `priority_level`, `category`, `notes`, `user_id`) VALUES
(5, 'home', 150000000, 0.00, '2025-12-29', '2026-12-12', 2000, 'Medium', 'Investment', 'grgreg', 'test@gmail.com'),
(6, 'Home', 300000, 0.00, '2026-01-24', '2027-01-24', 15000, 'High', 'Savings', 'dream home', '<br />\r\n<b>Warning</b>:  Undefined variable $userid in <b>D:\\xampp\\htdocs\\FinPlan\\User_Dashboard\\add_goal.php</b> on line <b>187</b><br />\r\n'),
(9, 'home', 3000000, 160000.00, '2026-02-19', '2028-07-26', 30000, 'Medium', 'Savings', 'dream home', '34'),
(10, 'Ps5', 200000, 23000.00, '2026-02-19', '2027-01-04', 20000, 'Medium', 'Savings', '', '34');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `room_id` varchar(50) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `room_id`, `sender_id`, `message`, `created_at`) VALUES
(9, 'a605fda268edd36d', 34, 'hi, iam Thahina', '2026-03-09 14:58:25'),
(10, 'a605fda268edd36d', 34, 'tell me', '2026-03-09 14:58:33'),
(11, 'a605fda268edd36d', 32, 'hi , iam Nidhin', '2026-03-09 15:01:09'),
(12, '157e9f4e6e3a4270', 33, 'hi', '2026-03-09 15:22:56'),
(13, '157e9f4e6e3a4270', 34, 'thahina', '2026-03-09 15:23:27'),
(14, '157e9f4e6e3a4270', 34, 'hi', '2026-03-09 15:25:33'),
(15, '157e9f4e6e3a4270', 34, 'hi', '2026-03-09 15:26:20'),
(16, '157e9f4e6e3a4270', 34, 'hi', '2026-03-09 15:27:28'),
(17, '157e9f4e6e3a4270', 34, 'hloooo', '2026-03-09 15:33:47'),
(18, '157e9f4e6e3a4270', 34, 'hi', '2026-03-09 15:36:17'),
(19, '3fef063ba4733483', 32, 'hlo , iam nidhin', '2026-03-09 16:30:16'),
(20, '3fef063ba4733483', 34, 'hi , iam thahinaaa', '2026-03-09 16:30:46'),
(21, '2ec7940be0ed099c', 35, 'hi , iam gopalan', '2026-03-10 03:59:20'),
(22, '2ec7940be0ed099c', 34, 'hi iam thahina', '2026-03-10 03:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 50.00,
  `card_last_four` varchar(4) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT 'Credit Card'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `appointment_id`, `user_id`, `amount`, `card_last_four`, `transaction_date`, `payment_method`) VALUES
(16, 33, 34, 50.00, '8766', '2026-03-09 14:57:13', 'Credit Card'),
(17, 34, 34, 50.00, '5678', '2026-03-09 15:14:29', 'Credit Card'),
(18, 35, 34, 50.00, '7767', '2026-03-09 16:29:39', 'Credit Card'),
(19, 36, 34, 50.00, '5592', '2026-03-10 03:58:38', 'Credit Card');

-- --------------------------------------------------------

--
-- Table structure for table `reg`
--

CREATE TABLE `reg` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `type` int(11) NOT NULL,
  `status` enum('pending','approved') DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reg`
--

INSERT INTO `reg` (`id`, `name`, `email`, `phone`, `password`, `type`, `status`) VALUES
(30, 'Admin', 'admin@gmail.com', '7356930165', '0192023a7bbd73250516f069df18b500', 0, 'approved'),
(32, 'Nidhin', 'nidhin@gmail.com', '9567991716', '658984a2eff7369d121575bec4b83684', 2, 'approved'),
(33, 'Farhan', 'farhan@gmail.com', '8589946755', 'aafccd2ba8f028c405615d4ca6aa3227', 2, 'approved'),
(34, 'Thahina', 'thahina@gmail.com', '7356930165', '20b806b71effdb6d4ffd705be7b23891', 1, 'approved'),
(35, 'Anandha Gopal', 'anandhu@gmail.com', '9876543210', '092e8c0116e883aee8398bc1ecef35f4', 2, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `transaction_type` varchar(50) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `category` text DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `transaction_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_email`, `account_id`, `transaction_type`, `amount`, `category`, `transaction_date`, `transaction_time`) VALUES
(11, NULL, NULL, 'Deposit', 5000.00, 'erty', '2026-01-24', '2026-01-24 07:26:54'),
(12, NULL, NULL, 'Deposit', 2000.00, 'ghh', '2026-01-24', '2026-01-24 07:51:04'),
(16, 'thahina@gmail.com', 12, 'Withdraw', 10000.00, 'Grocery', '2026-02-19', '2026-02-19 09:06:54'),
(17, 'thahina@gmail.com', 12, 'Withdraw', 500.00, 'Fuel', '2026-02-19', '2026-02-19 09:07:29'),
(18, 'thahina@gmail.com', 12, 'Withdraw', 2000.00, 'Medical', '2026-02-19', '2026-02-19 09:07:55'),
(19, 'thahina@gmail.com', 12, 'Deposit', 15000.00, 'Loan', '2026-02-19', '2026-02-19 09:08:34'),
(20, 'thahina@gmail.com', 12, 'Deposit', 500.00, 'Education', '2026-02-19', '2026-02-19 09:10:17'),
(21, 'thahina@gmail.com', NULL, 'Withdraw', 30000.00, 'Goal Deposit', '2026-02-19', '2026-02-19 10:31:54'),
(22, 'thahina@gmail.com', NULL, 'Withdraw', 3000.00, 'Goal Deposit', '2026-02-19', '2026-02-19 10:32:55'),
(23, 'thahina@gmail.com', NULL, 'Withdraw', 20000.00, 'Goal Deposit', '2026-03-07', '2026-03-07 08:02:22'),
(24, 'thahina@gmail.com', 12, 'Deposit', 100000.00, 'Others', '2026-03-07', '2026-03-07 08:07:35'),
(25, 'thahina@gmail.com', 12, 'Withdraw', 1000.00, 'Grocery', '2026-03-09', '2026-03-09 14:51:30'),
(26, 'thahina@gmail.com', 12, 'Withdraw', 500.00, 'Fuel', '2026-03-09', '2026-03-09 14:51:53'),
(27, 'thahina@gmail.com', 12, 'Deposit', 100.00, 'Medical', '2026-03-09', '2026-03-09 14:52:14'),
(28, 'thahina@gmail.com', NULL, 'Withdraw', 10000.00, 'Goal Deposit', '2026-03-10', '2026-03-10 03:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `upcoming_alerts`
--

CREATE TABLE `upcoming_alerts` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','notified') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upcoming_alerts`
--

INSERT INTO `upcoming_alerts` (`id`, `user_email`, `description`, `amount`, `due_date`, `status`) VALUES
(3, 'akdmuralimk@gmail.com', 'emi', 2000.00, '2024-12-12', 'pending'),
(4, 'akdmuralimk@gmail.com', 'emi', 4000.00, '2024-11-13', 'pending'),
(5, 'test@gmail.com', 'emi', 2000.00, '2025-12-29', 'pending'),
(6, '34', 'EMI', 8500.00, '2026-02-07', 'pending'),
(7, '34', 'EMI', 456.00, '2026-01-24', 'pending'),
(8, '34', 'EMI', 450.00, '2026-01-24', 'pending'),
(9, '34', 'EMI', 560.00, '2026-01-24', 'pending'),
(10, 'thahina@gmail.com', 'loan', 1500.00, '2026-01-24', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adv_reg`
--
ALTER TABLE `adv_reg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expence`
--
ALTER TABLE `expence`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `financial_plans`
--
ALTER TABLE `financial_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `reg`
--
ALTER TABLE `reg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `upcoming_alerts`
--
ALTER TABLE `upcoming_alerts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adv_reg`
--
ALTER TABLE `adv_reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expence`
--
ALTER TABLE `expence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_plans`
--
ALTER TABLE `financial_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reg`
--
ALTER TABLE `reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `upcoming_alerts`
--
ALTER TABLE `upcoming_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `bank_accounts_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `reg` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `reg` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `bank_accounts` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
