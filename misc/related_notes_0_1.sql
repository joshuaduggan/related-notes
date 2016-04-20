-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2016 at 07:53 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `relatednotes`
--

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_category` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(101) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `is_category`, `name`, `description`) VALUES
(15, 0, 'PHP', 'A scripting language often employed as a HTML pre-processor. [url]//php.net[/url] is a great place to start looking. By far the most popular server side scripting language, due to it''s traditional use as part of the LAMP stack.'),
(17, 1, 'Dynamic Language', 'Dynamic languages are not compiled but rather interpreted at run-time. Because of this they often allow greater alteration of the language''s behavior than would be easily achievable with a compiled language. Scripting languages are all dynamic languages due to the fact that they are not pre compiled.'),
(18, 0, 'Bootstrap', 'Same type of technology as jQuery UI and can be thought of as a competitor though Bootstrap has more layout capabilities and is now thouroughly mobile first. Bootstrap''s most popular feature is it''s responsive grid layout system.'),
(19, 0, 'CSS', 'A language for describing HTML document styling. It''s safe to assume virtually all clients are CSS 2.1 aware. CSS 3 is an expanding group of CSS 2.1 extension modules and are only partially supported by the latest clients.'),
(20, 0, 'DOM', 'The Document Object Model is an instanced document object constructed by an HTML or XML interpreter for manipulation programatically. Javascript manipulation of a document is done via the DOM.'),
(21, 0, 'HTML', 'Base language of document content, structure, and style. The current version which can be expected across all devices is HTML 4.01. It''s successor; HTML5 is close to being considered the standard version (as of 6/9/14). Regardless of HTML5''s official status 4.01 will persist for a long time due to legacy clients.\r\nIE9 is the first IE version which is HTML5 compatible, because it is not supported on Windows XP most XP users do not use a HTML5 compatible browser. [url]http://netmarketshare.com[/url] reported in May 2014 that over 25% of desktop visitors used a version of IE that was 8 or older. [url]http://statcounter.com[/url] reported from March to May 2014 that 25% of web usage was mobile and 75% was desktop. If we assume all mobile usage is HTML5 aware and all desktop users not using an old version of IE are HTML5 aware we still only have 81% of clients HTML5 aware. For this reason as of June 2014 a website intended for a general audience should not require HTML5 awareness. Furthermore because HTML5 has been an evolving standard those clients that do support it will not all do so equally well.'),
(22, 0, 'Java', 'A strictly object oriented pre-compiled byte-code language. At one point Java apps were quite common on the Web, however persistent security flaws have greatly reduced it''s client-side use. It has remained popular for server-side use where it''s relative speed makes up for larger code size and development times than some dynamic languages such as Python.'),
(23, 1, 'Javascript', 'Originally developed to fill the role of a client-side scripting language to give logic and control to the web. Javascript is now used almost anywhere a modern scripting language can be usefull. The bare Javascript language is now very stable and consistently interpreted across clients. Challenges remain in how different clients build and allow manipulation of the DOM however.\r\n\r\nJavascript is often used with extensions such as jQuery which greatly increase the language''s capabilities at the potential cost for increased incompatibility.'),
(24, 0, 'jQuery', 'A very widely used Javascript extension library. It can be thought of as a compilation of thouroughly tested "hacks" to add functionality and ease-of-use to Javascript programming and DOM manipulation.\r\n\r\njQuery is used by linking to a library file of jQuery functions in you HTML document then using it as needed in your custom scripts.'),
(25, 0, 'jQuery UI', 'UI components and systems which make use of and are closely tied with the base jQuery library. Just like other web standard libraries these are made with HTML, Javascript and CSS, and are employed by linking to library files in your HTML document that makes use of them.'),
(26, 0, 'Media Query', 'An enableing technology of Responsive Design, it''s used in CSS to select the appropriate styles for the current client. The complete specification is a module of CSS3, though it''s most often used capabilities are also implemented in some CSS2 clients - a notable exception being IE 8 and prior. Commonly sites that make use of media queries provide an entirely different styling system for IE 8 due to this incompatibility.'),
(27, 0, 'Mobile First', 'The term "mobile first" is a differentiator from the traditional method of web development which has been to build for a desktop visitor then expand access to include edge cases like mobile visitors. Mobile first is a philosophy of creating a great UI/UX for mobile and then adding and refining for desktop as needed.'),
(28, 0, 'Python', 'A widely used scripting language on web servers. Python''s heavy emphasis on code readability and adaptable nature has resulted in a rapidly expanding adoption over the past few years. It''s main competitors are PHP and Ruby, which are each capable filling the same role.'),
(29, 1, 'Responsive Design', 'Automatic styling and sizing of document content based on client screen dimensions. Responsive Design is commonly accomplished using Media Queries and applying CSS to it''s best effect for the current client.\r\nBootstrap provides good layout frameworks for responsive site design.'),
(30, 0, 'UI / UX', 'User Interface / User Experience design. A term or job description meant to widen the traditional focus on user interface components to a fuller study of the user''s overal perception of and success using a website.'),
(31, 0, 'URI', 'A Uniform Resource Identifier can be a URL or a URN (or both).'),
(32, 0, 'URL', 'A Uniform Resource Locator is a URI that provides the location of a specific resource and the method for obtaining it. They are often used to direct an app to content (a web browser to a HTML page).'),
(33, 0, 'URN', 'A Uniform Resource Name is a URI that names a resource but does not provide information on it''s location or how to retrieve it. They are often used to define a namespace.'),
(34, 0, 'XML', 'A document markup language, can be thought of as just like HTML but without any of the implied meaning. Comonly used to serialize and/or store structured data. Usually programatical interaction with XML is accomplished via a DOM just like HTML.'),
(35, 0, 'Ruby', ''),
(36, 0, 'LAMP', ''),
(37, 0, 'Wordpress', ''),
(38, 0, 'CGI', ''),
(39, 0, 'Zend', 'A company that proves popular PHP web app frameworks as well as an IDE.'),
(40, 0, 'XSS - Cross Site Scripting', ''),
(41, 1, 'Client Side Web Language', 'Program language executed by the web browser.'),
(42, 1, 'Server Side Web Language', ''),
(43, 1, 'Acronym', ''),
(44, 1, 'SSO - Single Sign-On', ''),
(45, 0, 'Oauth', ''),
(46, 0, 'Cookies', '');

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE IF NOT EXISTS `relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relationship_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

