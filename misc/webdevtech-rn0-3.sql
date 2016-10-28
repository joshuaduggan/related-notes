-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2016 at 08:11 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `related_notes_0_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `name` varchar(101) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `name`, `description`) VALUES
(15, 'PHP', 'A scripting language often employed as a HTML pre-processor. [url]//php.net[/url] is a great place to start looking. By far the most popular server side scripting language, due to it''s traditional use as part of the LAMP stack.'),
(17, 'Dynamic Language', '!!!REFINEMENT NEEDED!!! Dynamic languages are usually not pre-compiled but rather interpreted at run-time. Because of this they often allow greater alteration of the language''s behavior than would be easily achievable with a compiled language.'),
(18, 'Bootstrap', 'Same type of technology as jQuery UI and can be thought of as a competitor though Bootstrap has more layout capabilities and is now thouroughly mobile first. Bootstrap''s most popular feature is it''s responsive grid layout system.'),
(19, 'CSS', 'A language for describing HTML document styling. It''s safe to assume virtually all clients are CSS 2.1 aware. CSS 3 is an expanding group of CSS 2.1 extension modules and are only partially supported by the latest clients.'),
(20, 'DOM', 'The Document Object Model is an instanced document object constructed by an HTML or XML interpreter for manipulation programatically. Javascript manipulation of a document is done via the DOM.'),
(21, 'HTML', 'Base language of document content, structure, and style. The current version which can be expected across all devices is HTML 4.01. It''s successor; HTML5 is close to being considered the standard version (as of 6/9/14). Regardless of HTML5''s official status 4.01 will persist for a long time due to legacy clients.\r\nIE9 is the first IE version which is HTML5 compatible, because it is not supported on Windows XP most XP users do not use a HTML5 compatible browser. [url]http://netmarketshare.com[/url] reported in May 2014 that over 25% of desktop visitors used a version of IE that was 8 or older. [url]http://statcounter.com[/url] reported from March to May 2014 that 25% of web usage was mobile and 75% was desktop. If we assume all mobile usage is HTML5 aware and all desktop users not using an old version of IE are HTML5 aware we still only have 81% of clients HTML5 aware. For this reason as of June 2014 a website intended for a general audience should not require HTML5 awareness. Furthermore because HTML5 has been an evolving standard those clients that do support it will not all do so equally well.'),
(22, 'Java', 'A strictly object oriented pre-compiled byte-code language. At one point Java apps were quite common on the Web, however persistent security flaws have greatly reduced it''s client-side use. It has remained popular for server-side use where it''s relative speed makes up for larger code size and development times than some dynamic languages such as Python.'),
(23, 'Javascript', 'Originally developed to fill the role of a client-side scripting language to give logic and control to the web. Javascript is now used almost anywhere a modern scripting language can be usefull. The bare Javascript language is now very stable and consistently interpreted across clients. Challenges remain in how different clients build and allow manipulation of the DOM however.\r\n\r\nJavascript is often used with extensions such as jQuery which greatly increase the language''s capabilities at the potential cost for increased incompatibility.'),
(24, 'jQuery', 'A very widely used Javascript extension library. It can be thought of as a compilation of thouroughly tested "hacks" to add functionality and ease-of-use to Javascript programming and DOM manipulation.\r\n\r\njQuery is used by linking to a library file of jQuery functions in you HTML document then using it as needed in your custom scripts.'),
(25, 'jQuery UI', 'UI components and systems which make use of and are closely tied with the base jQuery library. Just like other web standard libraries these are made with HTML, Javascript and CSS, and are employed by linking to library files in your HTML document that makes use of them.'),
(26, 'Media Query', 'An enableing technology of Responsive Design, it''s used in CSS to select the appropriate styles for the current client. The complete specification is a module of CSS3, though it''s most often used capabilities are also implemented in some CSS2 clients - a notable exception being IE 8 and prior. Commonly sites that make use of media queries provide an entirely different styling system for IE 8 due to this incompatibility.'),
(27, 'Mobile First', 'The term "mobile first" is a differentiator from the traditional method of web development which has been to build for a desktop visitor then expand access to include edge cases like mobile visitors. Mobile first is a philosophy of creating a great UI/UX for mobile and then adding and refining for desktop as needed.'),
(28, 'Python', 'A widely used scripting language on web servers. Python''s heavy emphasis on code readability and adaptable nature has resulted in a rapidly expanding adoption over the past few years. It''s main competitors are PHP and Ruby, which are each capable filling the same role.'),
(29, 'Responsive Design', 'Automatic styling and sizing of document content based on client screen dimensions. Responsive Design is commonly accomplished using Media Queries and applying CSS to it''s best effect for the current client.\r\nBootstrap provides good layout frameworks for responsive site design.'),
(30, 'UI / UX', 'User Interface / User Experience design. A term or job description meant to widen the traditional focus on user interface components to a fuller study of the user''s overal perception of and success using a website.'),
(31, 'URI', 'A Uniform Resource Identifier can be a URL or a URN (or both).'),
(32, 'URL', 'A Uniform Resource Locator is a URI that provides the location of a specific resource and the method for obtaining it. They are often used to direct an app to content (a web browser to a HTML page).'),
(33, 'URN', 'A Uniform Resource Name is a URI that names a resource but does not provide information on it''s location or how to retrieve it. They are often used to define a namespace.'),
(34, 'XML', 'A document markup language, can be thought of as just like HTML but without any of the implied meaning. Comonly used to serialize and/or store structured data. Usually programatical interaction with XML is accomplished via a DOM just like HTML.'),
(35, 'Ruby', ''),
(36, 'LAMP', 'The most popular platform for web applications. Refers to Linux, Apache, MySQL, Perl (or PHP, Python, or Ruby).'),
(37, 'Wordpress', 'The most popular blogging software and an excellent (though complex) example of what LAMP is capable of. Wordpress is both a freely available web app and blog hosting service. [url]http://wordpress.org[/url] is the central site for the free and open-source software. [url]http://wordpress.com[/url] is a for profit blog and domain hosting company which offers free entry level services.'),
(38, 'CGI', 'Common Gateway Interface is the original standard for allowing programmatic responses to HTTP requests. CGI is a standard that specifies the execution environment that a web server should provide and what to do with the program''s output. While capable of being used with virtually all languages (both interpreted and native) CGI was most commonly used with PERL. The Achilles heel of CGI was it''s requirement for a brand new process (and interpreter load, and script interpretation) for each HTTP request. It was superseded by FastCGI which reduces the required overhead, but which itself was overtaken by even more efficient frameworks that are more closely tied to their individual web server - such as Apache''s module system. However CGI lives on in that most modern frameworks continue to emulate it''s principles and nomenclature.'),
(39, 'Zend', 'A company that proves popular PHP web app frameworks as well as an IDE.'),
(40, 'XSS - Cross Site Scripting', '!!!REFINEMENT NEEDED!!! Statements inserted into a website by a malicious 3rd party, often making use of input forms on the targeted website to submit data that''s later displayed to it''s visitors. '),
(41, 'Client Side Web Language', 'A programming language that''s often used in the client web browser.'),
(42, 'Server Side Web Language', 'A programming language that''s often used on the web server.'),
(43, 'Acronym', ''),
(44, 'SSO - Single Sign-On', 'Enables website visitors to identify themselves via a third party (often a large company like Google or Facebook). The benefit being users don''t have to create separate login identities for each web-app they use. The current standard in greatest use is Oauth 2.0. Unfortunately the standard doesn''t contain many implementation details and therefore each SSO provider uses a unique API that requires individual attention from a web developer.'),
(46, 'Cookie', 'A small piece of data that is shared between client and server. This data is often no more than minimal property/value pairs, the critical aspect being that the data is stored on the client and returned to the originating server on subsequent HTTP requests. What this enables is the voluntary identification of the client by the server, making the server capable of maintaining the client''s state between visits.'),
(48, 'Security', ''),
(49, 'Jetpack', 'A Wordpress plugin that allows a self-hosted wordpress.org blog access to many of the features provided through wordpress.com. This  is a massive plugin that has many modules. The primary goal of Jetpack is to extend wordpress.com''s social features to the broader wordpress.org installations across the net.'),
(50, 'D3', 'Framework to build data driven visualizations (hypercharged charts). [url]http://d3js.org[/url]'),
(51, 'SVG', 'Scalable Vector Graphics; Vector graphics described in XML and manipulable via a DOM.'),
(52, 'GIT', 'Popular version control system.'),
(53, 'Development Tools', ''),
(54, 'Microsoft', ''),
(55, 'Sharepoint', 'A software product which provides back-end services support and document management for Microsoft''s business productivity tools. Current versions are Sharepoint 2013 and Sharepoint Online which is purely a cloud offering.'),
(56, 'Cloud Computing', 'Server availability or specific online services provided over the Internet. The prime benefit being that there is usually no purchase or management of a physical server required by the user.'),
(57, 'Azure', 'Microsoft''s cloud computing pure server offering. Available directly as a VM service but perhaps more commonly used as the backbone for most of Microsoft''s other cloud computing products such as Sharepoint, Lync and Office 365.'),
(58, 'Lync', 'Microsoft''s business oriented messaging/voice/video communication system - Skype for business.'),
(59, 'Office 365', 'A cloud version of Microsoft Office. Currently provides a subset of features to Office 2013, but is expected to eventually fully replace the more traditional "boxed" Office. It''s direct competitor is Google Apps.'),
(60, 'Related Notes Home', 'This is the "home" note of Related Notes. Gives a place to enter the app and relates to other "entry point" notes in this database.'),
(61, 'Web Application', '');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `name` varchar(101) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `name`, `value`) VALUES
(1, 'installation_name', 'Web Development Technologies'),
(2, 'home_note_id', '60');

-- --------------------------------------------------------

--
-- Table structure for table `rel_cores`
--

CREATE TABLE `rel_cores` (
  `id` int(11) NOT NULL,
  `rel_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rel_cores`
--

INSERT INTO `rel_cores` (`id`, `rel_type`) VALUES
(27, 2),
(29, 2),
(31, 2),
(33, 2),
(35, 2),
(37, 2),
(39, 2),
(41, 2),
(43, 2),
(65, 1),
(85, 2),
(87, 2),
(119, 2),
(121, 2),
(123, 2),
(125, 2),
(127, 2),
(129, 2),
(131, 2),
(133, 2),
(147, 2),
(151, 2),
(167, 2),
(169, 2),
(177, 2),
(179, 2),
(183, 2),
(185, 1),
(187, 2),
(201, 2),
(207, 2),
(231, 2),
(235, 2),
(239, 2),
(241, 2),
(243, 2),
(245, 2),
(246, 3),
(247, 3),
(248, 3),
(249, 3),
(250, 3),
(251, 3),
(252, 4),
(253, 4),
(261, 2),
(262, 3),
(263, 4),
(265, 4),
(271, 4),
(272, 4),
(273, 4),
(274, 4);

-- --------------------------------------------------------

--
-- Table structure for table `rel_legs`
--

CREATE TABLE `rel_legs` (
  `id` int(11) NOT NULL,
  `rel_core` int(11) NOT NULL,
  `note` int(11) NOT NULL,
  `role` varchar(101) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rel_legs`
