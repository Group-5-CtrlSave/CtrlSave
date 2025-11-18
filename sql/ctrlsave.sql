-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 08:06 AM
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
  `type` varchar(20) NOT NULL,
  `achievementDescription` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_achievements`
--

INSERT INTO `tbl_achievements` (`achievementID`, `achievementName`, `icon`, `lvl`, `type`, `achievementDescription`) VALUES
(1, 'Newbie Saver', 'newbieTitle.png', 1, 'title', 'Reach Level 1'),
(2, 'Passionate Saver', 'passionateTitle.png', 5, 'title', 'Reach Level 5'),
(3, 'Elite Saver', 'eliteTitle.png', 7, 'title', 'Reach Level 7'),
(4, 'Veteran Saver', 'veteranTitle.png', 10, 'title', 'Reach Level 10'),
(5, 'Newcomer', 'newcomerBadge.png', 0, 'badge', 'Login to CtrlSave'),
(6, 'Income Badge', 'incomeproBadge.png', 0, 'badge', 'Add Income Transactions, 20 times'),
(7, 'Saving Badge', 'savinggoalsBadge.png', 0, 'badge', 'Complete a Saving Goal'),
(8, 'Challenge Pro', 'challengeproBadge.png', 0, 'badge', 'Complete 20 Challenges');

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
  `dueDate` date DEFAULT NULL,
  `isRecurring` tinyint(1) NOT NULL DEFAULT 0,
  `note` varchar(200) DEFAULT NULL,
  `recurringID` int(11) DEFAULT NULL,
  `userBudgetversionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_expense`
--

INSERT INTO `tbl_expense` (`expenseID`, `userID`, `amount`, `userCategoryID`, `dateSpent`, `dueDate`, `isRecurring`, `note`, `recurringID`, `userBudgetversionID`) VALUES
(1, 1, 5000.00, 3, '2025-11-17 16:00:00', NULL, 0, 'Groceries for this month', 0, 1);

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
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_goaltransactions`
--

