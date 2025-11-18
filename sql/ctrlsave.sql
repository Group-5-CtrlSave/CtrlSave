-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 12:22 PM
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
-- Database: `ctrlsave`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_achievements`
--

CREATE TABLE `tbl_achievements` (
  `achievementID` int(11) NOT NULL,
  `achievementName` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `lvl` int(11) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_defaultallocation`
--

CREATE TABLE `tbl_defaultallocation` (
  `defaultAllocationID` int(11) DEFAULT NULL,
  `defaultBudgetruleID` int(11) NOT NULL,
  `defaultnecessityType` varchar(30) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_defaultallocation`
--

INSERT INTO `tbl_defaultallocation` (`defaultAllocationID`, `defaultBudgetruleID`, `defaultnecessityType`, `value`) VALUES
(1, 1, 'need', 50),
(1, 1, 'want', 30),
(1, 1, 'saving', 20),
(2, 2, 'need', 60),
(2, 2, 'want', 20),
(2, 2, 'saving', 20),
(3, 3, 'need, want', 80),
(3, 3, 'saving', 20);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_defaultbudgetrule`
--

CREATE TABLE `tbl_defaultbudgetrule` (
  `defaultBudgetruleID` int(11) DEFAULT NULL,
  `ruleName` varchar(50) NOT NULL,
  `ruleDescription` varchar(200) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_defaultbudgetrule`
--

INSERT INTO `tbl_defaultbudgetrule` (`defaultBudgetruleID`, `ruleName`, `ruleDescription`, `createdAt`) VALUES
(1, '50/30/20 Rule', 'The 50/30/20 rule allocates your income into 50% needs, 30% wants, and 20% savings.', '2025-10-20 13:21:44'),
(2, '60/20/20 Rule', 'The 60/20/20 rule suggests using 60% for needs, 20% for wants, and 20% for savings.', '2025-10-20 13:21:44'),
(3, '80/20 Rule', 'The 80/20 rule is simple: save 20% and spend 80% on everything else.', '2025-10-20 13:21:44');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_defaultcategories`
--

CREATE TABLE `tbl_defaultcategories` (
  `defaultCategoryID` int(11) NOT NULL,
  `categoryName` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `defaultNecessitytype` varchar(20) NOT NULL,
  `defaultIsflexible` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_defaultcategories`
--

INSERT INTO `tbl_defaultcategories` (`defaultCategoryID`, `categoryName`, `type`, `icon`, `defaultNecessitytype`, `defaultIsflexible`) VALUES
(1, 'Allowance', 'income', 'Allowance.png', 'unspecified', 0),
(2, 'Income', 'income', 'Income.png', 'unspecified', 0),
(3, 'Scholarship', 'income', 'Scholarship.png', 'unspecified', 0),
(4, 'Car (Fuel, Maintenance)', 'expense', 'Car.png', 'want', 1),
(5, 'Clothes', 'expense', 'Clothes.png', 'need', 1),
(6, 'Coffee', 'expense', 'Coffee.png', 'want', 1),
(7, 'Dining Out', 'expense', 'Dining Out.png', 'want', 1),
(8, 'Electricity', 'expense', 'Electricity.png', 'need', 0),
(9, 'Entertainment', 'expense', 'Entertainment.png', 'want', 1),
(10, 'Gift', 'expense', 'Gift.png', 'want', 1),
(11, 'Groceries', 'expense', 'Groceries.png', 'need', 1),
(12, 'Health', 'expense', 'Health.png', 'need', 0),
(13, 'House', 'expense', 'House.png', 'need', 0),
(14, 'Internet Connection', 'expense', 'Internet Connection.png', 'need', 0),
(15, 'Laundry', 'expense', 'Laundry.png', 'need', 0),
(16, 'Party', 'expense', 'Party.png', 'want', 1),
(17, 'Rent', 'expense', 'Rent.png', 'need', 0),
(18, 'School Needs', 'expense', 'School Needs.png', 'need', 0),
(19, 'Selfcare', 'expense', 'Selfcare.png', 'need', 1),
(20, 'Shopping', 'expense', 'Shopping.png', 'want', 1),
(21, 'Subscriptions', 'expense', 'Subscriptions.png', 'want', 1),
(22, 'Transportation', 'expense', 'Transportation.png', 'need', 0),
(23, 'Tuition', 'expense', 'Tuition.png', 'need', 0),
(24, 'Water', 'expense', 'Water.png', 'need', 0),
(25, 'Savings', 'savings', 'default-category.png', 'saving', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_expense`
--

CREATE TABLE `tbl_expense` (
  `expenseID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `userCategoryID` int(11) NOT NULL,
  `dateSpent` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isRecurring` tinyint(1) NOT NULL DEFAULT 0,
  `note` varchar(200) NOT NULL,
  `userBudgetversionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_forecasts`
--

CREATE TABLE `tbl_forecasts` (
  `forecastID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `forecastType` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  `forecastMonth` int(11) NOT NULL,
  `predictedAmount` int(11) NOT NULL,
  `confidence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_goaltransactions`
--

CREATE TABLE `tbl_goaltransactions` (
  `goalTransactionID` int(11) NOT NULL,
  `savingGoalID` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction` varchar(10) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_income`
--

CREATE TABLE `tbl_income` (
  `incomeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `dateReceived` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` varchar(200) NOT NULL,
  `userCategoryID` int(11) NOT NULL,
  `userBudgetversionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notifications`
--

CREATE TABLE `tbl_notifications` (
  `notificationID` int(11) NOT NULL,
  `notificationTitle` varchar(50) NOT NULL,
  `message` varchar(200) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `isRead` tinyint(1) NOT NULL DEFAULT 0,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_recurringtransactions`
--

CREATE TABLE `tbl_recurringtransactions` (
  `recurringID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `userCategoryID` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `frequency` varchar(20) NOT NULL,
  `nextDuedate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resources`
--

CREATE TABLE `tbl_resources` (
  `resourceID` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `resourceType` varchar(10) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_savinggoals`
--

CREATE TABLE `tbl_savinggoals` (
  `savingGoalID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `goalName` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `targetAmount` decimal(15,2) NOT NULL,
  `currentAmount` decimal(15,2) NOT NULL,
  `deadline` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `remind` tinyint(1) NOT NULL,
  `repeatFrequency` varchar(20) NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_spendinginsights`
--

CREATE TABLE `tbl_spendinginsights` (
  `spendingInsightID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `categoryA` int(11) NOT NULL,
  `categoryB` int(11) NOT NULL,
  `insightType` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_userachievements`
--

CREATE TABLE `tbl_userachievements` (
  `userAchievementID` int(11) NOT NULL,
  `achievementID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `isClaimed` tinyint(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_userallocation`
--

CREATE TABLE `tbl_userallocation` (
  `userAllocationID` int(11) NOT NULL,
  `userBudgetruleID` int(11) NOT NULL,
  `userCategoryID` int(11) DEFAULT NULL,
  `necessityType` varchar(20) NOT NULL,
  `limitType` int(11) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_userbudgetversion`
--

CREATE TABLE `tbl_userbudgetversion` (
  `userBudgetversionID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_usercategories`
--

CREATE TABLE `tbl_usercategories` (
  `userCategoryID` int(11) NOT NULL,
  `categoryName` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `userNecessityType` varchar(20) NOT NULL,
  `userisFlexible` tinyint(1) NOT NULL,
  `defaultCategoryID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `isSelected` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_userlvl`
--

CREATE TABLE `tbl_userlvl` (
  `userLvlID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  `lvl` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `userID` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `currencyCode` varchar(3) NOT NULL,
  `isDisabled` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_achievements`
--
ALTER TABLE `tbl_achievements`
  ADD PRIMARY KEY (`achievementID`);

--
-- Indexes for table `tbl_defaultcategories`
--
ALTER TABLE `tbl_defaultcategories`
  ADD PRIMARY KEY (`defaultCategoryID`);

--
-- Indexes for table `tbl_expense`
--
ALTER TABLE `tbl_expense`
  ADD PRIMARY KEY (`expenseID`);

--
-- Indexes for table `tbl_forecasts`
--
ALTER TABLE `tbl_forecasts`
  ADD PRIMARY KEY (`forecastID`);

--
-- Indexes for table `tbl_income`
--
ALTER TABLE `tbl_income`
  ADD PRIMARY KEY (`incomeID`);

--
-- Indexes for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  ADD PRIMARY KEY (`notificationID`);

--
-- Indexes for table `tbl_recurringtransactions`
--
ALTER TABLE `tbl_recurringtransactions`
  ADD PRIMARY KEY (`recurringID`);

--
-- Indexes for table `tbl_resources`
--
ALTER TABLE `tbl_resources`
  ADD PRIMARY KEY (`resourceID`);

--
-- Indexes for table `tbl_savinggoals`
--
ALTER TABLE `tbl_savinggoals`
  ADD PRIMARY KEY (`savingGoalID`);

--
-- Indexes for table `tbl_spendinginsights`
--
ALTER TABLE `tbl_spendinginsights`
  ADD PRIMARY KEY (`spendingInsightID`);

--
-- Indexes for table `tbl_userachievements`
--
ALTER TABLE `tbl_userachievements`
  ADD PRIMARY KEY (`userAchievementID`);

--
-- Indexes for table `tbl_userallocation`
--
ALTER TABLE `tbl_userallocation`
  ADD PRIMARY KEY (`userAllocationID`);

--
-- Indexes for table `tbl_userbudgetversion`
--
ALTER TABLE `tbl_userbudgetversion`
  ADD PRIMARY KEY (`userBudgetversionID`);

--
-- Indexes for table `tbl_usercategories`
--
ALTER TABLE `tbl_usercategories`
  ADD PRIMARY KEY (`userCategoryID`);

--
-- Indexes for table `tbl_userlvl`
--
ALTER TABLE `tbl_userlvl`
  ADD PRIMARY KEY (`userLvlID`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_achievements`
--
ALTER TABLE `tbl_achievements`
  MODIFY `achievementID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_defaultcategories`
--
ALTER TABLE `tbl_defaultcategories`
  MODIFY `defaultCategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_expense`
--
ALTER TABLE `tbl_expense`
  MODIFY `expenseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_forecasts`
--
ALTER TABLE `tbl_forecasts`
  MODIFY `forecastID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_income`
--
ALTER TABLE `tbl_income`
  MODIFY `incomeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_recurringtransactions`
--
ALTER TABLE `tbl_recurringtransactions`
  MODIFY `recurringID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_resources`
--
ALTER TABLE `tbl_resources`
  MODIFY `resourceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_savinggoals`
--
ALTER TABLE `tbl_savinggoals`
  MODIFY `savingGoalID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_spendinginsights`
--
ALTER TABLE `tbl_spendinginsights`
  MODIFY `spendingInsightID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_userachievements`
--
ALTER TABLE `tbl_userachievements`
  MODIFY `userAchievementID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_userallocation`
--
ALTER TABLE `tbl_userallocation`
  MODIFY `userAllocationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_userbudgetversion`
--
ALTER TABLE `tbl_userbudgetversion`
  MODIFY `userBudgetversionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_usercategories`
--
ALTER TABLE `tbl_usercategories`
  MODIFY `userCategoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_userlvl`
--
ALTER TABLE `tbl_userlvl`
  MODIFY `userLvlID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