--

INSERT INTO `rel_legs` (`id`, `rel_core`, `note`, `role`) VALUES
(27, 27, 43, 'parent'),
(28, 27, 15, 'child'),
(29, 29, 43, 'parent'),
(30, 29, 19, 'child'),
(31, 31, 43, 'parent'),
(32, 31, 20, 'child'),
(33, 33, 43, 'parent'),
(34, 33, 21, 'child'),
(35, 35, 43, 'parent'),
(36, 35, 30, 'child'),
(37, 37, 43, 'parent'),
(38, 37, 31, 'child'),
(39, 39, 43, 'parent'),
(40, 39, 32, 'child'),
(41, 41, 43, 'parent'),
(42, 41, 33, 'child'),
(43, 43, 43, 'parent'),
(44, 43, 34, 'child'),
(65, 65, 39, ''),
(66, 65, 15, ''),
(85, 85, 36, 'parent'),
(86, 85, 43, 'parent'),
(87, 87, 36, 'parent'),
(88, 87, 15, 'child'),
(119, 119, 38, 'child'),
(120, 119, 43, 'parent'),
(121, 121, 42, 'parent'),
(122, 121, 15, 'child'),
(123, 123, 42, 'parent'),
(124, 123, 22, 'child'),
(125, 125, 42, 'parent'),
(126, 125, 28, 'child'),
(127, 127, 42, 'parent'),
(128, 127, 35, 'child'),
(129, 129, 41, 'parent'),
(130, 129, 19, 'child'),
(131, 131, 41, 'parent'),
(132, 131, 21, 'child'),
(133, 133, 41, 'parent'),
(134, 133, 22, 'child'),
(147, 147, 44, 'child'),
(148, 147, 43, 'parent'),
(151, 151, 37, 'parent'),
(152, 151, 36, 'parent'),
(167, 167, 40, 'child'),
(168, 167, 43, 'parent'),
(169, 169, 40, 'child'),
(170, 169, 48, 'parent'),
(177, 177, 50, 'child'),
(178, 177, 23, 'parent'),
(179, 179, 50, 'child'),
(180, 179, 41, 'parent'),
(183, 183, 51, 'child'),
(184, 183, 41, 'parent'),
(185, 185, 51, ''),
(186, 185, 50, ''),
(187, 187, 53, 'parent'),
(188, 187, 52, 'child'),
(201, 201, 55, 'child'),
(202, 201, 54, 'parent'),
(207, 207, 58, 'child'),
(208, 207, 54, 'parent'),
(231, 231, 59, 'child'),
(232, 231, 54, 'parent'),
(235, 235, 56, 'parent'),
(236, 235, 55, 'child'),
(239, 239, 56, 'parent'),
(240, 239, 58, 'child'),
(241, 241, 56, 'parent'),
(242, 241, 59, 'child'),
(243, 243, 57, 'child'),
(244, 243, 54, 'parent'),
(245, 245, 57, 'child'),
(246, 245, 56, 'parent'),
(247, 246, 60, 'parent'),
(248, 246, 43, 'child'),
(249, 247, 60, 'parent'),
(250, 247, 41, 'child'),
(251, 248, 60, 'parent'),
(252, 248, 42, 'child'),
(253, 249, 60, 'parent'),
(254, 249, 56, 'child'),
(255, 250, 60, 'parent'),
(256, 250, 53, 'child'),
(257, 251, 60, 'parent'),
(258, 251, 29, 'child'),
(273, 261, 61, 'parent'),
(274, 261, 37, 'child'),
(275, 262, 60, 'parent'),
(276, 262, 61, 'child'),
(277, 263, 18, 'parent'),
(278, 263, 23, 'child'),
(281, 265, 49, 'parent'),
(282, 265, 37, 'child'),
(293, 271, 15, 'parent'),
(294, 271, 37, 'child'),
(295, 272, 19, 'parent'),
(296, 272, 37, 'child'),
(297, 273, 21, 'parent'),
(298, 273, 37, 'child'),
(299, 274, 24, 'parent'),
(300, 274, 37, 'child');