--
-- Dumping data for table `relationships`
--

INSERT INTO `relationships` (`id`, `relationship_id`, `note_id`) VALUES
(27, 27, 43),
(28, 27, 15),
(29, 29, 43),
(30, 29, 19),
(31, 31, 43),
(32, 31, 20),
(35, 35, 43),
(36, 35, 30),
(37, 37, 43),
(38, 37, 31),
(39, 39, 43),
(40, 39, 32),
(41, 41, 43),
(42, 41, 33),
(43, 43, 43),
(44, 43, 34),
(45, 45, 43),
(46, 45, 36),
(47, 47, 43),
(48, 47, 40),
(49, 49, 38),
(50, 49, 43),
(57, 57, 42),
(58, 57, 15),
(59, 59, 42),
(60, 59, 22),
(61, 61, 42),
(62, 61, 28),
(63, 63, 42),
(64, 63, 35),
(65, 65, 39),
(66, 65, 15),
(69, 69, 44),
(70, 69, 43),
(73, 73, 45),
(74, 73, 43),
(75, 75, 45),
(76, 75, 44),
(79, 79, 21),
(80, 79, 43),
(81, 81, 41),
(82, 81, 19),
(83, 83, 41),
(84, 83, 21),
(85, 85, 41),
(86, 85, 22),
(87, 87, 41),
(88, 87, 24),
(89, 89, 41),
(90, 89, 25);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userEmail` varchar(255) NOT NULL,
  `userPassHash` varchar(255) NOT NULL,
  `availableTime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userEmail`, `userPassHash`, `availableTime`) VALUES
(2, 'jdoe@mail.com', '$2y$10$zx0dm/3vWvs5ArpPGobwBufe/SgX7InEvPLtX3RE7pRmiqFeBCzGO', 1460569605);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