INSERT INTO `tbl_goaltransactions` (`goalTransactionID`, `savingGoalID`, `amount`, `transaction`, `date`) VALUES
(1, 1, 500.00, 'Deposit', '2025-11-01 10:00:00'),
(2, 1, 1000.00, 'Deposit', '2025-11-02 11:30:00'),
(3, 1, 5.00, 'add', '2025-11-05 00:00:00'),
(4, 6, 1000.00, 'add', '2025-11-15 00:00:00'),
(5, 6, 10000.00, 'add', '2025-11-15 00:00:00'),
(6, 6, 100.00, 'add', '2025-11-15 00:00:00'),
(7, 6, 100.00, 'add', '2025-11-15 00:00:00'),
(8, 6, 100.00, 'add', '2025-11-15 21:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_income`
--

CREATE TABLE `tbl_income` (
  `incomeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `dateReceived` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` varchar(200) DEFAULT NULL,
  `userCategoryID` int(11) NOT NULL,
  `userBudgetversionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_income`
--

INSERT INTO `tbl_income` (`incomeID`, `userID`, `amount`, `dateReceived`, `note`, `userCategoryID`, `userBudgetversionID`) VALUES
(25, 1, 500.00, '2025-11-18 06:59:27', 'Baon for today', 5, 1);

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
  `note` varchar(200) NOT NULL,
  `frequency` varchar(20) NOT NULL,
  `nextDuedate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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

--
-- Dumping data for table `tbl_resources`
--

INSERT INTO `tbl_resources` (`resourceID`, `link`, `resourceType`, `title`, `description`) VALUES
(11, 'https://www.youtube.com/watch?v=beJeJFHxnDI', 'video', 'Ultimate Guide to Save Money on a Tight Budget', 'A beginner-friendly guide explaining budgeting strategies for tight incomes.'),
(12, 'https://www.youtube.com/watch?v=HyMQpsGsmwg', 'video', 'The Best Way to Save Money and Invest', 'Quick explanation on building savings and investment habits.'),
(13, 'https://www.youtube.com/watch?v=_jrUzpd-WPg', 'video', 'Guide to Savings for Beginners', 'Clear breakdown of essential savings concepts: what, why, and how to start.'),
(14, 'https://www.nerdwallet.com/article/finance/how-to-save-money', 'article', 'How to Save Money: 17 Proven Ways', 'NerdWallet shares practical, real-life money-saving tips.'),
(15, 'https://www.ramseysolutions.com/saving/how-to-save-money', 'article', '20 Simple Ways to Save Money', 'Actionable saving tips focused on cutting daily expenses.'),
(16, 'https://www.consumer.gov/articles/1002-making-budget', 'article', 'Making a Budget (Consumer.gov)', 'Easy-to-understand article on building a personalized budgeting plan.'),
(21, 'https://www.gutenberg.org/files/1727/1727-h/1727-h.htm', 'book', 'The Richest Man in Babylon', 'Timeless parables on saving 10%, paying debts, and growing wealth. '),
(22, 'https://www.gutenberg.org/files/8587/8587-h/8587-h.htm', 'book', 'The Art of Money Getting', 'P.T. Barnum’s 20 rules: avoid debt, work hard, save first. '),
(23, 'https://www.gutenberg.org/files/368/368-h/368-h.htm', 'book', 'Acres of Diamonds', 'You don’t need to go far to get rich – wealth is in your backyard. '),
(24, 'https://www.gutenberg.org/files/4507/4507-h/4507-h.htm', 'book', 'As a Man Thinketh', 'Your thoughts control your finances. '),
(25, 'https://www.gutenberg.org/files/37115/37115-h/37115-h.htm', 'book', 'Thrift', 'Victorian guide to living below your means, saving, and self-reliance. ');

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
  `remind` tinyint(1) NOT NULL DEFAULT 0,
  `time` time DEFAULT NULL,
  `frequency` varchar(20) DEFAULT NULL
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

--
-- Dumping data for table `tbl_userachievements`
--

INSERT INTO `tbl_userachievements` (`userAchievementID`, `achievementID`, `userID`, `isClaimed`, `date`) VALUES
(1, 1, 2, 1, '2025-11-13 23:52:22'),
(2, 2, 2, 1, '2025-11-13 23:53:48'),
(3, 3, 2, 1, '2025-11-14 00:17:41'),
(4, 4, 2, 1, '2025-11-14 01:02:24'),
(5, 5, 2, 1, '2025-11-14 00:24:17'),
(6, 6, 2, 1, '2025-11-14 00:18:40'),
(7, 7, 2, 0, '2025-11-13 20:03:48'),
(8, 8, 2, 0, '2025-11-13 22:25:26'),
(9, 1, 3, 1, '2025-11-14 05:43:32'),
(10, 2, 3, 1, '2025-11-14 05:43:50'),
(11, 3, 3, 1, '2025-11-14 05:44:56'),
(12, 4, 3, 1, '2025-11-15 01:02:36'),
(13, 5, 3, 1, '2025-11-14 05:43:37'),
(14, 6, 3, 1, '2025-11-14 05:43:55'),
(15, 7, 3, 1, '2025-11-14 05:44:02'),
(16, 8, 3, 0, '2025-11-13 23:56:40');

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
-- Table structure for table `tbl_userbudgetrule`
--

CREATE TABLE `tbl_userbudgetrule` (
  `userBudgetRuleID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `ruleName` varchar(100) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `isSelected` tinyint(1) NOT NULL
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

--
-- Dumping data for table `tbl_userbudgetversion`
--

INSERT INTO `tbl_userbudgetversion` (`userBudgetversionID`, `userID`, `balance`, `createdAt`, `isActive`) VALUES
(1, 1, 10000.00, '2025-11-05 13:21:41', 1);

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

--
-- Dumping data for table `tbl_usercategories`
--

INSERT INTO `tbl_usercategories` (`userCategoryID`, `categoryName`, `type`, `icon`, `userNecessityType`, `userisFlexible`, `defaultCategoryID`, `userID`, `isSelected`) VALUES
(1, 'Dining Out', 'expense', 'Dining Out.png', 'Want', 1, 7, 1, 1),
(2, 'Electricity', 'expense', 'Electricity.png', 'Need', 0, 8, 1, 1),
(3, 'Groceries', 'expense', 'Groceries.png', 'Need', 1, 11, 1, 1),
(4, 'Savings', 'savings', 'Savings.png', 'Saving', 0, 25, 1, 1),
(5, 'Allowance', 'income', 'Allowance.png', 'unspecified', 0, 1, 1, 1),
(6, 'Income', 'income', 'Income.png', 'unspecified', 0, 2, 1, 1),
(7, 'Scholarship', 'income', 'Scholarship.png', 'unspecified', 0, 3, 1, 1);

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
  `password` varchar(100) NOT NULL,
  `currencyCode` varchar(3) NOT NULL DEFAULT 'PH',
  `isDisabled` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `profilePicture` varchar(255) DEFAULT NULL,
  `displayedBadges` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`userID`, `userName`, `firstName`, `lastName`, `email`, `password`, `currencyCode`, `isDisabled`, `createdAt`, `profilePicture`, `displayedBadges`) VALUES
(2, 'rown', 'Ron Gabriel', 'Ramos', 'rongabriel01.ramos@gmail.com', '$2y$10$3u9PoZjbLMPL/X.U.QQjue2DY25OU1Rlq/E5Pesa2nS.d3YWOZT8u', 'PH', 0, '2025-11-07 04:45:47', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_resource_progress`
--

CREATE TABLE `tbl_user_resource_progress` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `resourceID` int(11) NOT NULL,
  `isCompleted` tinyint(1) DEFAULT 0,
  `dateCompleted` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_resource_progress`
--

INSERT INTO `tbl_user_resource_progress` (`id`, `userID`, `resourceID`, `isCompleted`, `dateCompleted`) VALUES
(1, 5, 14, 0, '2025-11-11 14:53:20'),
(2, 5, 11, 0, '2025-11-11 14:53:41'),
(3, 5, 12, 0, '2025-11-11 14:53:58'),
(4, 5, 16, 0, '2025-11-11 15:16:45'),
(5, 5, 17, 0, '2025-11-11 15:16:49'),
(6, 5, 18, 0, '2025-11-11 15:16:49'),
(7, 5, 19, 0, '2025-11-11 15:16:51'),
(8, 5, 15, 0, '2025-11-11 15:17:09'),
(9, 5, 13, 0, '2025-11-11 15:17:58'),
(10, 5, 20, 0, '2025-11-11 15:35:32'),
(11, 5, 21, 0, '2025-11-14 19:56:49');

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
-- Indexes for table `tbl_goaltransactions`
--
ALTER TABLE `tbl_goaltransactions`
  ADD PRIMARY KEY (`goalTransactionID`);

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
-- Indexes for table `tbl_userbudgetrule`
--
ALTER TABLE `tbl_userbudgetrule`
  ADD PRIMARY KEY (`userBudgetRuleID`);

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
-- Indexes for table `tbl_user_resource_progress`
--
ALTER TABLE `tbl_user_resource_progress`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_achievements`
--
ALTER TABLE `tbl_achievements`
  MODIFY `achievementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_defaultcategories`
--
ALTER TABLE `tbl_defaultcategories`
  MODIFY `defaultCategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_expense`
--
ALTER TABLE `tbl_expense`
  MODIFY `expenseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_forecasts`
--
ALTER TABLE `tbl_forecasts`
  MODIFY `forecastID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_goaltransactions`
--
ALTER TABLE `tbl_goaltransactions`
  MODIFY `goalTransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_income`
--
ALTER TABLE `tbl_income`
  MODIFY `incomeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_recurringtransactions`
--
ALTER TABLE `tbl_recurringtransactions`
  MODIFY `recurringID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_resources`
--
ALTER TABLE `tbl_resources`
  MODIFY `resourceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `userAchievementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_userallocation`
--
ALTER TABLE `tbl_userallocation`
  MODIFY `userAllocationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_userbudgetrule`
--
ALTER TABLE `tbl_userbudgetrule`
  MODIFY `userBudgetRuleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_userbudgetversion`
--
ALTER TABLE `tbl_userbudgetversion`
  MODIFY `userBudgetversionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_usercategories`
--
ALTER TABLE `tbl_usercategories`
  MODIFY `userCategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_userlvl`
--
ALTER TABLE `tbl_userlvl`
  MODIFY `userLvlID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_user_resource_progress`
--
ALTER TABLE `tbl_user_resource_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
