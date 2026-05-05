-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: vengg
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dep`
--

DROP TABLE IF EXISTS `dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_create` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dep`
--

LOCK TABLES `dep` WRITE;
/*!40000 ALTER TABLE `dep` DISABLE KEYS */;
INSERT INTO `dep` VALUES (1,'พนักงานคอมพิวเตอร์','2019-08-10 17:45:49'),(2,'นักวิชาการคอมพิวเตอร์ชำนาญการ','2019-08-10 17:45:49'),(3,'เจ้าหน้าที่ศาลยุติธรรมปฏิบัติงาน','2019-08-10 17:45:49'),(4,'เจ้าหน้าที่ศาลยุติธรรมชำนาญงาน','2019-08-10 17:45:49'),(5,'นักจิตวิทยาปฏิบัติการ','2019-08-10 17:45:49'),(6,'พนักงานสถานที่','2019-08-10 17:45:49'),(7,'พนักงานขับรถยนต์','2019-08-10 17:45:49'),(8,'เจ้าหน้าที่ศาลยุติธรรม','2019-08-10 17:45:49'),(9,'เจ้าพนักงานศาลยุติธรรมปฏิบัติการ','2019-08-10 17:45:49'),(10,'นิติกรชำนาญการ','2019-08-10 17:45:49'),(11,'เจ้าพนักงานศาลยุติธรรมชำนาญการ','2019-08-10 17:45:49'),(12,'นักวิชาการเงินและบัญชีปฏิบัติการ','2019-08-10 17:45:49'),(13,'เจ้าพนักงานศาลยุติธรรมชำนาญการพิเศษ','2019-08-10 17:45:49'),(14,'นิติกร','2019-08-10 17:45:49'),(15,'ผู้อำนวยการสำนักงานประจำศาลจังหวัดเบตง','2019-08-10 17:45:49'),(17,'พนักงานขับรถยนต์(จ้างเหมา)',NULL),(18,'ผู้พิพากษาศาลจังหวัดเบตง',NULL),(19,'นิติกรชำนาญการพิเศษ',NULL),(20,'เจ้าพนักงานการเงินและบัญชีปฏิบัติงาน',NULL),(21,'นักจิตวิทยาชำนาญการ',NULL),(22,'เจ้าพนักงานศาลยุติธรรม',NULL),(23,'ผู้พิพากษาหัวหน้าศาลจังหวัดเบตง',NULL),(24,'ผู้พิพากษาหัวหน้าคณะชั้นต้นในศาลจังหวัดเบตง',NULL);
/*!40000 ALTER TABLE `dep` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fname`
--

DROP TABLE IF EXISTS `fname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_create` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fname`
--

LOCK TABLES `fname` WRITE;
/*!40000 ALTER TABLE `fname` DISABLE KEYS */;
INSERT INTO `fname` VALUES (1,'นาย','2019-08-10 17:45:49'),(2,'นาง','2019-08-10 17:45:50'),(3,'นางสาว','2019-08-10 17:45:50'),(4,'พันจ่าเอก',NULL),(5,'พ.ต.อ.',NULL),(6,'พท.',NULL),(7,'ส.ต.อ.',NULL);
/*!40000 ALTER TABLE `fname` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date_create` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES (1,'ผู้อำนวยการฯ','2019-10-06 18:49:32'),(2,'กลุ่มช่วยอำนวยการ','2019-10-06 18:49:32'),(3,'กลุ่มงานช่วยพิจารณาคดี','2019-10-06 18:49:32'),(4,'กลุ่มงานบริหารจัดการคดี','2019-10-06 18:49:32'),(5,'กลุ่มงานคลัง','2019-10-06 18:49:32'),(6,'กลุ่มงานปริการประชาชนและประชาสัมพันธ์','2019-10-06 18:49:32'),(7,'กลุ่มงานไกล่เกลี่ยและประนอมข้อพิพาท','2019-10-06 18:49:32'),(8,'ผู้พิพากษา',NULL),(9,'กลุ่มงานเจ้าพนักงานตำรวจศาล',NULL);
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holiday`
--

DROP TABLE IF EXISTS `holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holiday` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holiday`
--

LOCK TABLES `holiday` WRITE;
/*!40000 ALTER TABLE `holiday` DISABLE KEYS */;
INSERT INTO `holiday` VALUES (2,'2026-05-13','วันพืชมงคล'),(3,'2026-05-04','วันฉัตรมงคล'),(4,'2026-06-01','วันหยุดชดเชยวันวิสาขบูชา');
/*!40000 ALTER TABLE `holiday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `line`
--

DROP TABLE IF EXISTS `line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `line`
--

LOCK TABLES `line` WRITE;
/*!40000 ALTER TABLE `line` DISABLE KEYS */;
/*!40000 ALTER TABLE `line` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `id_card` varchar(255) DEFAULT NULL,
  `fname` varchar(25) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sname` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `bloodtype` varchar(255) DEFAULT NULL,
  `dep` varchar(255) DEFAULT NULL,
  `workgroup` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `bank_comment` varchar(200) DEFAULT NULL,
  `status` smallint(6) DEFAULT 10,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `st` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1566445991,'1566445991','','นาย','admin','admin',NULL,NULL,NULL,'','','','',NULL,NULL,10,NULL,'2026-04-27 08:23:38',999);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` smallint(6) NOT NULL DEFAULT 1,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=1680162007 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1566445991,'admin','qdYs15wrU3R7ghAuAuXNMc-fFxlZ8QyT','$2y$10$86PRXBYSyTaUNl1RhBA8B.8CSGsHiOuEY4Ll2749z066wKMEpfTBi','VSorsXwWyDjK0WwK76PNqcdsextFlwai_1566445992','',9,10,'0000-00-00 00:00:00','2023-06-19 13:24:45');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ven`
--

DROP TABLE IF EXISTS `ven`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ven` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ven_com_id` varchar(255) DEFAULT NULL,
  `ven_com_idb` varchar(255) DEFAULT NULL,
  `ven_date` date NOT NULL,
  `ven_time` varchar(255) NOT NULL,
  `ven_month` varchar(255) NOT NULL,
  `vn_id` int(11) DEFAULT NULL,
  `vns_id` int(11) DEFAULT NULL,
  `DN` varchar(250) DEFAULT NULL,
  `ven_com_name` varchar(255) DEFAULT NULL,
  `ven_com_num_all` varchar(255) DEFAULT NULL,
  `ven_name` varchar(255) DEFAULT NULL,
  `u_role` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `gcal_id` varchar(255) DEFAULT NULL,
  `ref1` varchar(255) DEFAULT NULL,
  `ref2` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ven`
--

LOCK TABLES `ven` WRITE;
/*!40000 ALTER TABLE `ven` DISABLE KEYS */;
/*!40000 ALTER TABLE `ven` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ven_change`
--

DROP TABLE IF EXISTS `ven_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ven_change` (
  `id` varchar(255) NOT NULL,
  `ven_month` varchar(255) DEFAULT NULL,
  `ven_date1` varchar(255) DEFAULT NULL,
  `ven_date2` varchar(255) DEFAULT NULL,
  `ven_com_id` varchar(255) DEFAULT NULL,
  `ven_com_num_all` varchar(255) DEFAULT NULL,
  `DN` varchar(255) DEFAULT NULL,
  `u_role` varchar(255) DEFAULT NULL,
  `ven_id1` int(11) DEFAULT NULL,
  `ven_id2` int(11) DEFAULT NULL,
  `ven_id1_old` int(11) DEFAULT NULL,
  `ven_id2_old` int(11) DEFAULT NULL,
  `user_id1` int(11) DEFAULT NULL,
  `user_id2` int(11) DEFAULT NULL,
  `s_po` int(11) DEFAULT NULL,
  `s_bb` int(11) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ref1` varchar(255) DEFAULT NULL,
  `ref2` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ven_change`
--

LOCK TABLES `ven_change` WRITE;
/*!40000 ALTER TABLE `ven_change` DISABLE KEYS */;
/*!40000 ALTER TABLE `ven_change` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ven_com`
--

DROP TABLE IF EXISTS `ven_com`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ven_com` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ven_com_num` varchar(255) DEFAULT NULL,
  `ven_com_date` varchar(255) DEFAULT NULL,
  `ven_month` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `vn_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `ref` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ven_com`
--

LOCK TABLES `ven_com` WRITE;
/*!40000 ALTER TABLE `ven_com` DISABLE KEYS */;
/*!40000 ALTER TABLE `ven_com` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ven_excluded`
--

DROP TABLE IF EXISTS `ven_excluded`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ven_excluded` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ven_name` varchar(255) NOT NULL,
  `ven_month` varchar(7) NOT NULL COMMENT 'YYYY-MM',
  `day` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_exclusion` (`user_id`,`ven_name`,`ven_month`,`day`),
  KEY `idx_ven_month` (`ven_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ven_excluded`
--

LOCK TABLES `ven_excluded` WRITE;
/*!40000 ALTER TABLE `ven_excluded` DISABLE KEYS */;
/*!40000 ALTER TABLE `ven_excluded` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ven_name`
--

DROP TABLE IF EXISTS `ven_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ven_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `DN` varchar(255) DEFAULT NULL,
  `srt` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ven_name`
--

LOCK TABLES `ven_name` WRITE;
/*!40000 ALTER TABLE `ven_name` DISABLE KEYS */;
INSERT INTO `ven_name` VALUES (24,'ฟื้นฟู/ตรวจสอบการจับ','กลางวัน',4),(25,'หมายจับ-ค้น','กลางคืน',1),(26,'ผู้ตรวจ(กลางคืน)','กลางคืน',5),(27,'ศาลแขวงและพิจารณาคำร้องขอปล่อยชั่วคราว','กลางวัน',2),(28,'เวรเปิดทำการพิจารณาคำร้องขอปล่อยชั่วคราว','กลางวัน',3);
/*!40000 ALTER TABLE `ven_name` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ven_name_sub`
--

DROP TABLE IF EXISTS `ven_name_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ven_name_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ven_name_id` int(11) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `srt` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ven_name_sub`
--

LOCK TABLES `ven_name_sub` WRITE;
/*!40000 ALTER TABLE `ven_name_sub` DISABLE KEYS */;
INSERT INTO `ven_name_sub` VALUES (106,'ผู้พิพากษา',24,0,'YellowGreen',1),(107,'หัวหน้ากลุ่ม',24,0,'YellowGreen',3),(111,'ผู้ตรวจ',26,0,'DarkCyan',0),(114,'หัวหน้ากลุ่ม',27,1500,'BlueViolet',3),(115,'งานการเงิน ประชาสัมพันธ์',24,0,'YellowGreen',4),(116,'ผู้พิพากษา',25,2500,'Blue',1),(117,'จนท.',25,1200,'Blue',3),(118,'ผู้พิพากษา',27,3000,'BlueViolet',1),(119,'งานการเงิน',27,1500,'BlueViolet',4),(120,'งานผัดฟ้อง-ฝากขัง',27,1500,'BlueViolet',6),(121,'งานประชาสัมพันธ์',27,1500,'BlueViolet',7),(123,'งานหน้าบัลลังก์ รับฟ้อง',24,0,'YellowGreen',5),(124,'ผู้พิพากษา',28,3000,'Chocolate',1),(126,'หัวหน้ากลุ่ม',28,1500,'Chocolate',3),(127,'งานหมาย/รับคำร้อง',28,1500,'Chocolate',4),(128,'งานประชาสัมพันธ์',28,1500,'Chocolate',5),(129,'งานรับฟ้อง',27,1500,'BlueViolet',5),(130,'งานการเงิน',28,1500,'Chocolate',3);
/*!40000 ALTER TABLE `ven_name_sub` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ven_user`
--

DROP TABLE IF EXISTS `ven_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ven_user` (
  `vu_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `order` int(2) DEFAULT NULL,
  `vn_id` int(11) NOT NULL,
  `vns_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  PRIMARY KEY (`vu_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ven_user`
--

LOCK TABLES `ven_user` WRITE;
/*!40000 ALTER TABLE `ven_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `ven_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-30  0:10:17
