-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 10, 2023 at 10:41 AM
-- Server version: 5.7.40
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `video`
--

-- --------------------------------------------------------

--
-- Table structure for table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE IF NOT EXISTS `commentaire` (
  `id_commentaire` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contenu` text NOT NULL,
  `fk_user` int(11) NOT NULL,
  `fk_video` int(11) NOT NULL,
  PRIMARY KEY (`id_commentaire`),
  KEY `commentaire_user` (`fk_user`),
  KEY `commentaire_video` (`fk_video`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `commentaire`
--

INSERT INTO `commentaire` (`id_commentaire`, `date`, `contenu`, `fk_user`, `fk_video`) VALUES
(1, '2023-08-09 16:16:35', 'what happened to our country, amirite ?', 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(30) NOT NULL,
  `name` varchar(175) NOT NULL,
  `email` varchar(175) NOT NULL,
  `mot_de_passe` varchar(175) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `role`, `name`, `email`, `mot_de_passe`) VALUES
(1, 'guest', 'Coyote', 'wileecoyote@acme.com', 'acme'),
(2, 'guest', 'Marge', 'marge@20', 'homer'),
(3, 'guest', 'Lorem', 'lorem@ipsum', 'lorem'),
(4, 'guest', 'Mr', 'mrbean@uk.co.uk', 'bean'),
(5, 'guest', 'admin', 'admin@voyage', '08b16d6f');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id_video` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL,
  `titre` varchar(90) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `path` varchar(175) DEFAULT NULL,
  `resume` text NOT NULL,
  `is_visible` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_video`),
  KEY `ct_video_user` (`fk_user`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id_video`, `fk_user`, `titre`, `date`, `path`, `resume`, `is_visible`) VALUES
(1, 3, 'rapture', '2023-08-09 00:00:00', 'lorem_life.mp4', 'come...', 1),
(2, 3, 'lorem', '2023-08-01 00:00:00', 'lorem.mp4', 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Perferendis suscipit tempore eligendi vero nesciunt, esse corporis porro impedit iure perspiciatis molestiae aspernatur libero commodi, iste ipsam, ducimus pariatur beatae? Totam.', 1),
(3, 1, 'ACME review.', '2022-08-10 00:00:00', 'wile_discuss_acme_latest_products.mp4', 'I am disappointed with the recent quality of ACME products.', 1),
(4, 4, 'I recently bought a bike.', '2013-08-01 00:00:00', '404b.mp4', 'I am training for a competition next month.', 1),
(5, 2, 'homer eating seafood', '2023-08-31 00:00:00', 'marge_homer_seafood.mp4', 'What the title says.', 1),
(6, 2, 'Krump Krump Krump !', '2023-08-06 00:00:00', 'marge_krumping.mp4', 'check the moves.', 1),
(9, 4, 'driving', '2023-07-18 00:00:00', 'mrbean_driving.mp4', 'I did not get hurt.', 1),
(10, 4, 'I received a trophy', '2023-08-31 00:00:00', 'mrbean_button.mp4', '<p>yay ! just trying this out. works? xcwxcsdfsfsfd fdg</p><p><strong>yay!</strong></p><p>finally!</p>', 1),
(11, 1, 'Review 2', '2023-08-22 00:00:00', 'wile_jet.mp4', 'you see.', 1),
(12, 1, 'catching the road runner', '2023-08-28 00:00:00', 'wile_catching_roadrunner.mp4', 'you guys always wanted me to catch him.\r\nnow what ?', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `commentaire_user` FOREIGN KEY (`fk_user`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `commentaire_video` FOREIGN KEY (`fk_video`) REFERENCES `videos` (`id_video`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `ct_video_user` FOREIGN KEY (`fk_user`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