-- --------------------------------------------------------

--
-- Table structure for table `rel_types`
--

CREATE TABLE `rel_types` (
  `id` int(11) NOT NULL,
  `structure` varchar(101) NOT NULL,
  `name` varchar(101) NOT NULL,
  `purpose` varchar(101) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rel_types`
--

INSERT INTO `rel_types` (`id`, `structure`, `name`, `purpose`) VALUES
(1, 'one-one', 'Generic', ''),
(2, 'one-many', 'Category', 'is'),
(3, 'one-many', 'Home', 'linked from'),
(4, 'one-many', 'Uses Language', 'uses');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_pass_hash` varchar(255) NOT NULL,
  `available_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_email`, `user_pass_hash`, `available_time`) VALUES
(2, 'jdoe@mail.com', '$2y$10$zx0dm/3vWvs5ArpPGobwBufe/SgX7InEvPLtX3RE7pRmiqFeBCzGO', 1477673508);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `rel_cores`
--
ALTER TABLE `rel_cores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rel_legs`
--
ALTER TABLE `rel_legs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rel_types`
--
ALTER TABLE `rel_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;
--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `rel_cores`
--
ALTER TABLE `rel_cores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;
--
-- AUTO_INCREMENT for table `rel_legs`
--
ALTER TABLE `rel_legs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;
--
-- AUTO_INCREMENT for table `rel_types`
--
ALTER TABLE `rel_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
