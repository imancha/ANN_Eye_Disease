-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 04, 2014 at 01:27 AM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ANN`
--

-- --------------------------------------------------------

--
-- Table structure for table `Eye Disease`
--

CREATE TABLE IF NOT EXISTS `Eye Disease` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `signs` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `Eye Disease`
--

INSERT INTO `Eye Disease` (`id`, `name`, `signs`) VALUES
(1, 'Glaucoma', '1 2 5 6 7'),
(2, 'Macular Degeneration', '5 8 10 12'),
(3, 'Pink Eye (conjunctivitis)', '2 8 13 17'),
(4, 'Uveitis', '1 2 8 9 14 16 20'),
(5, 'Corneal Ulcer', '1 2 8 11 17 18 20'),
(6, 'Keratoconus', '8 10 12'),
(7, 'Blepharitis', '1 2 8 13 18 20'),
(8, 'Astigmatism', '3 5 8 19');

-- --------------------------------------------------------

--
-- Table structure for table `Symptoms`
--

CREATE TABLE IF NOT EXISTS `Symptoms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `Symptoms`
--

INSERT INTO `Symptoms` (`id`, `name`) VALUES
(1, 'Pains in the eye'),
(2, 'Redness or pink color of eye'),
(3, 'Bright light or antiglare sunglasses improves vision'),
(4, 'Poor night vision'),
(5, 'Family histories of the eye problem'),
(6, 'Decrease in peripheral field of view'),
(7, 'Age greater than 45 years'),
(8, 'Blurred vision'),
(9, 'Blurred vision improves with eye blinking'),
(10, 'Distorted vision'),
(11, 'Cloudy substance formed in front of eye lens'),
(12, 'Slow recovery of vision after exposure to bright light'),
(13, 'Irritation, itchy, scratchy or burning sensation of eye'),
(14, 'Discomfort after long concentration use of eye'),
(15, 'Trouble discerning colors'),
(16, 'Floaters in eye, flashes of light, halos around light'),
(17, 'Watering or discharge from eye'),
(18, 'Swelling of eye'),
(19, 'Steamy appearing cornea of eye'),
(20, 'Sensitivity to light (photophobia)'),
(21, 'Blurred vision for distant objects'),
(22, 'Blurred vision for close objects');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
