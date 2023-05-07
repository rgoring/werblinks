SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `werblinks`
--
CREATE DATABASE IF NOT EXISTS `werblinks` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `werblinks`;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `groupid` int NOT NULL,
  `userid` int NOT NULL,
  `workspaceid` int NOT NULL,
  `xpos` int NOT NULL,
  `ypos` int NOT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `htmlobjs`
--

CREATE TABLE `htmlobjs` (
  `htmlid` int NOT NULL,
  `userid` int NOT NULL,
  `workspaceid` int NOT NULL,
  `script` tinyint(1) NOT NULL DEFAULT '0',
  `xpos` int NOT NULL,
  `ypos` int NOT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `html` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `linkid` int NOT NULL,
  `userid` int NOT NULL,
  `groupid` int NOT NULL,
  `workspaceid` int NOT NULL,
  `position` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int NOT NULL,
  `email` varchar(300) NOT NULL,
  `password` varchar(256) NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `lastlogin` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `workspaces`
--

CREATE TABLE `workspaces` (
  `workspaceid` int NOT NULL,
  `userid` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`groupid`);

--
-- Indexes for table `htmlobjs`
--
ALTER TABLE `htmlobjs`
  ADD PRIMARY KEY (`htmlid`),
  ADD KEY `userid` (`userid`,`workspaceid`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`linkid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `workspaces`
--
ALTER TABLE `workspaces`
  ADD PRIMARY KEY (`workspaceid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `groupid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `htmlobjs`
--
ALTER TABLE `htmlobjs`
  MODIFY `htmlid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `linkid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workspaces`
--
ALTER TABLE `workspaces`
  MODIFY `workspaceid` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
