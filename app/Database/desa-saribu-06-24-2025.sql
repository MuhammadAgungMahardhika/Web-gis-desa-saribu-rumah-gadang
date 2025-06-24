-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: MYSQL1002.site4now.net    Database: db_ab830d_desasar
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `atraction`
--

DROP TABLE IF EXISTS `atraction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atraction` (
  `id` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `open` time NOT NULL,
  `close` time NOT NULL,
  `geom` geometry DEFAULT NULL,
  `cp` varchar(15) DEFAULT NULL,
  `price_ticket` int DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atraction`
--

LOCK TABLES `atraction` WRITE;
/*!40000 ALTER TABLE `atraction` DISABLE KEYS */;
INSERT INTO `atraction` VALUES ('A01','Menara Songket','Jl. Batang Labuah, Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','09:00:00','17:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿','81378249309',10000,'Menara Songket ini adalah sebuah menara yang dibangun pemerintah setempat di kawasan Seribu Rumah Gadang. Objek Wisata Menara Songket berada Nagari Koto Baru, Kecamatan Sungai Pagu, Kabupaten Solok Selatan Sumbar, 31 KM dari pusat ibu kota Solok Selatan. ',NULL,-1.48159100,101.05816300,'2023-10-19 13:54:11','2023-10-29 17:46:50'),('A02','Jembatan Merah','Jl. Batang Labuah, Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','09:00:00','17:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿','81378249309',NULL,'','',-1.48231400,101.05668900,'2023-10-19 13:54:12','2023-10-19 13:54:12'),('A03','Galeri Saribu Rumah Gadang','Jl. Batang Labuah, Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','09:00:00','17:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿','81378249309',NULL,'','',-1.48186400,101.05589000,'2023-10-19 13:54:12','2023-10-19 13:54:12'),('A04','Lapangan Hijau Koto Baru','Koto Baru, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','09:00:00','17:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿','81378249309',NULL,'','',-1.48177400,101.05771800,'2023-10-19 13:54:12','2023-10-19 13:54:12'),('A05','Kuburan Dt. Rajo Batuah','Jl. Batang Labuah, Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','09:00:00','17:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿','81378249309',NULL,'','',-1.48256400,101.05825300,'2023-10-19 13:54:12','2023-10-19 13:54:12'),('A06','Surau Menara Desa Lubuk Jaya','Koto Baru, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','09:00:00','17:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿','81378249309',NULL,'Suray','',-1.48264100,101.05854700,'2023-10-19 13:54:12','2023-10-19 13:54:12');
/*!40000 ALTER TABLE `atraction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atraction_facility`
--

DROP TABLE IF EXISTS `atraction_facility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atraction_facility` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atraction_facility`
--

LOCK TABLES `atraction_facility` WRITE;
/*!40000 ALTER TABLE `atraction_facility` DISABLE KEYS */;
INSERT INTO `atraction_facility` VALUES ('AF01','Facility 1','2023-10-17 13:49:00','2023-10-17 16:31:03'),('AF02','Facility 2','2023-10-17 13:49:00','2023-10-17 16:30:56'),('AF03','Facility 33','2023-10-17 16:30:37','2023-10-18 02:45:00');
/*!40000 ALTER TABLE `atraction_facility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atraction_gallery`
--

DROP TABLE IF EXISTS `atraction_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atraction_gallery` (
  `id` varchar(10) NOT NULL,
  `id_atraction` varchar(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `atraction_gallery_id_atraction_foreign` (`id_atraction`),
  CONSTRAINT `atraction_gallery_id_atraction_foreign` FOREIGN KEY (`id_atraction`) REFERENCES `atraction` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atraction_gallery`
--

LOCK TABLES `atraction_gallery` WRITE;
/*!40000 ALTER TABLE `atraction_gallery` DISABLE KEYS */;
INSERT INTO `atraction_gallery` VALUES ('01','A01','a1a.jpg','2023-12-11 02:49:36','2023-12-11 02:49:36'),('02','A01','a1b.jpg','2023-12-11 02:49:36','2023-12-11 02:49:36'),('03','A01','a1c.jpg','2023-12-11 02:49:36','2023-12-11 02:49:36'),('04','A01','a1d.jpg','2023-12-11 02:49:36','2023-12-11 02:49:36'),('05','A04','a4a.jpg','2023-12-11 02:49:36','2023-12-11 02:49:36'),('06','A04','a4b.jpg','2023-12-11 02:49:36','2023-12-11 02:49:36');
/*!40000 ALTER TABLE `atraction_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_activation_attempts`
--

DROP TABLE IF EXISTS `auth_activation_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_activation_attempts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_activation_attempts`
--

LOCK TABLES `auth_activation_attempts` WRITE;
/*!40000 ALTER TABLE `auth_activation_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_activation_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_groups`
--

DROP TABLE IF EXISTS `auth_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_groups`
--

LOCK TABLES `auth_groups` WRITE;
/*!40000 ALTER TABLE `auth_groups` DISABLE KEYS */;
INSERT INTO `auth_groups` VALUES (1,'admin','Site Administrator'),(2,'user','Reguler User'),(3,'owner','Owner');
/*!40000 ALTER TABLE `auth_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_groups_permissions`
--

DROP TABLE IF EXISTS `auth_groups_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_groups_permissions` (
  `group_id` int unsigned NOT NULL DEFAULT '0',
  `permission_id` int unsigned NOT NULL DEFAULT '0',
  KEY `auth_groups_permissions_permission_id_foreign` (`permission_id`),
  KEY `group_id_permission_id` (`group_id`,`permission_id`),
  CONSTRAINT `auth_groups_permissions_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auth_groups_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_groups_permissions`
--

LOCK TABLES `auth_groups_permissions` WRITE;
/*!40000 ALTER TABLE `auth_groups_permissions` DISABLE KEYS */;
INSERT INTO `auth_groups_permissions` VALUES (1,1),(1,2),(2,2),(3,3);
/*!40000 ALTER TABLE `auth_groups_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_groups_users`
--

DROP TABLE IF EXISTS `auth_groups_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_groups_users` (
  `group_id` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned NOT NULL DEFAULT '0',
  KEY `auth_groups_users_user_id_foreign` (`user_id`),
  KEY `group_id_user_id` (`group_id`,`user_id`),
  CONSTRAINT `auth_groups_users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auth_groups_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_groups_users`
--

LOCK TABLES `auth_groups_users` WRITE;
/*!40000 ALTER TABLE `auth_groups_users` DISABLE KEYS */;
INSERT INTO `auth_groups_users` VALUES (1,4),(2,2),(3,1);
/*!40000 ALTER TABLE `auth_groups_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_logins`
--

DROP TABLE IF EXISTS `auth_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_logins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `date` datetime NOT NULL,
  `success` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_logins`
--

LOCK TABLES `auth_logins` WRITE;
/*!40000 ALTER TABLE `auth_logins` DISABLE KEYS */;
INSERT INTO `auth_logins` VALUES (1,'::1','ranggiaureliyanto@gmail.com',NULL,'2023-08-28 03:40:13',0),(2,'::1','accadmin1',NULL,'2023-08-28 03:41:45',0),(3,'::1','accadmin1',NULL,'2023-08-28 03:41:57',0),(4,'::1','ranggiaureliyanto@gmail.com',1,'2023-08-28 03:53:49',1),(5,'::1','ranggiaureliyanto@gmail.com',1,'2023-08-28 03:55:46',1),(6,'::1','ranggiaureliyanto@gmail.com',1,'2023-08-28 03:58:22',1),(7,'::1','ranggiaureliyanto@gmail.com',1,'2023-08-30 05:56:19',1),(8,'::1','m.agungmahardika12@gmail.com',2,'2023-09-11 22:29:08',1),(9,'::1','m.agungmahardika12@gmail.com',NULL,'2023-09-14 22:02:31',0),(10,'::1','m.agungmahardika12@gmail.com',2,'2023-09-14 22:02:38',1),(11,'::1','m.agungmahardika12@gmail.com',2,'2023-09-15 07:25:50',1),(12,'::1','me@domain.com',NULL,'2023-09-15 07:27:25',0),(13,'::1','me@domain.com',NULL,'2023-09-15 07:27:31',0),(14,'::1','me@gmail.com',NULL,'2023-09-15 07:27:44',0),(15,'::1','me@mydomain.com',NULL,'2023-09-15 07:29:50',0),(16,'::1','me@mydomain.com',NULL,'2023-09-15 07:30:04',0),(17,'::1','me@gmail.com',4,'2023-09-15 07:30:39',1),(18,'::1','m.agungmahardika12@gmail.com',2,'2023-09-15 07:31:28',1),(19,'::1','m.agungmahardika12@gmail.com',NULL,'2023-09-27 07:41:06',0),(20,'::1','m.agungmahardika12@gmail.com',2,'2023-09-27 07:41:16',1),(21,'::1','m.agungmahardika12@gmail.com',2,'2023-09-27 22:33:11',1),(22,'::1','m.agungmahardika12@gmail.com',2,'2023-09-28 09:20:50',1),(23,'::1','m.agungmahardika12@gmail.com',2,'2023-09-28 21:40:32',1),(24,'::1','m.agungmahardika12@gmail.com',NULL,'2023-09-30 07:31:59',0),(25,'::1','m.agungmahardika12@gmail.com',NULL,'2023-09-30 07:32:04',0),(26,'::1','m.agungmahardika12@gmail.com',2,'2023-09-30 07:32:10',1),(27,'::1','m.agungmahardika12@gmail.com',2,'2023-09-30 20:54:03',1),(28,'::1','m.agungmahardika12@gmail.com',2,'2023-09-30 22:44:26',1),(29,'::1','m.agungmahardika12@gmail.com',2,'2023-10-01 10:23:20',1),(30,'::1','m.agungmahardika12@gmail.com',2,'2023-10-01 10:24:21',1),(31,'::1','m.agungmahardika12@gmail.com',2,'2023-10-01 10:25:10',1),(32,'::1','me@gmail.com',4,'2023-10-01 10:27:03',1),(33,'::1','m.agungmahardika12@gmail.com',2,'2023-10-02 21:33:09',1),(34,'::1','me@gmail.com',4,'2023-10-04 21:27:58',1),(35,'::1','me@gmail.com',4,'2023-10-04 22:05:20',1),(36,'::1','me@gmail.com',4,'2023-10-05 08:39:25',1),(37,'::1','me@gmail.com',4,'2023-10-05 20:45:34',1),(38,'::1','me@gmail.com',4,'2023-10-07 21:02:27',1),(39,'::1','me@gmail.com',4,'2023-10-08 00:38:28',1),(40,'::1','me@gmail.com',4,'2023-10-10 02:38:13',1),(41,'::1','me@gmail.com',4,'2023-10-10 09:12:42',1),(42,'::1','me@gmail.com',4,'2023-10-11 22:25:00',1),(43,'::1','me@gmail.com',4,'2023-10-12 01:53:25',1),(44,'::1','m.agungmahardika12@gmail.com',2,'2023-10-12 09:37:03',1),(45,'::1','m.agungmahardika12@gmail.com',2,'2023-10-12 09:39:09',1),(46,'::1','m.agungmahardika12@gmail.com',2,'2023-10-12 09:41:59',1),(47,'::1','me@gmail.com',4,'2023-10-12 09:51:33',1),(48,'::1','m.agungmahardika12@gmail.com',2,'2023-10-13 09:31:42',1),(49,'::1','m.agungmahardika12@gmail.com',2,'2023-10-13 22:12:48',1),(50,'::1','m.agungmahardika12@gmail.com',2,'2023-10-15 05:39:10',1),(51,'::1','me@gmail.com',4,'2023-10-15 06:22:44',1),(52,'::1','m.agungmahardika12@gmail.com',2,'2023-10-15 06:42:31',1),(53,'::1','me@gmail.com',4,'2023-10-15 06:43:42',1),(54,'::1','m.agungmahardika12@gmail.com',2,'2023-10-15 21:29:34',1),(55,'::1','me@gmail.com',4,'2023-10-15 21:29:44',1),(56,'::1','m.agungmahardika12@gmail.com',2,'2023-10-16 07:05:08',1),(57,'::1','me@gmail.com',4,'2023-10-16 07:06:09',1),(58,'::1','m.agungmahardika12@gmail.com',2,'2023-10-16 07:27:55',1),(59,'::1','m.agungmahardika12@gmail.com',2,'2023-10-16 07:28:11',1),(60,'::1','m.agungmahardika12@gmail.com',2,'2023-10-16 07:30:27',1),(61,'::1','me@gmail.com',4,'2023-10-16 07:30:40',1),(62,'::1','m.agungmahardika12@gmail.com',2,'2023-10-16 08:56:31',1),(63,'::1','me@gmail.com',4,'2023-10-16 08:57:03',1),(64,'::1','m.agungmahardika12@gmail.com',2,'2023-10-16 09:37:10',1),(65,'::1','m.agungmahardika12@gmail.com',2,'2023-10-16 09:38:27',1),(66,'::1','me@gmail.com',4,'2023-10-16 10:29:50',1),(67,'::1','me@gmail.com',NULL,'2023-10-16 20:54:45',0),(68,'::1','me@gmail.com',4,'2023-10-16 20:55:01',1),(69,'::1','me@gmail.com',4,'2023-10-17 03:11:04',1),(70,'::1','me@gmail.com',4,'2023-10-17 07:26:26',1),(71,'::1','me@gmail.com',4,'2023-10-17 20:34:27',1),(72,'::1','me@gmail.com',4,'2023-10-18 08:02:00',1),(73,'::1','me@gmail.com',4,'2023-10-18 22:23:37',1),(74,'::1','me@gmail.com',4,'2023-10-19 07:56:06',1),(75,'::1','me@gmail.com',4,'2023-10-19 20:41:38',1),(76,'::1','me@gmail.com',4,'2023-10-20 04:16:11',1),(77,'::1','m.agungmahardika12@gmail.com',2,'2023-10-20 04:17:35',1),(78,'::1','m.agungmahardika12@gmail.com',2,'2023-10-20 04:18:13',1),(79,'::1','me@gmail.com',4,'2023-10-20 04:18:30',1),(80,'::1','me@gmail.com',4,'2023-10-20 06:43:58',1),(81,'::1','me@gmail.com',4,'2023-10-20 18:36:33',1),(82,'::1','admin@gmail.com',NULL,'2023-10-28 06:49:50',0),(83,'::1','admin@gmail.com',NULL,'2023-10-28 06:49:55',0),(84,'::1','m.agungmahardika12@gmail.com',NULL,'2023-10-28 06:50:06',0),(85,'::1','m.agungmahardika12@gmail.com',2,'2023-10-28 06:50:11',1),(86,'::1','admin@gmail.com',NULL,'2023-10-28 06:50:26',0),(87,'::1','admin@gmail.com',4,'2023-10-28 06:53:45',1),(88,'::1','user@gmail.com',2,'2023-10-28 22:16:33',1),(89,'::1','user@gmail.com',NULL,'2023-10-29 01:39:03',0),(90,'::1','user@gmail.com',2,'2023-10-29 01:39:07',1),(91,'::1','admin@gmail.com',NULL,'2023-10-29 01:48:21',0),(92,'::1','admin@gmail.com',NULL,'2023-10-29 01:48:24',0),(93,'::1','admin@gmail.com',NULL,'2023-10-29 01:48:29',0),(94,'::1','m.agungmahardika12@gmail.com',NULL,'2023-10-29 01:48:41',0),(95,'::1','admin@gmail.com',4,'2023-10-29 01:48:50',1),(96,'::1','admin@gmail.com',4,'2023-10-29 21:21:13',1),(97,'::1','admin@gmail.com',4,'2023-10-29 22:21:30',1),(98,'::1','user@gmail.com',2,'2023-10-30 03:32:50',1),(99,'::1','user@gmail.com',NULL,'2023-10-31 05:04:11',0),(100,'::1','user@gmail.com',2,'2023-10-31 05:04:15',1),(101,'::1','user@gmail.com',2,'2023-10-31 05:04:19',1),(102,'::1','admin@gmail.com',4,'2023-10-31 05:07:43',1),(103,'::1','user@gmail.com',2,'2023-10-31 08:04:58',1),(104,'::1','admin@gmail.com',4,'2023-10-31 08:05:24',1),(105,'::1','user@gmail.com',2,'2023-10-31 20:25:42',1),(106,'::1','admin@gmail.com',4,'2023-10-31 20:25:54',1),(107,'::1','admin@gmail.com',4,'2023-11-01 00:47:13',1),(108,'::1','user@gmail.com',2,'2023-11-01 00:49:11',1),(109,'::1','user@gmail.com',2,'2023-11-03 04:13:58',1),(110,'::1','user@gmail.com',2,'2023-11-03 06:47:41',1),(111,'::1','user@gmail.com',2,'2023-11-04 03:44:03',1),(112,'::1','admin@gmail.com',4,'2023-11-04 03:45:19',1),(113,'::1','admin@gmail.com',4,'2023-11-04 04:06:44',1),(114,'::1','user@gmail.com',2,'2023-11-05 20:38:06',1),(115,'::1','admin@gmail.com',4,'2023-11-05 21:26:40',1),(116,'::1','user@gmail.com',2,'2023-11-06 01:06:46',1),(117,'::1','admin@gmail.com',4,'2023-11-06 01:08:28',1),(118,'::1','user@gmail.com',2,'2023-11-06 06:16:11',1),(119,'::1','admin@gmail.com',4,'2023-11-06 06:20:20',1),(120,'::1','admin@gmail.com',4,'2023-11-06 19:37:04',1),(121,'::1','user@gmail.com',2,'2023-11-06 23:00:56',1),(122,'::1','admin@gmail.com',4,'2023-11-06 23:46:55',1),(123,'::1','admin@gmail.com',4,'2023-11-07 04:33:43',1),(124,'::1','user@gmail.com',2,'2023-11-07 04:54:22',1),(125,'::1','admin@gmail.com',4,'2023-11-07 10:24:26',1),(126,'::1','user@gmail.com',2,'2023-11-07 19:18:01',1),(127,'::1','user@gmail.com',2,'2023-11-08 04:05:29',1),(128,'::1','user@gmail.com',2,'2023-11-08 08:20:02',1),(129,'::1','user@gmail.com',2,'2023-11-08 17:07:26',1),(130,'::1','admin@gmail.com',4,'2023-11-08 17:12:01',1),(131,'::1','user@gmail.com',2,'2023-11-08 19:07:25',1),(132,'::1','user@gmail.com',2,'2023-11-15 05:08:22',1),(133,'::1','admin@gmail.com',4,'2023-12-02 07:33:51',1),(134,'::1','admin@gmail.com',4,'2023-12-02 07:34:42',1),(135,'::1','user@gmail.com',2,'2023-12-02 07:35:32',1),(136,'::1','user@gmail.com',2,'2023-12-03 19:02:36',1),(137,'::1','admin@gmail.com',4,'2023-12-03 19:43:43',1),(138,'::1','user@gmail.com',2,'2023-12-04 01:41:52',1),(139,'::1','admin@gmail.com',4,'2023-12-04 01:44:23',1),(140,'::1','user@gmail.com',2,'2023-12-04 06:31:22',1),(141,'::1','admin@gmail.com',4,'2023-12-04 07:15:59',1),(142,'::1','user@gmail.com',2,'2023-12-04 09:58:42',1),(143,'::1','admin@gmail.com',4,'2023-12-04 10:26:11',1),(144,'::1','user@gmail.com',2,'2023-12-04 18:19:42',1),(145,'::1','admin@gmail.com',NULL,'2023-12-04 18:20:29',0),(146,'::1','admin@gmail.com',NULL,'2023-12-04 18:20:40',0),(147,'::1','admin@gmail.com',4,'2023-12-04 18:20:45',1),(148,'::1','user@gmail.com',2,'2023-12-05 08:42:35',1),(149,'::1','admin@gmail.com',4,'2023-12-05 08:48:39',1),(150,'::1','user@gmail.com',2,'2023-12-08 01:53:59',1),(151,'::1','user@gmail.com',2,'2023-12-11 06:49:02',1),(152,'::1','admin@gmail.com',4,'2023-12-11 07:31:39',1),(153,'::1','admin@gmail.com',4,'2023-12-11 19:31:13',1),(154,'::1','user@gmail.com',2,'2023-12-11 19:49:56',1),(155,'::1','user@gmail.com',2,'2024-07-22 04:59:49',1),(156,'::1','user@gmail.com',2,'2024-07-22 22:57:45',1),(157,'::1','admin@gmail.com',4,'2024-07-22 23:19:40',1),(158,'::1','user@gmail.com',2,'2024-07-22 23:20:59',1),(159,'::1','user@gmail.com',2,'2025-02-17 05:07:11',1),(160,'::1','admin@gmail.com',NULL,'2025-02-17 08:59:31',0),(161,'::1','admin@gmail.com',4,'2025-02-17 08:59:35',1),(162,'::1','user@gmail.com',2,'2025-02-27 21:37:48',1),(163,'::1','admin@gmail.com',4,'2025-03-02 21:42:50',1),(164,'::1','admin@gmail.com',4,'2025-03-03 22:52:22',1),(165,'::1','user@gmail.com',NULL,'2025-03-04 21:39:06',0),(166,'::1','user@gmail.com',2,'2025-03-04 21:39:12',1),(167,'::1','user@gmail.com',2,'2025-03-05 01:53:21',1),(168,'::1','user@gmail.com',2,'2025-03-05 22:14:36',1),(169,'192.168.100.83','user@gmail.com',2,'2025-03-26 09:48:30',1),(170,'192.168.100.83','user@gmail.com',2,'2025-03-26 09:53:20',1),(171,'192.168.100.83','user@gmail.com',2,'2025-03-26 11:27:42',1),(172,'192.168.100.83','user@gmail.com',2,'2025-03-26 11:52:43',1),(173,'192.168.0.22','user@gmail.com',2,'2025-03-27 02:37:36',1),(174,'192.168.100.83','user@gmail.com',2,'2025-03-29 02:19:33',1),(175,'192.168.100.83','user@gmail.com',2,'2025-03-29 06:24:17',1),(176,'192.168.100.83','user@gmail.com',2,'2025-03-29 06:27:12',1),(177,'192.168.100.83','user@gmail.com',2,'2025-03-29 06:28:22',1),(178,'192.168.100.83','user@gmail.com',2,'2025-03-29 07:43:20',1),(179,'192.168.100.199','user@gmail.com',2,'2025-03-30 07:23:23',1),(180,'192.168.100.199','user@gmail.com',2,'2025-03-30 07:23:23',1),(181,'192.168.100.83','user@gmail.com',2,'2025-04-03 23:42:57',1),(182,'192.168.100.83','user@gmail.com',2,'2025-04-04 06:21:09',1),(183,'192.168.0.22','user@gmail.com',2,'2025-04-08 21:31:54',1),(184,'::1','user@gmail.com',2,'2025-04-27 03:47:07',1),(185,'36.69.9.189','user@gmail.com',2,'2025-04-27 03:57:22',1),(186,'36.77.69.105','user@gmail.com',2,'2025-04-28 05:33:26',1),(187,'36.69.125.71','m.agungmahardika12@gmail.com',10,'2025-05-03 06:24:47',1),(188,'36.69.125.71','m.agungmahardika12@gmail.com',10,'2025-05-03 06:55:34',1),(189,'36.69.125.71','m.agungmahardika12@gmail.com',10,'2025-05-03 07:05:02',1),(190,'36.69.125.71','m.agungmahardika12@gmail.com',10,'2025-05-03 11:32:23',1),(191,'182.253.145.206','test@gmail.com',NULL,'2025-05-04 03:56:57',0),(192,'182.253.145.206','ko@gmail.com',NULL,'2025-05-04 03:59:44',0),(193,'182.253.145.206','ha@gmail.com',NULL,'2025-05-04 04:08:22',0),(194,'182.253.145.206','ha@gmail.com',NULL,'2025-05-04 04:08:57',0),(195,'182.253.145.206','hi@gmail.com',NULL,'2025-05-04 04:09:49',0),(196,'182.253.145.206','user@gmail.com',2,'2025-05-04 04:12:05',1),(197,'182.253.145.206','cu@gmail.com',NULL,'2025-05-04 04:18:31',0),(198,'182.253.145.206','cu@gmail.com',NULL,'2025-05-04 04:21:05',0),(199,'182.253.145.206','ga@gmail.com',23,'2025-05-04 04:21:35',0),(200,'182.253.145.206','ga@gmail.com',23,'2025-05-04 04:21:46',0),(201,'182.253.145.206','ga@gmail.com',23,'2025-05-04 04:24:15',1),(202,'182.253.145.206','aa@gmail.com',24,'2025-05-04 04:25:38',0),(203,'182.253.145.206','ab@gmail.com',25,'2025-05-04 04:27:07',1),(204,'182.253.145.206','ga@gmail.com',23,'2025-05-04 06:20:05',1),(205,'182.4.68.155','admin@gmail.com',NULL,'2025-06-23 00:00:46',0),(206,'182.4.68.155','admin',NULL,'2025-06-23 00:01:03',0),(207,'182.4.68.155','admin@example.com',NULL,'2025-06-23 00:01:19',0),(208,'182.4.68.155','user@gmail.com',NULL,'2025-06-23 00:01:30',0),(209,'182.4.68.155','user@gmail.com',2,'2025-06-23 00:01:44',1),(210,'182.4.68.155','user@gmail.com',2,'2025-06-23 00:02:03',1),(211,'182.4.68.155','tes1@gmail.com',27,'2025-06-23 00:25:50',1),(212,'182.253.145.204','user@gmail.com',2,'2025-06-24 04:52:19',1);
/*!40000 ALTER TABLE `auth_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_permissions`
--

DROP TABLE IF EXISTS `auth_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_permissions`
--

LOCK TABLES `auth_permissions` WRITE;
/*!40000 ALTER TABLE `auth_permissions` DISABLE KEYS */;
INSERT INTO `auth_permissions` VALUES (1,'manage-users','Manage All User'),(2,'manage-profile','Manage User\'s Profile '),(3,'manage-property','Manage Owner\'s Properties');
/*!40000 ALTER TABLE `auth_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_reset_attempts`
--

DROP TABLE IF EXISTS `auth_reset_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_reset_attempts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_reset_attempts`
--

LOCK TABLES `auth_reset_attempts` WRITE;
/*!40000 ALTER TABLE `auth_reset_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_reset_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_tokens`
--

DROP TABLE IF EXISTS `auth_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `selector` varchar(255) NOT NULL,
  `hashedValidator` varchar(255) NOT NULL,
  `user_id` int unsigned NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_tokens_user_id_foreign` (`user_id`),
  KEY `selector` (`selector`),
  CONSTRAINT `auth_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_tokens`
--

LOCK TABLES `auth_tokens` WRITE;
/*!40000 ALTER TABLE `auth_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_users_permissions`
--

DROP TABLE IF EXISTS `auth_users_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_users_permissions` (
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `permission_id` int unsigned NOT NULL DEFAULT '0',
  KEY `auth_users_permissions_permission_id_foreign` (`permission_id`),
  KEY `user_id_permission_id` (`user_id`,`permission_id`),
  CONSTRAINT `auth_users_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auth_users_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_users_permissions`
--

LOCK TABLES `auth_users_permissions` WRITE;
/*!40000 ALTER TABLE `auth_users_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_users_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `city` (
  `id` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `geom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city`
--

LOCK TABLES `city` WRITE;
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
/*!40000 ALTER TABLE `city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment` (
  `id_comment` varchar(10) NOT NULL,
  `id_rumah_gadang` varchar(10) DEFAULT NULL,
  `id_event` varchar(10) DEFAULT NULL,
  `id_unique_place` varchar(10) DEFAULT NULL,
  `id_user` int unsigned NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `date` datetime NOT NULL,
  `status` varchar(5) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_comment`),
  UNIQUE KEY `id_comment` (`id_comment`),
  KEY `comment_id_rumah_gadang_foreign` (`id_rumah_gadang`),
  KEY `comment_id_event_foreign` (`id_event`),
  KEY `comment_id_unique_place_foreign` (`id_unique_place`),
  KEY `comment_id_user_foreign` (`id_user`),
  CONSTRAINT `comment_id_event_foreign` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comment_id_rumah_gadang_foreign` FOREIGN KEY (`id_rumah_gadang`) REFERENCES `rumah_gadang` (`id_rumah_gadang`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comment_id_unique_place_foreign` FOREIGN KEY (`id_unique_place`) REFERENCES `unique_place` (`id_unique_place`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comment_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `id` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `geom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `culinary_place`
--

DROP TABLE IF EXISTS `culinary_place`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `culinary_place` (
  `id_culinary_place` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cp` varchar(15) DEFAULT NULL,
  `open` time DEFAULT NULL,
  `close` time DEFAULT NULL,
  `geom` geometry DEFAULT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_culinary_place`),
  UNIQUE KEY `id_culinary_place` (`id_culinary_place`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `culinary_place`
--

LOCK TABLES `culinary_place` WRITE;
/*!40000 ALTER TABLE `culinary_place` DISABLE KEYS */;
INSERT INTO `culinary_place` VALUES ('C01','Kios Fastfood','Pasir Talang Sel., Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','81503703921','07:00:00','18:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.48101400,10.10500000,'Kios Fastfood','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C02','Dhapoer Iciak Cafe & Resto','Jln raya cuaca No.77, Pasir Talang Sel., Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','81270002899','08:00:00','22:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47805391,101.05046463,'Dhapoer Iciak Cafe & Resto','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C03','Rumah Makan Barokah','Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','81261428398','09:30:00','17:30:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47877431,101.05626966,'Rumah Makan Barokah','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C04','Warung Nasi Tina','Jl. Raya Rawang No.63, Pasir Talang Sel., Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','85274771733','10:00:00','22:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47573607,101.04365912,'Warung Nasi Tina','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C05','Rumah Makan Singgalang','Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','82268631614','08:00:00','22:30:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47817764,101.05419273,'Rumah Makan Singgalang','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C06','Sate Pak Cun','Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','85274426750','07:00:00','22:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47878661,101.05571372,'','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C07','Ampera Ida','Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','82386505303','07:00:00','20:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47885408,101.05604495,'','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C08','Cafe es batok','Ps. Muara Labuh, Kec. Sungai Pagu, Kabupaten Solok Selatan, Sumatera Barat 27776','82170547588','10:00:00','22:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47866155,101.05523650,'','2023-12-05 00:38:48','2023-12-05 00:38:48'),('C09','Mpek Mpek Cek Zia','Jln. Lintas padang muara - labuh. Pakan rabaa tengah batu kulambai, Taman kota, Kec. Koto Parik Gadang Diateh, Kabupaten Solok Selatan, Sumatera Barat 27776','081278050528','11:00:00','22:00:00',_binary '\æ\0\0\0\0\0\0\0\0\0\0\0¾6/\ê¾CY@\ãW¿	ó¼÷¿¿6/ÆµCY@±@\ß\á¾÷¿¾6/n\ÂCY@j\ÅþIÙ¿÷¿¾6/\ê¾CY@\ãW¿	ó¼÷¿',-1.47917730,101.05365690,'','2023-12-05 00:38:48','2023-12-05 00:38:48');
/*!40000 ALTER TABLE `culinary_place` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `culinary_place_gallery`
--

DROP TABLE IF EXISTS `culinary_place_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `culinary_place_gallery` (
  `id_culinary_place_gallery` varchar(10) NOT NULL,
  `id_culinary_place` varchar(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_culinary_place_gallery`),
  UNIQUE KEY `id_culinary_place_gallery` (`id_culinary_place_gallery`),
  KEY `culinary_place_gallery_id_culinary_place_foreign` (`id_culinary_place`),
  CONSTRAINT `culinary_place_gallery_id_culinary_place_foreign` FOREIGN KEY (`id_culinary_place`) REFERENCES `culinary_place` (`id_culinary_place`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `culinary_place_gallery`
--

LOCK TABLES `culinary_place_gallery` WRITE;
/*!40000 ALTER TABLE `culinary_place_gallery` DISABLE KEYS */;
INSERT INTO `culinary_place_gallery` VALUES ('01','C03','c3b.png',NULL,NULL),('02','C08','c8a.png',NULL,NULL),('03','C09','c9a.png',NULL,NULL);
/*!40000 ALTER TABLE `culinary_place_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_facility_atraction`
--

DROP TABLE IF EXISTS `detail_facility_atraction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_facility_atraction` (
  `id` varchar(10) NOT NULL,
  `id_atraction` varchar(50) NOT NULL,
  `id_atraction_facility` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `detail_facility_atraction_id_atraction_foreign` (`id_atraction`),
  KEY `detail_facility_atraction_id_atraction_facility_foreign` (`id_atraction_facility`),
  CONSTRAINT `detail_facility_atraction_id_atraction_facility_foreign` FOREIGN KEY (`id_atraction_facility`) REFERENCES `atraction_facility` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_facility_atraction_id_atraction_foreign` FOREIGN KEY (`id_atraction`) REFERENCES `atraction` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_facility_atraction`
--

LOCK TABLES `detail_facility_atraction` WRITE;
/*!40000 ALTER TABLE `detail_facility_atraction` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_facility_atraction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_facility_homestay`
--

DROP TABLE IF EXISTS `detail_facility_homestay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_facility_homestay` (
  `id` varchar(10) NOT NULL,
  `id_homestay` varchar(50) NOT NULL,
  `id_homestay_facility` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_facility_homestay_id_homestay_foreign` (`id_homestay`),
  KEY `detail_facility_homestay_id_homestay_facility_foreign` (`id_homestay_facility`),
  CONSTRAINT `detail_facility_homestay_id_homestay_facility_foreign` FOREIGN KEY (`id_homestay_facility`) REFERENCES `homestay_facility` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_facility_homestay_id_homestay_foreign` FOREIGN KEY (`id_homestay`) REFERENCES `homestay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_facility_homestay`
--

LOCK TABLES `detail_facility_homestay` WRITE;
/*!40000 ALTER TABLE `detail_facility_homestay` DISABLE KEYS */;
INSERT INTO `detail_facility_homestay` VALUES ('01','H01','HF01','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('02','H01','HF02','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('03','H01','HF03','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('04','H01','HF04','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('05','H01','HF05','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('06','H01','HF06','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('07','H01','HF07','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('08','H01','HF08','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('09','H01','HF09','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('10','H03','HF01','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('11','H03','HF02','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('12','H03','HF03','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('13','H03','HF04','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('14','H03','HF05','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('15','H03','HF06','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('16','H03','HF07','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('17','H04','HF01','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('18','H04','HF02','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('19','H04','HF07','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('20','H04','HF03','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('21','H04','HF04','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('22','H04','HF05','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('23','H05','HF01','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('24','H05','HF07','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('25','H05','HF03','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('26','H05','HF04','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('27','H08','HF01','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('28','H08','HF07','','2023-12-11 12:45:57','2023-12-11 12:45:57'),('29','H08','HF04','','2023-12-11 12:45:57','2023-12-11 12:45:57');
/*!40000 ALTER TABLE `detail_facility_homestay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_facility_rumah_gadang`
--

DROP TABLE IF EXISTS `detail_facility_rumah_gadang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_facility_rumah_gadang` (
  `id_detail_facility_rumah_gadang` varchar(10) NOT NULL,
  `id_rumah_gadang` varchar(10) NOT NULL,
  `id_facility_rumah_gadang` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_facility_rumah_gadang`),
  UNIQUE KEY `id_detail_facility_rumah_gadang` (`id_detail_facility_rumah_gadang`),
  KEY `detail_facility_rumah_gadang_id_rumah_gadang_foreign` (`id_rumah_gadang`),
  KEY `detail_facility_rumah_gadang_id_facility_rumah_gadang_foreign` (`id_facility_rumah_gadang`),
  CONSTRAINT `detail_facility_rumah_gadang_id_facility_rumah_gadang_foreign` FOREIGN KEY (`id_facility_rumah_gadang`) REFERENCES `facility_rumah_gadang` (`id_facility_rumah_gadang`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_facility_rumah_gadang_id_rumah_gadang_foreign` FOREIGN KEY (`id_rumah_gadang`) REFERENCES `rumah_gadang` (`id_rumah_gadang`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_facility_rumah_gadang`
--

LOCK TABLES `detail_facility_rumah_gadang` WRITE;
/*!40000 ALTER TABLE `detail_facility_rumah_gadang` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_facility_rumah_gadang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_homestay_unit_facility`
--

DROP TABLE IF EXISTS `detail_homestay_unit_facility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_homestay_unit_facility` (
  `id` varchar(10) NOT NULL,
  `id_homestay_unit` varchar(50) NOT NULL,
  `id_homestay_unit_facility` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_homestay_unit_facility_id_homestay_unit_foreign` (`id_homestay_unit`),
  KEY `detail_homestay_unit_facility_id_homestay_unit_facility_foreign` (`id_homestay_unit_facility`),
  CONSTRAINT `detail_homestay_unit_facility_id_homestay_unit_facility_foreign` FOREIGN KEY (`id_homestay_unit_facility`) REFERENCES `homestay_unit_facility` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_homestay_unit_facility_id_homestay_unit_foreign` FOREIGN KEY (`id_homestay_unit`) REFERENCES `homestay_unit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_homestay_unit_facility`
--

LOCK TABLES `detail_homestay_unit_facility` WRITE;
/*!40000 ALTER TABLE `detail_homestay_unit_facility` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_homestay_unit_facility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_package`
--

DROP TABLE IF EXISTS `detail_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_package` (
  `activity` varchar(255) NOT NULL,
  `id_day` varchar(5) NOT NULL,
  `id_package` varchar(50) NOT NULL,
  `id_object` varchar(50) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`activity`),
  KEY `detail_package_id_day_foreign` (`id_day`),
  KEY `detail_package_id_package_foreign` (`id_package`),
  CONSTRAINT `detail_package_id_day_foreign` FOREIGN KEY (`id_day`) REFERENCES `package_day` (`day`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_package_id_package_foreign` FOREIGN KEY (`id_package`) REFERENCES `tourism_package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_package`
--

LOCK TABLES `detail_package` WRITE;
/*!40000 ALTER TABLE `detail_package` DISABLE KEYS */;
INSERT INTO `detail_package` VALUES ('01','01','P01','W02','Worship Place','Visit Masjid Al-Muqarramah',NULL,NULL),('02','01','P01','H01','Homestay','Visit Homestay 01',NULL,NULL),('03','02','P02','A02','Atraksi','Visit Jembatan Merah',NULL,NULL),('04','03','P03','A02','Atraksi','Visit Jembatan Merah',NULL,NULL),('05','03','P03','A03','Atraksi','Visit Galeri Saribu Rumah Gadang',NULL,NULL),('06','04','P04','A02','Atraksi','Visit Jembatan Merah',NULL,NULL),('07','04','P04','A03','Atraksi','Visit Galeri Saribu Rumah Gadang',NULL,NULL),('08','04','P04','H02','Homestay','Visit Homestay 02',NULL,NULL);
