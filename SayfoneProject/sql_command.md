-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 11, 2025 at 03:36 AM
-- Server version: 10.10.2-MariaDB
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sayfonesm`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE IF NOT EXISTS `academic_years` (
  `academic_year_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດສົກຮຽນ (PK)',
  `year_name` varchar(50) NOT NULL COMMENT 'ຊື່/ປີຂອງສົກຮຽນ (ຕົວຢ່າງ: 2024-2025)',
  `start_date` date NOT NULL COMMENT 'ວັນທີເລີ່ມຕົ້ນສົກຮຽນ',
  `end_date` date NOT NULL COMMENT 'ວັນທີສິ້ນສຸດສົກຮຽນ',
  `is_current` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ກຳນົດວ່າແມ່ນສົກຮຽນປັດຈຸບັນ ຫຼື ບໍ່ (TRUE/FALSE)',
  `status` enum('upcoming','active','completed') NOT NULL DEFAULT 'upcoming' COMMENT 'ສະຖານະຂອງສົກຮຽນ: upcoming, active, completed',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`academic_year_id`),
  UNIQUE KEY `UQ_AcademicYears_name` (`year_name`) COMMENT 'ຊື່ສົກຮຽນຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_AcademicYears_current` (`is_current`),
  KEY `IDX_AcademicYears_status` (`status`),
  KEY `IDX_AcademicYears_dates` (`start_date`,`end_date`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE IF NOT EXISTS `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດປະກາດ (PK)',
  `title` varchar(255) NOT NULL COMMENT 'ຫົວຂໍ້ປະກາດ',
  `content` text DEFAULT NULL COMMENT 'ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງປະກາດ',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມສະແດງປະກາດນີ້',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດການສະແດງປະກາດ (NULL ໝາຍເຖິງບໍ່ມີກຳນົດ)',
  `target_group` enum('all','teachers','students','parents') NOT NULL DEFAULT 'all' COMMENT 'ກຸ່ມເປົ້າໝາຍທີ່ເຫັນປະກາດນີ້',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ປັກໝຸດປະກາດນີ້ໄວ້ເທິງສຸດ ຫຼື ບໍ່ (TRUE/FALSE)',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ແນບ (ຖ້າມີ)',
  `created_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ສ້າງປະກາດ (FK ຈາກ Users)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`announcement_id`),
  KEY `IDX_Announcements_creator` (`created_by`),
  KEY `IDX_Announcements_target` (`target_group`),
  KEY `IDX_Announcements_dates` (`start_date`,`end_date`),
  KEY `IDX_Announcements_pinned` (`is_pinned`),
  KEY `IDX_Announcements_created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D49: ຕາຕະລາງເກັບຂໍ້ມູນປະກາດຂ່າວສານ';

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດບັນທຶກການຂາດ-ມາ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `class_id` int(11) NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK ຈາກ Classes)',
  `subject_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດວິຊາ (FK ຈາກ Subjects). ອາດຈະ NULL ຖ້າເປັນການເຊັກຊື່ລວມປະຈຳວັນ.',
  `attendance_date` date NOT NULL COMMENT 'ວັນທີທີ່ບັນທຶກການຂາດ-ມາ',
  `status` enum('present','absent','late','excused') NOT NULL COMMENT 'ສະຖານະ: present, absent, late, excused',
  `reason` text DEFAULT NULL COMMENT 'ເຫດຜົນການຂາດ/ລາ/ມາຊ້າ (ຖ້າມີ)',
  `recorded_by` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ບັນທຶກ (FK ຈາກ Users). ອາດຈະ NULL.',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `UQ_Attendance_stud_date_subj` (`student_id`,`attendance_date`,`subject_id`) COMMENT 'ປ້ອງກັນການບັນທຶກຊ້ຳຊ້ອນສຳລັບ ນັກຮຽນ/ວັນທີ/ວິຊາ(ຖ້າມີ)',
  KEY `IDX_Attendance_student` (`student_id`),
  KEY `IDX_Attendance_class` (`class_id`),
  KEY `IDX_Attendance_subject` (`subject_id`),
  KEY `IDX_Attendance_recorder` (`recorded_by`),
  KEY `IDX_Attendance_date` (`attendance_date`),
  KEY `IDX_Attendance_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D34: ຕາຕະລາງເກັບຂໍ້ມູນດິບການຂາດ-ມາຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
CREATE TABLE IF NOT EXISTS `backups` (
  `backup_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການສຳຮອງຂໍ້ມູນ (PK)',
  `backup_name` varchar(255) NOT NULL COMMENT 'ຊື່ໄຟລ໌ ຫຼື ຊື່ທີ່ໃຊ້ລະບຸການສຳຮອງຂໍ້ມູນຄັ້ງນີ້',
  `backup_type` enum('full','partial') NOT NULL COMMENT 'ປະເພດການສຳຮອງຂໍ້ມູນ: full, partial',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ເຕັມຂອງໄຟລ໌ສຳຮອງຂໍ້ມູນທີ່ຖືກເກັບໄວ້',
  `file_size` bigint(20) DEFAULT NULL COMMENT 'ຂະໜາດຂອງໄຟລ໌ສຳຮອງຂໍ້ມູນ (ເປັນ bytes)',
  `backup_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາທີ່ສຳຮອງຂໍ້ມູນ',
  `status` enum('success','failed','in_progress') NOT NULL DEFAULT 'in_progress' COMMENT 'ສະຖານະການສຳຮອງຂໍ້ມູນ: success, failed, in_progress',
  `initiated_by` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ເລີ່ມດຳເນີນການ (FK ຈາກ Users). ອາດຈະ NULL ຖ້າເປັນລະບົບອັດຕະໂນມັດ.',
  `description` text DEFAULT NULL COMMENT 'ໝາຍເຫດເພີ່ມເຕີມກ່ຽວກັບການສຳຮອງຂໍ້ມູນຄັ້ງນີ້',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record ນີ້',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ນີ້ຄັ້ງສຸດທ້າຍ (ເຊັ່ນ: ເມື່ອປ່ຽນ status)',
  PRIMARY KEY (`backup_id`),
  UNIQUE KEY `UQ_Backups_name` (`backup_name`) USING HASH COMMENT 'ຊື່ການສຳຮອງຂໍ້ມູນຄວນຈະບໍ່ຊ້ຳກັນ',
  KEY `IDX_Backups_initiator` (`initiated_by`),
  KEY `IDX_Backups_type` (`backup_type`),
  KEY `IDX_Backups_date` (`backup_date`),
  KEY `IDX_Backups_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D59: ຕາຕະລາງເກັບປະຫວັດການສຳຮອງຂໍ້ມູນລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `biometric_data`
--

DROP TABLE IF EXISTS `biometric_data`;
CREATE TABLE IF NOT EXISTS `biometric_data` (
  `biometric_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຂໍ້ມູນຊີວະມິຕິ (PK)',
  `user_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ເຈົ້າຂອງຂໍ້ມູນ (FK ຈາກ Users)',
  `biometric_type` enum('fingerprint','face') NOT NULL COMMENT 'ປະເພດຂໍ້ມູນຊີວະມິຕິ: fingerprint, face',
  `biometric_data` longblob NOT NULL COMMENT 'ຂໍ້ມູນຊີວະມິຕິຕົວຈິງ (template/binary data)',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະຂໍ້ມູນນີ້: active, inactive',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`biometric_id`),
  KEY `IDX_BiometricData_user` (`user_id`),
  KEY `IDX_BiometricData_type` (`biometric_type`),
  KEY `IDX_BiometricData_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D6: ຕາຕະລາງເກັບຂໍ້ມູນຊີວະມິຕິສຳລັບການຢືນຢັນຕົວຕົນ';

-- --------------------------------------------------------

--
-- Table structure for table `biometric_logs`
--

DROP TABLE IF EXISTS `biometric_logs`;
CREATE TABLE IF NOT EXISTS `biometric_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດ Log ການໃຊ້ຊີວະມິຕິ (PK)',
  `user_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ພະຍາຍາມສະແກນ (FK ຈາກ Users)',
  `biometric_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຂໍ້ມູນຊີວະມິຕິທີ່ໃຊ້ (FK ຈາກ Biometric_Data). ອາດຈະ NULL ຖ້າສະແກນບໍ່ຜ່ານ ຫຼື ຫາ User ບໍ່ພົບ.',
  `log_type` enum('check_in','check_out','authentication') NOT NULL COMMENT 'ປະເພດການໃຊ້ງານ: check_in, check_out, authentication',
  `status` enum('success','failed') NOT NULL COMMENT 'ຜົນລັບການສະແກນ: success, failed',
  `device_id` varchar(100) DEFAULT NULL COMMENT 'ລະຫັດເຄື່ອງສະແກນ/ອຸປະກອນທີ່ໃຊ້',
  `location` varchar(100) DEFAULT NULL COMMENT 'ສະຖານທີ່ຕິດຕັ້ງເຄື່ອງສະແກນ (ເຊັ່ນ: ປະຕູໜ້າ, ຫ້ອງການ)',
  `log_time` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາທີ່ເກີດເຫດການສະແກນ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາທີ່ບັນທຶກ Log ນີ້',
  PRIMARY KEY (`log_id`),
  KEY `IDX_BiometricLogs_user` (`user_id`),
  KEY `IDX_BiometricLogs_bio_data` (`biometric_id`),
  KEY `IDX_BiometricLogs_log_type` (`log_type`),
  KEY `IDX_BiometricLogs_status` (`status`),
  KEY `IDX_BiometricLogs_log_time` (`log_time`),
  KEY `IDX_BiometricLogs_device` (`device_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D7: ຕາຕະລາງເກັບ Log ການພະຍາຍາມໃຊ້ລະບົບຊີວະມິຕິ';

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຫ້ອງຮຽນ (PK)',
  `class_name` varchar(100) NOT NULL COMMENT 'ຊື່ຫ້ອງຮຽນ (ຕົວຢ່າງ: ມ.1/1, ປ.5A)',
  `grade_level` varchar(50) NOT NULL COMMENT 'ລະດັບຊັ້ນ (ຕົວຢ່າງ: ມ.1, ປ.5)',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK ຈາກ Academic_Years)',
  `homeroom_teacher_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຄູປະຈຳຫ້ອງ (FK ຈາກ Teachers). ອາດຈະ NULL.',
  `room_number` varchar(20) DEFAULT NULL COMMENT 'ເລກຫ້ອງ ຫຼື ສະຖານທີ່ຂອງຫ້ອງຮຽນ',
  `capacity` int(11) DEFAULT NULL COMMENT 'ຈຳນວນນັກຮຽນສູງສຸດທີ່ຫ້ອງຮອງຮັບໄດ້',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບຫ້ອງຮຽນ',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະຫ້ອງຮຽນ: active, inactive',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `UQ_Classes_name_year` (`academic_year_id`,`class_name`) COMMENT 'ຊື່ຫ້ອງຮຽນຕ້ອງບໍ່ຊ້ຳກັນພາຍໃນສົກຮຽນດຽວກັນ',
  KEY `IDX_Classes_academic_year` (`academic_year_id`),
  KEY `IDX_Classes_teacher` (`homeroom_teacher_id`),
  KEY `IDX_Classes_grade_level` (`grade_level`),
  KEY `IDX_Classes_status` (`status`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `class_subjects`
--

DROP TABLE IF EXISTS `class_subjects`;
CREATE TABLE IF NOT EXISTS `class_subjects` (
  `class_subject_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການມອບໝາຍວິຊາ-ຫ້ອງຮຽນ (PK)',
  `class_id` int(11) NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK ຈາກ Classes)',
  `subject_id` int(11) NOT NULL COMMENT 'ລະຫັດວິຊາຮຽນ (FK ຈາກ Subjects)',
  `teacher_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຄູສອນທີ່ຮັບຜິດຊອບວິຊານີ້ໃນຫ້ອງນີ້ (FK ຈາກ Teachers). ອາດຈະ NULL.',
  `hours_per_week` int(11) DEFAULT NULL COMMENT 'ຈຳນວນຊົ່ວໂມງຕໍ່ອາທິດ (ໂດຍປະມານ, ຖ້າມີ)',
  `day_of_week` varchar(20) DEFAULT NULL COMMENT 'ມື້ທີ່ສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ, ຖ້າມີ)',
  `start_time` time DEFAULT NULL COMMENT 'ເວລາເລີ່ມສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ, ຖ້າມີ)',
  `end_time` time DEFAULT NULL COMMENT 'ເວລາເລີກສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ, ຖ້າມີ)',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະການມອບໝາຍນີ້: active, inactive',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`class_subject_id`),
  UNIQUE KEY `UQ_ClassSubjects_class_subj` (`class_id`,`subject_id`) COMMENT 'ສົມມຸດວ່າວິຊາໜຶ່ງຖືກມອບໝາຍໃຫ້ຫ້ອງຮຽນໜຶ່ງພຽງຄັ້ງດຽວ (ບໍ່ຂຶ້ນກັບຄູ)',
  KEY `IDX_ClassSubjects_class` (`class_id`),
  KEY `IDX_ClassSubjects_subject` (`subject_id`),
  KEY `IDX_ClassSubjects_teacher` (`teacher_id`),
  KEY `IDX_ClassSubjects_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D32: ຕາຕະລາງເຊື່ອມໂຍງ ຫ້ອງຮຽນ-ວິຊາ-ຄູສອນ';

-- --------------------------------------------------------

--
-- Table structure for table `digital_library_resources`
--

DROP TABLE IF EXISTS `digital_library_resources`;
CREATE TABLE IF NOT EXISTS `digital_library_resources` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຊັບພະຍາກອນ (PK)',
  `title` varchar(255) NOT NULL COMMENT 'ຊື່ ຫຼື ຫົວຂໍ້ຂອງຊັບພະຍາກອນ',
  `author` varchar(255) DEFAULT NULL COMMENT 'ຊື່ຜູ້ແຕ່ງ ຫຼື ຜູ້ສ້າງ',
  `publisher` varchar(255) DEFAULT NULL COMMENT 'ຊື່ສຳນັກພິມ ຫຼື ຜູ້ເຜີຍແຜ່',
  `publication_year` int(11) DEFAULT NULL COMMENT 'ປີທີ່ພິມ ຫຼື ເຜີຍແຜ່ (ຄ.ສ.)',
  `resource_type` enum('book','document','video','audio','image') NOT NULL COMMENT 'ປະເພດຊັບພະຍາກອນ: book, document, video, audio, image',
  `category` varchar(100) DEFAULT NULL COMMENT 'ໝວດໝູ່ (ຕົວຢ່າງ: Science, History, Novel)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍ ຫຼື ເນື້ອຫຍໍ້',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ຊັບພະຍາກອນຕົວຈິງ',
  `file_size` int(11) DEFAULT NULL COMMENT 'ຂະໜາດຂອງໄຟລ໌ (ເປັນ bytes)',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ຮູບຕົວຢ່າງ ຫຼື ໜ້າປົກ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ສາມາດເຂົ້າເຖິງໄດ້)',
  `added_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ເພີ່ມຊັບພະຍາກອນນີ້ (FK ຈາກ Users)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`resource_id`),
  KEY `IDX_DigLibRes_adder` (`added_by`),
  KEY `IDX_DigLibRes_title` (`title`(250)),
  KEY `IDX_DigLibRes_author` (`author`(250)),
  KEY `IDX_DigLibRes_type` (`resource_type`),
  KEY `IDX_DigLibRes_category` (`category`),
  KEY `IDX_DigLibRes_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D54: ຕາຕະລາງເກັບລາຍການຊັບພະຍາກອນໃນຫ້ອງສະໝຸດດິຈິຕອລ';

-- --------------------------------------------------------

--
-- Table structure for table `digital_resource_access`
--

DROP TABLE IF EXISTS `digital_resource_access`;
CREATE TABLE IF NOT EXISTS `digital_resource_access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການເຂົ້າເຖິງ (PK)',
  `resource_id` int(11) NOT NULL COMMENT 'ລະຫັດຊັບພະຍາກອນທີ່ເຂົ້າເຖິງ (FK ຈາກ D54)',
  `user_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ເຂົ້າເຖິງ (FK ຈາກ Users)',
  `access_time` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາທີ່ເຂົ້າເຖິງ',
  `access_type` enum('view','download','print') NOT NULL COMMENT 'ປະເພດການເຂົ້າເຖິງ: view, download, print',
  `device_info` varchar(255) DEFAULT NULL COMMENT 'ຂໍ້ມູນອຸປະກອນທີ່ໃຊ້ເຂົ້າເຖິງ (ເຊັ່ນ: User Agent)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ທີ່ຢູ່ IP ຂອງຜູ້ໃຊ້ທີ່ເຂົ້າເຖິງ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ Log record (ຄື access_time)',
  PRIMARY KEY (`access_id`),
  KEY `IDX_DigResAccess_resource` (`resource_id`),
  KEY `IDX_DigResAccess_user` (`user_id`),
  KEY `IDX_DigResAccess_time` (`access_time`),
  KEY `IDX_DigResAccess_type` (`access_type`),
  KEY `IDX_DigResAccess_ip` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D55: ຕາຕະລາງເກັບ Log ການເຂົ້າເຖິງຊັບພະຍາກອນໃນຫ້ອງສະໝຸດດິຈິຕອລ';

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE IF NOT EXISTS `discounts` (
  `discount_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດສ່ວນຫຼຸດ (PK)',
  `discount_name` varchar(100) NOT NULL COMMENT 'ຊື່ສ່ວນຫຼຸດ (ຕົວຢ່າງ: ສ່ວນຫຼຸດພີ່ນ້ອງ, ທຶນຮຽນດີ)',
  `discount_type` enum('percentage','fixed') NOT NULL COMMENT 'ປະເພດສ່ວນຫຼຸດ: percentage (ເປີເຊັນ), fixed (ຈຳນວນເງິນຄົງທີ່)',
  `discount_value` decimal(10,2) NOT NULL COMMENT 'ຄ່າຂອງສ່ວນຫຼຸດ (ຖ້າປະເພດເປັນ percentage ແມ່ນເກັບ 0-100, ຖ້າ fixed ແມ່ນເກັບຈຳນວນເງິນ)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍ ຫຼື ເງື່ອນໄຂຂອງສ່ວນຫຼຸດ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ຍັງສາມາດໃຊ້ສ່ວນຫຼຸດນີ້ໄດ້, FALSE = ບໍ່ສາມາດໃຊ້ໄດ້ແລ້ວ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`discount_id`),
  UNIQUE KEY `UQ_Discounts_name` (`discount_name`) COMMENT 'ຊື່ສ່ວນຫຼຸດຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_Discounts_type` (`discount_type`),
  KEY `IDX_Discounts_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D42: ຕາຕະລາງເກັບຂໍ້ມູນປະເພດສ່ວນຫຼຸດ';

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
CREATE TABLE IF NOT EXISTS `districts` (
  `district_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດເມືອງ (PK)',
  `district_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ເມືອງ (ພາສາລາວ)',
  `district_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ເມືອງ (ພາສາອັງກິດ)',
  `province_id` int(11) NOT NULL COMMENT 'ລະຫັດແຂວງທີ່ເມືອງນີ້ສັງກັດ (FK ຈາກ Provinces)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `UQ_Districts_province_name_lao` (`province_id`,`district_name_lao`) COMMENT 'ຊື່ເມືອງ (ລາວ) ຕ້ອງບໍ່ຊ້ຳກັນພາຍໃນແຂວງດຽວກັນ',
  UNIQUE KEY `UQ_Districts_province_name_en` (`province_id`,`district_name_en`) COMMENT 'ຊື່ເມືອງ (ອັງກິດ) ຖ້າມີ, ຕ້ອງບໍ່ຊ້ຳກັນພາຍໃນແຂວງດຽວກັນ',
  KEY `IDX_Districts_province` (`province_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D9: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຊື່ເມືອງ';

-- --------------------------------------------------------

--
-- Table structure for table `ethnicities`
--

DROP TABLE IF EXISTS `ethnicities`;
CREATE TABLE IF NOT EXISTS `ethnicities` (
  `ethnicity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຊົນເຜົ່າ (PK)',
  `ethnicity_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ຊົນເຜົ່າ (ພາສາລາວ)',
  `ethnicity_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຊົນເຜົ່າ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`ethnicity_id`),
  UNIQUE KEY `UQ_Ethnicities_name_lao` (`ethnicity_name_lao`),
  UNIQUE KEY `UQ_Ethnicities_name_en` (`ethnicity_name_en`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D13: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຊື່ຊົນເຜົ່າ';

-- --------------------------------------------------------

--
-- Table structure for table `examinations`
--

DROP TABLE IF EXISTS `examinations`;
CREATE TABLE IF NOT EXISTS `examinations` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການສອບເສັງ (PK)',
  `exam_name` varchar(255) NOT NULL COMMENT 'ຊື່ການສອບເສັງ (ຕົວຢ່າງ: ເສັງພາກຮຽນ 1, Quiz ບົດທີ 5)',
  `exam_type` enum('midterm','final','quiz','assignment') NOT NULL COMMENT 'ປະເພດການສອບເສັງ/ປະເມີນ: midterm, final, quiz, assignment',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK ຈາກ Academic_Years)',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມໄລຍະເວລາສອບເສັງ (ຖ້າມີ)',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດໄລຍະເວລາສອບເສັງ (ຖ້າມີ)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບການສອບເສັງ',
  `total_marks` int(11) NOT NULL COMMENT 'ຄະແນນເຕັມສຳລັບການສອບເສັງນີ້',
  `passing_marks` int(11) DEFAULT NULL COMMENT 'ຄະແນນຂັ້ນຕ່ຳເພື່ອຖືວ່າຜ່ານ (ຖ້າມີກຳນົດ)',
  `status` enum('upcoming','ongoing','completed') NOT NULL DEFAULT 'upcoming' COMMENT 'ສະຖານະການສອບເສັງ: upcoming, ongoing, completed',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`exam_id`),
  UNIQUE KEY `UQ_Exams_name_year` (`academic_year_id`,`exam_name`) USING HASH COMMENT 'ຊື່ການສອບເສັງຕ້ອງບໍ່ຊ້ຳກັນພາຍໃນສົກຮຽນດຽວກັນ',
  KEY `IDX_Exams_acad_year` (`academic_year_id`),
  KEY `IDX_Exams_type` (`exam_type`),
  KEY `IDX_Exams_status` (`status`),
  KEY `IDX_Exams_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D36: ຕາຕະລາງເກັບຂໍ້ມູນ Metadata ຂອງການສອບເສັງ';

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດລາຍຈ່າຍ (PK)',
  `expense_category` varchar(100) NOT NULL COMMENT 'ໝວດໝູ່ລາຍຈ່າຍ (ຕົວຢ່າງ: ອຸປະກອນການສຶກສາ, ຄ່າໄຟຟ້າ, ຄ່າສ້ອມແປງ)',
  `amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນທີ່ຈ່າຍອອກ',
  `expense_date` date NOT NULL COMMENT 'ວັນທີທີ່ເກີດລາຍຈ່າຍ ຫຼື ວັນທີບັນທຶກ',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດເພີ່ມເຕີມກ່ຽວກັບລາຍຈ່າຍ',
  `payment_method` enum('cash','bank_transfer','other') DEFAULT NULL COMMENT 'ວິທີການຈ່າຍເງິນ: cash, bank_transfer, other',
  `receipt_number` varchar(50) DEFAULT NULL COMMENT 'ເລກທີ່ໃບບິນ ຫຼື ເອກະສານອ້າງອີງ (ຖ້າມີ)',
  `receipt_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ຮູບພາບໃບບິນ (ຖ້າມີ)',
  `approved_by` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ອະນຸມັດລາຍຈ່າຍ (FK ຈາກ Users). ອາດຈະ NULL.',
  `created_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ບັນທຶກລາຍຈ່າຍ (FK ຈາກ Users)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`expense_id`),
  KEY `IDX_Expenses_approver` (`approved_by`),
  KEY `IDX_Expenses_creator` (`created_by`),
  KEY `IDX_Expenses_category` (`expense_category`),
  KEY `IDX_Expenses_date` (`expense_date`),
  KEY `IDX_Expenses_payment_method` (`payment_method`),
  KEY `IDX_Expenses_receipt_number` (`receipt_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D44: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຈ່າຍຂອງໂຮງຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `extracurricular_activities`
--

DROP TABLE IF EXISTS `extracurricular_activities`;
CREATE TABLE IF NOT EXISTS `extracurricular_activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດກິດຈະກຳ (PK)',
  `activity_name` varchar(255) NOT NULL COMMENT 'ຊື່ກິດຈະກຳ (ຕົວຢ່າງ: ຊົມລົມບານເຕະ, ແຂ່ງຂັນວິທະຍາສາດ)',
  `activity_type` varchar(100) DEFAULT NULL COMMENT 'ປະເພດກິດຈະກຳ (ຕົວຢ່າງ: ຊົມລົມ, ກິລາ, ອາສາສະໝັກ)',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດກ່ຽວກັບກິດຈະກຳ',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມຕົ້ນຂອງກິດຈະກຳ',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດຂອງກິດຈະກຳ (NULL ໝາຍເຖິງບໍ່ມີກຳນົດ)',
  `schedule` varchar(255) DEFAULT NULL COMMENT 'ຕາຕະລາງເວລາຈັດກິດຈະກຳ (ແບບຂໍ້ຄວາມ, ເຊັ່ນ: ທຸກໆວັນສຸກ 15:00)',
  `location` varchar(255) DEFAULT NULL COMMENT 'ສະຖານທີ່ຈັດກິດຈະກຳ',
  `max_participants` int(11) DEFAULT NULL COMMENT 'ຈຳນວນຜູ້ເຂົ້າຮ່ວມສູງສຸດ (NULL ໝາຍເຖິງບໍ່ຈຳກັດ)',
  `coordinator_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ປະສານງານ/ຮັບຜິດຊອບກິດຈະກຳ (FK ຈາກ Users). ອາດຈະ NULL.',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນທີ່ກິດຈະກຳນີ້ຈັດຂຶ້ນ (FK ຈາກ Academic_Years)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ກຳລັງເປີດຮັບ ຫຼື ດຳເນີນຢູ່)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`activity_id`),
  UNIQUE KEY `UQ_ExtraAct_name_year` (`academic_year_id`,`activity_name`) USING HASH COMMENT 'ຊື່ກິດຈະກຳຕ້ອງບໍ່ຊ້ຳກັນພາຍໃນສົກຮຽນດຽວກັນ',
  KEY `IDX_ExtraAct_coord` (`coordinator_id`),
  KEY `IDX_ExtraAct_acad_year` (`academic_year_id`),
  KEY `IDX_ExtraAct_type` (`activity_type`),
  KEY `IDX_ExtraAct_active` (`is_active`),
  KEY `IDX_ExtraAct_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D56: ຕາຕະລາງເກັບຂໍ້ມູນກິດຈະກຳນອກຫຼັກສູດ';

-- --------------------------------------------------------

--
-- Table structure for table `fee_types`
--

DROP TABLE IF EXISTS `fee_types`;
CREATE TABLE IF NOT EXISTS `fee_types` (
  `fee_type_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດປະເພດຄ່າທຳນຽມ (PK)',
  `fee_name` varchar(100) NOT NULL COMMENT 'ຊື່ປະເພດຄ່າທຳນຽມ (ຕົວຢ່າງ: ຄ່າຮຽນພາກຮຽນ 1, ຄ່າກິດຈະກຳ)',
  `fee_description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍລາຍລະອຽດກ່ຽວກັບຄ່າທຳນຽມ',
  `amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນມາດຕະຖານສຳລັບຄ່າທຳນຽມປະເພດນີ້',
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ເປັນຄ່າທຳນຽມທີ່ເກັບປະຈຳ (TRUE) ຫຼື ເກັບຄັ້ງດຽວ (FALSE)',
  `recurring_interval` enum('monthly','quarterly','yearly') DEFAULT NULL COMMENT 'ໄລຍະເວລາການເກັບຫາກເປັນແບບປະຈຳ (monthly, quarterly, yearly)',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ເປັນຄ່າທຳນຽມທີ່ບັງຄັບຈ່າຍ (TRUE) ຫຼື ທາງເລືອກ (FALSE)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ຍັງໃຊ້ງານ, FALSE = ບໍ່ໃຊ້ແລ້ວ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`fee_type_id`),
  UNIQUE KEY `UQ_FeeTypes_name` (`fee_name`) COMMENT 'ຊື່ປະເພດຄ່າທຳນຽມຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_FeeTypes_recurring` (`is_recurring`),
  KEY `IDX_FeeTypes_mandatory` (`is_mandatory`),
  KEY `IDX_FeeTypes_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D39: ຕາຕະລາງເກັບຂໍ້ມູນປະເພດຄ່າທຳນຽມຕ່າງໆ';

-- --------------------------------------------------------

--
-- Table structure for table `generated_reports`
--

DROP TABLE IF EXISTS `generated_reports`;
CREATE TABLE IF NOT EXISTS `generated_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດລາຍງານທີ່ຖືກສ້າງ (PK)',
  `report_name` varchar(255) NOT NULL COMMENT 'ຊື່ລາຍງານ (ທີ່ຜູ້ໃຊ້ຕັ້ງ ຫຼື ລະບົບສ້າງໃຫ້)',
  `template_id` int(11) NOT NULL COMMENT 'ລະຫັດແມ່ແບບທີ່ໃຊ້ສ້າງລາຍງານ (FK ຈາກ Report_Templates)',
  `report_type` varchar(50) DEFAULT NULL COMMENT 'ປະເພດຂອງລາຍງານ (ຄວນຈະກົງກັບ template_type ໃນ D46)',
  `report_data` longtext DEFAULT NULL COMMENT 'ຂໍ້ມູນດິບທີ່ນຳໃຊ້ໃນການສ້າງລາຍງານ (ອາດຈະເປັນ JSON, XML). ໝາຍເຫດ: ອາດຈະໃຊ້ພື້ນທີ່ຫຼາຍ.',
  `report_format` enum('pdf','excel','word','html') NOT NULL COMMENT 'ຮູບແບບ (format) ຂອງໄຟລ໌ລາຍງານຜົນລັບ',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ລາຍງານທີ່ຖືກບັນທຶກ (ຖ້າມີ)',
  `generated_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ສັ່ງໃຫ້ສ້າງລາຍງານ (FK ຈາກ Users)',
  `generated_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາທີ່ສ້າງລາຍງານສຳເລັດ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`report_id`),
  KEY `IDX_GenReports_template` (`template_id`),
  KEY `IDX_GenReports_generator` (`generated_by`),
  KEY `IDX_GenReports_name` (`report_name`(250)),
  KEY `IDX_GenReports_type` (`report_type`),
  KEY `IDX_GenReports_format` (`report_format`),
  KEY `IDX_GenReports_generated_at` (`generated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D47: ຕາຕະລາງເກັບປະຫວັດລາຍງານທີ່ລະບົບສ້າງຂຶ້ນ';

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຄະແນນ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `class_id` int(11) NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK ຈາກ Classes)',
  `subject_id` int(11) NOT NULL COMMENT 'ລະຫັດວິຊາ (FK ຈາກ Subjects)',
  `exam_id` int(11) NOT NULL COMMENT 'ລະຫັດການສອບເສັງ (FK ຈາກ Examinations)',
  `marks` decimal(5,2) NOT NULL COMMENT 'ຄະແນນທີ່ນັກຮຽນໄດ້ຮັບ',
  `grade_letter` varchar(5) DEFAULT NULL COMMENT 'ຄະແນນຕົວອັກສອນ (ເກຣດ) (ເຊັ່ນ: A, B+, C)',
  `comments` text DEFAULT NULL COMMENT 'ໝາຍເຫດ ຫຼື ຄຳຄິດເຫັນຈາກຄູສອນ',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ສະຖານະການເຜີຍແຜ່ໃຫ້ນັກຮຽນ/ຜູ້ປົກຄອງເຫັນ (TRUE/FALSE)',
  `graded_by` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ໃຫ້ຄະແນນ (FK ຈາກ Users). ອາດຈະ NULL.',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `UQ_Grades_student_exam_subject` (`student_id`,`exam_id`,`subject_id`) COMMENT 'ນັກຮຽນໜຶ່ງຄົນຄວນມີຄະແນນສຳລັບການເສັງໜຶ່ງຄັ້ງຂອງວິຊາໜຶ່ງພຽງອັນດຽວ',
  KEY `IDX_Grades_student` (`student_id`),
  KEY `IDX_Grades_class` (`class_id`),
  KEY `IDX_Grades_subject` (`subject_id`),
  KEY `IDX_Grades_exam` (`exam_id`),
  KEY `IDX_Grades_grader` (`graded_by`),
  KEY `IDX_Grades_published` (`is_published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D37: ຕາຕະລາງເກັບຂໍ້ມູນຄະແນນຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

DROP TABLE IF EXISTS `income`;
CREATE TABLE IF NOT EXISTS `income` (
  `income_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດລາຍຮັບ (PK)',
  `income_category` varchar(100) NOT NULL COMMENT 'ໝວດໝູ່ລາຍຮັບ (ຕົວຢ່າງ: ເງິນບໍລິຈາກ, ລາຍຮັບຮ້ານຄ້າ, ຄ່າເຊົ່າ)',
  `amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນທີ່ໄດ້ຮັບ',
  `income_date` date NOT NULL COMMENT 'ວັນທີທີ່ໄດ້ຮັບລາຍຮັບ',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດ ຫຼື ແຫຼ່ງທີ່ມາຂອງລາຍຮັບ',
  `payment_method` enum('cash','bank_transfer','qr_code','other') DEFAULT NULL COMMENT 'ວິທີການຮັບເງິນ: cash, bank_transfer, qr_code, other',
  `receipt_number` varchar(50) DEFAULT NULL COMMENT 'ເລກທີ່ໃບຮັບເງິນ ຫຼື ເອກະສານອ້າງອີງ (ຖ້າມີ)',
  `received_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ຮັບເງິນ ຫຼື ຜູ້ບັນທຶກ (FK ຈາກ Users)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`income_id`),
  KEY `IDX_Income_receiver` (`received_by`),
  KEY `IDX_Income_category` (`income_category`),
  KEY `IDX_Income_date` (`income_date`),
  KEY `IDX_Income_payment_method` (`payment_method`),
  KEY `IDX_Income_receipt_number` (`receipt_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D45: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຮັບອື່ນໆຂອງໂຮງຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຂໍ້ຄວາມ (PK)',
  `sender_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ສົ່ງ (FK ຈາກ Users)',
  `receiver_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ຮັບ (FK ຈາກ Users)',
  `subject` varchar(255) DEFAULT NULL COMMENT 'ຫົວຂໍ້ຂອງຂໍ້ຄວາມ',
  `message_content` text DEFAULT NULL COMMENT 'ເນື້ອໃນຂອງຂໍ້ຄວາມ',
  `read_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ສະຖານະການອ່ານ (TRUE = ອ່ານແລ້ວ, FALSE = ຍັງບໍ່ອ່ານ)',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'ວັນທີ ແລະ ເວລາທີ່ອ່ານຂໍ້ຄວາມ (NULL ຖ້າຍັງບໍ່ອ່ານ)',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ແນບ (ຖ້າມີ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ ຫຼື ສົ່ງຂໍ້ຄວາມ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ (ເຊັ່ນ: ເມື່ອອ່ານ)',
  PRIMARY KEY (`message_id`),
  KEY `IDX_Messages_sender` (`sender_id`),
  KEY `IDX_Messages_receiver` (`receiver_id`),
  KEY `IDX_Messages_receiver_read` (`receiver_id`,`read_status`),
  KEY `IDX_Messages_created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D48: ຕາຕະລາງເກັບຂໍ້ມູນຂໍ້ຄວາມພາຍໃນລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `nationalities`
--

DROP TABLE IF EXISTS `nationalities`;
CREATE TABLE IF NOT EXISTS `nationalities` (
  `nationality_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດສັນຊາດ (PK)',
  `nationality_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ສັນຊາດ (ພາສາລາວ)',
  `nationality_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ສັນຊາດ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`nationality_id`),
  UNIQUE KEY `UQ_Nationalities_name_lao` (`nationality_name_lao`),
  UNIQUE KEY `UQ_Nationalities_name_en` (`nationality_name_en`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D11: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຊື່ສັນຊາດ';

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການແຈ້ງເຕືອນ (PK)',
  `user_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ໄດ້ຮັບການແຈ້ງເຕືອນ (FK ຈາກ Users)',
  `title` varchar(255) NOT NULL COMMENT 'ຫົວຂໍ້ການແຈ້ງເຕືອນ (ເພື່ອສະແດງຜົນແບບຫຍໍ້)',
  `content` text DEFAULT NULL COMMENT 'ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງການແຈ້ງເຕືອນ (ຖ້າມີ)',
  `notification_type` varchar(50) DEFAULT NULL COMMENT 'ປະເພດຂອງການແຈ້ງເຕືອນ (ຕົວຢ່າງ: new_message, request_update, fee_due)',
  `related_id` int(11) DEFAULT NULL COMMENT 'ID ຂອງຂໍ້ມູນທີ່ກ່ຽວຂ້ອງ (ຕົວຢ່າງ: message_id, request_id, student_fee_id)',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ສະຖານະການອ່ານ (TRUE = ອ່ານແລ້ວ, FALSE = ຍັງບໍ່ອ່ານ)',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'ວັນທີ ແລະ ເວລາທີ່ອ່ານ (NULL ຖ້າຍັງບໍ່ອ່ານ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງການແຈ້ງເຕືອນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ (ເຊັ່ນ: ເມື່ອ is_read ປ່ຽນ)',
  PRIMARY KEY (`notification_id`),
  KEY `IDX_Notifications_user` (`user_id`),
  KEY `IDX_Notifications_user_read` (`user_id`,`is_read`),
  KEY `IDX_Notifications_related` (`notification_type`,`related_id`),
  KEY `IDX_Notifications_created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D50: ຕາຕະລາງເກັບຂໍ້ມູນການແຈ້ງເຕືອນພາຍໃນລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
CREATE TABLE IF NOT EXISTS `parents` (
  `parent_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຜູ້ປົກຄອງ (PK)',
  `first_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ຜູ້ປົກຄອງ (ພາສາລາວ)',
  `last_name_lao` varchar(100) NOT NULL COMMENT 'ນາມສະກຸນຜູ້ປົກຄອງ (ພາສາລາວ)',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຜູ້ປົກຄອງ (ພາສາອັງກິດ)',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'ນາມສະກຸນຜູ້ປົກຄອງ (ພາສາອັງກິດ)',
  `gender` enum('male','female','other') DEFAULT NULL COMMENT 'ເພດ',
  `date_of_birth` date DEFAULT NULL COMMENT 'ວັນເດືອນປີເກີດ',
  `national_id` varchar(50) DEFAULT NULL COMMENT 'ເລກບັດປະຈຳຕົວປະຊາຊົນ',
  `occupation` varchar(100) DEFAULT NULL COMMENT 'ອາຊີບ',
  `workplace` varchar(255) DEFAULT NULL COMMENT 'ສະຖານທີ່ເຮັດວຽກ',
  `education_level` varchar(100) DEFAULT NULL COMMENT 'ລະດັບການສຶກສາສູງສຸດ',
  `income_level` varchar(100) DEFAULT NULL COMMENT 'ລະດັບລາຍຮັບ (ອາດຈະເປັນຂໍ້ມູນລະອຽດອ່ອນ)',
  `phone` varchar(20) NOT NULL COMMENT 'ເບີໂທລະສັບຫຼັກ (ສຳຄັນ)',
  `alternative_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບສຳຮອງ',
  `email` varchar(100) DEFAULT NULL COMMENT 'ອີເມວ (ສຳຄັນຖ້າຕ້ອງການເຊື່ອມກັບບັນຊີຜູ້ໃຊ້)',
  `village_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK ຈາກ Villages)',
  `district_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK ຈາກ Districts)',
  `province_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK ຈາກ Provinces)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ)',
  `user_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດບັນຊີຜູ້ໃຊ້ຂອງຜູ້ປົກຄອງ (FK ຈາກ Users). ອາດຈະ NULL.',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`parent_id`),
  UNIQUE KEY `UQ_Parents_national_id` (`national_id`),
  UNIQUE KEY `UQ_Parents_email` (`email`),
  UNIQUE KEY `UQ_Parents_user` (`user_id`),
  KEY `IDX_Parents_name_lao` (`last_name_lao`,`first_name_lao`),
  KEY `IDX_Parents_name_en` (`last_name_en`,`first_name_en`),
  KEY `IDX_Parents_village` (`village_id`),
  KEY `IDX_Parents_district` (`district_id`),
  KEY `IDX_Parents_province` (`province_id`),
  KEY `IDX_Parents_user` (`user_id`),
  KEY `IDX_Parents_phone` (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D25: ຕາຕະລາງເກັບຂໍ້ມູນຜູ້ປົກຄອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການຊຳລະເງິນ (PK)',
  `student_fee_id` int(11) NOT NULL COMMENT 'ລະຫັດລາຍການຄ່າທຳນຽມທີ່ຊຳລະ (FK ຈາກ Student_Fees)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students) - ອາດຈະຊ້ຳຊ້ອນກັບຂໍ້ມູນໃນ Student_Fees ແຕ່ມີໄວ້ເພື່ອສະດວກ',
  `amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນທີ່ຊຳລະໃນຄັ້ງນີ້',
  `payment_date` date NOT NULL COMMENT 'ວັນທີຊຳລະເງິນ',
  `payment_method` enum('cash','bank_transfer','qr_code','other') NOT NULL COMMENT 'ວິທີການຊຳລະ: cash, bank_transfer, qr_code, other',
  `transaction_id` varchar(100) DEFAULT NULL COMMENT 'ລະຫັດອ້າງອີງທຸລະກຳ (ສຳລັບການໂອນ ຫຼື QR)',
  `receipt_number` varchar(50) DEFAULT NULL COMMENT 'ເລກທີ່ໃບຮັບເງິນທີ່ອອກໂດຍໂຮງຮຽນ',
  `payment_note` text DEFAULT NULL COMMENT 'ໝາຍເຫດເພີ່ມເຕີມກ່ຽວກັບການຊຳລະ',
  `received_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ຮັບເງິນ/ບັນທຶກ (FK ຈາກ Users)',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະຢືນຢັນການໄດ້ຮັບເງິນ (TRUE = ຢືນຢັນແລ້ວ, FALSE = ລໍຖ້າກວດສອບ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`payment_id`),
  KEY `IDX_Payments_stud_fee` (`student_fee_id`),
  KEY `IDX_Payments_student` (`student_id`),
  KEY `IDX_Payments_receiver` (`received_by`),
  KEY `IDX_Payments_date` (`payment_date`),
  KEY `IDX_Payments_method` (`payment_method`),
  KEY `IDX_Payments_receipt` (`receipt_number`),
  KEY `IDX_Payments_transaction` (`transaction_id`),
  KEY `IDX_Payments_confirmed` (`is_confirmed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D41: ຕາຕະລາງເກັບຂໍ້ມູນທຸລະກຳການຊຳລະເງິນ';

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດສິດທິ (PK)',
  `permission_name` varchar(100) NOT NULL COMMENT 'ຊື່ສິດທິ (ເຊັ່ນ: create_user, edit_grades, view_reports)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍວ່າສິດທິນີ້ອະນຸຍາດໃຫ້ເຮັດຫຍັງ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `UQ_Permissions_permission_name` (`permission_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D3: ຕາຕະລາງເກັບຂໍ້ມູນສິດທິການໃຊ້ງານລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

DROP TABLE IF EXISTS `provinces`;
CREATE TABLE IF NOT EXISTS `provinces` (
  `province_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດແຂວງ (PK)',
  `province_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ແຂວງ (ພາສາລາວ)',
  `province_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ແຂວງ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`province_id`),
  UNIQUE KEY `UQ_Provinces_name_lao` (`province_name_lao`),
  UNIQUE KEY `UQ_Provinces_name_en` (`province_name_en`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D8: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຊື່ແຂວງ';

-- --------------------------------------------------------

--
-- Table structure for table `religions`
--

DROP TABLE IF EXISTS `religions`;
CREATE TABLE IF NOT EXISTS `religions` (
  `religion_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດສາສະໜາ (PK)',
  `religion_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ສາສະໜາ (ພາສາລາວ)',
  `religion_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ສາສະໜາ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`religion_id`),
  UNIQUE KEY `UQ_Religions_name_lao` (`religion_name_lao`),
  UNIQUE KEY `UQ_Religions_name_en` (`religion_name_en`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D12: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຊື່ສາສະໜາ';

-- --------------------------------------------------------

--
-- Table structure for table `report_templates`
--

DROP TABLE IF EXISTS `report_templates`;
CREATE TABLE IF NOT EXISTS `report_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດແມ່ແບບລາຍງານ (PK)',
  `template_name` varchar(100) NOT NULL COMMENT 'ຊື່ແມ່ແບບລາຍງານ (ຕົວຢ່າງ: ໃບຄະແນນນັກຮຽນ)',
  `template_type` varchar(50) DEFAULT NULL COMMENT 'ປະເພດຂອງແມ່ແບບ ຫຼື ລາຍງານ (ຕົວຢ່າງ: Transcript, Attendance Report)',
  `template_content` longtext DEFAULT NULL COMMENT 'ເນື້ອຫາ ຫຼື ໂຄງສ້າງຂອງແມ່ແບບ (ອາດຈະເປັນ HTML, XML, JSON, ...)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ແມ່ແບບນີ້ສາມາດໃຊ້ງານໄດ້)',
  `created_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ສ້າງ ຫຼື ອັບໂຫຼດແມ່ແບບ (FK ຈາກ Users)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `UQ_ReportTemplates_name` (`template_name`) COMMENT 'ຊື່ແມ່ແບບຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_ReportTemplates_creator` (`created_by`),
  KEY `IDX_ReportTemplates_type` (`template_type`),
  KEY `IDX_ReportTemplates_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D46: ຕາຕະລາງເກັບແມ່ແບບສຳລັບສ້າງລາຍງານ';

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຄຳຮ້ອງ (PK)',
  `user_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ຍື່ນຄຳຮ້ອງ (FK ຈາກ Users)',
  `request_type` varchar(100) NOT NULL COMMENT 'ປະເພດຄຳຮ້ອງ (ຕົວຢ່າງ: Document Request, Leave Request)',
  `subject` varchar(255) NOT NULL COMMENT 'ຫົວຂໍ້ຂອງຄຳຮ້ອງ',
  `content` text DEFAULT NULL COMMENT 'ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງຄຳຮ້ອງ',
  `status` enum('pending','approved','rejected','processing') NOT NULL DEFAULT 'pending' COMMENT 'ສະຖານະຂອງຄຳຮ້ອງ: pending, approved, rejected, processing',
  `response` text DEFAULT NULL COMMENT 'ຄຳຕອບ ຫຼື ຄຳຄິດເຫັນຈາກຜູ້ດຳເນີນການ',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ແນບທີ່ຜູ້ຮ້ອງສົ່ງມາ (ຖ້າມີ)',
  `handled_by` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ດຳເນີນການ/ຜູ້ອະນຸມັດ (FK ຈາກ Users). ອາດຈະ NULL ຕອນທຳອິດ.',
  `handled_at` timestamp NULL DEFAULT NULL COMMENT 'ວັນທີ ແລະ ເວລາທີ່ດຳເນີນການສຳເລັດ (NULL ຖ້າ status=pending)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຄຳຮ້ອງ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ (ເຊັ່ນ: ເມື່ອປ່ຽນ status)',
  PRIMARY KEY (`request_id`),
  KEY `IDX_Requests_user` (`user_id`),
  KEY `IDX_Requests_handler` (`handled_by`),
  KEY `IDX_Requests_type` (`request_type`),
  KEY `IDX_Requests_status` (`status`),
  KEY `IDX_Requests_created_at` (`created_at`),
  KEY `IDX_Requests_handled_at` (`handled_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D51: ຕາຕະລາງເກັບຂໍ້ມູນຄຳຮ້ອງຕ່າງໆໃນລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດບົດບາດ (PK)',
  `role_name` varchar(50) NOT NULL COMMENT 'ຊື່ບົດບາດ (ເຊັ່ນ: Admin, Teacher, Student, Parent)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍໜ້າທີ່ຂອງບົດບາດ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `UQ_Roles_role_name` (`role_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D2: ຕາຕະລາງເກັບຂໍ້ມູນບົດບາດຜູ້ໃຊ້';

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_permission_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການເຊື່ອມໂຍງ (PK)',
  `role_id` int(11) NOT NULL COMMENT 'ລະຫັດບົດບາດ (FK ຈາກ Roles)',
  `permission_id` int(11) NOT NULL COMMENT 'ລະຫັດສິດທິ (FK ຈາກ Permissions)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`role_permission_id`),
  UNIQUE KEY `UQ_RolePermissions_role_perm` (`role_id`,`permission_id`) COMMENT 'ແຕ່ລະບົດບາດສາມາດມີສິດທິແຕ່ລະອັນໄດ້ພຽງຄັ້ງດຽວ',
  KEY `IDX_RolePermissions_permission` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D4: ຕາຕະລາງເຊື່ອມໂຍງລະຫວ່າງບົດບາດ ແລະ ສິດທິ';

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຕາຕະລາງສອນ (PK)',
  `class_id` int(11) NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK ຈາກ Classes)',
  `subject_id` int(11) NOT NULL COMMENT 'ລະຫັດວິຊາ (FK ຈາກ Subjects)',
  `teacher_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຄູສອນ (FK ຈາກ Teachers). ອາດຈະ NULL.',
  `day_of_week` varchar(20) NOT NULL COMMENT 'ມື້ໃນອາທິດ (ຕົວຢ່າງ: Monday, Tuesday, ...)',
  `start_time` time NOT NULL COMMENT 'ເວລາເລີ່ມສອນ',
  `end_time` time NOT NULL COMMENT 'ເວລາເລີກສອນ',
  `room` varchar(50) DEFAULT NULL COMMENT 'ຊື່/ເລກຫ້ອງ ຫຼື ສະຖານທີ່ສອນ',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK ຈາກ Academic_Years)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະຕາຕະລາງນີ້ (TRUE = ໃຊ້ງານ, FALSE = ບໍ່ໃຊ້)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`schedule_id`),
  UNIQUE KEY `UQ_Schedules_class_time` (`academic_year_id`,`class_id`,`day_of_week`,`start_time`) COMMENT 'ປ້ອງກັນຫ້ອງຮຽນມີສອງວິຊາຮຽນໃນເວລາຊ້ອນກັນ',
  UNIQUE KEY `UQ_Schedules_room_time` (`academic_year_id`,`room`,`day_of_week`,`start_time`) COMMENT 'ປ້ອງກັນຫ້ອງຮຽນຖືກໃຊ້ຊ້ອນກັນໃນເວລາໃດໜຶ່ງ (ເມື່ອ room ບໍ່ແມ່ນ NULL)',
  KEY `IDX_Schedules_class` (`class_id`),
  KEY `IDX_Schedules_subject` (`subject_id`),
  KEY `IDX_Schedules_teacher` (`teacher_id`),
  KEY `IDX_Schedules_acad_year` (`academic_year_id`),
  KEY `IDX_Schedules_day` (`day_of_week`),
  KEY `IDX_Schedules_room` (`room`),
  KEY `IDX_Schedules_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D35: ຕາຕະລາງເກັບຂໍ້ມູນຕາຕະລາງສອນລະອຽດ';

-- --------------------------------------------------------

--
-- Table structure for table `school_store_items`
--

DROP TABLE IF EXISTS `school_store_items`;
CREATE TABLE IF NOT EXISTS `school_store_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດສິນຄ້າ (PK)',
  `item_name` varchar(255) NOT NULL COMMENT 'ຊື່ສິນຄ້າ',
  `item_code` varchar(50) DEFAULT NULL COMMENT 'ລະຫັດສິນຄ້າ/ບາໂຄດ (ຖ້າມີ)',
  `category` varchar(100) DEFAULT NULL COMMENT 'ໝວດໝູ່ສິນຄ້າ (ຕົວຢ່າງ: ເຄື່ອງຂຽນ, ເຄື່ອງແບບ)',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດເພີ່ມເຕີມກ່ຽວກັບສິນຄ້າ',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາຂາຍຕໍ່ໜ່ວຍ',
  `stock_quantity` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນສິນຄ້າທີ່ມີໃນສາງປັດຈຸບັນ',
  `reorder_level` int(11) DEFAULT NULL COMMENT 'ລະດັບຄົງເຫຼືອຂັ້ນຕ່ຳທີ່ຄວນສັ່ງຊື້ໃໝ່',
  `item_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ຮູບພາບສິນຄ້າ (ຖ້າມີ)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ຍັງມີຂາຍຢູ່)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `UQ_StoreItems_code` (`item_code`) COMMENT 'ລະຫັດສິນຄ້າ (ຖ້າມີ) ຕ້ອງບໍ່ຊ້ຳກັນ',
  UNIQUE KEY `UQ_StoreItems_name` (`item_name`) USING HASH COMMENT 'ຊື່ສິນຄ້າຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_StoreItems_category` (`category`),
  KEY `IDX_StoreItems_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D52: ຕາຕະລາງເກັບຂໍ້ມູນ Master Data ຂອງສິນຄ້າໃນຮ້ານຄ້າ';

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການຕັ້ງຄ່າ (PK)',
  `setting_key` varchar(100) NOT NULL COMMENT 'ຊື່ Key ທີ່ບໍ່ຊ້ຳກັນສຳລັບການຕັ້ງຄ່າ (ຕົວຢ່າງ: school_name, current_academic_year_id)',
  `setting_value` text DEFAULT NULL COMMENT 'ຄ່າຂອງການຕັ້ງຄ່າ (ອາດຈະເກັບເປັນ String, JSON, etc.)',
  `setting_group` varchar(50) DEFAULT NULL COMMENT 'ກຸ່ມຂອງການຕັ້ງຄ່າ ເພື່ອຈັດໝວດໝູ່ (ຕົວຢ່າງ: General, Academic, Finance)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍວ່າການຕັ້ງຄ່ານີ້ໃຊ້ສຳລັບຫຍັງ',
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ກຳນົດວ່າເປັນການຕັ້ງຄ່າຫຼັກຂອງລະບົບ ຫຼື ບໍ່ (TRUE=ແມ່ນ, ບໍ່ຄວນລຶບ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `UQ_Settings_key` (`setting_key`) COMMENT 'Key ຂອງການຕັ້ງຄ່າຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_Settings_group` (`setting_group`),
  KEY `IDX_Settings_system` (`is_system`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D58: ຕາຕະລາງເກັບຄ່າ Configuration ຕ່າງໆຂອງລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `store_sales`
--

DROP TABLE IF EXISTS `store_sales`;
CREATE TABLE IF NOT EXISTS `store_sales` (
  `sale_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການຂາຍ (PK)',
  `item_id` int(11) NOT NULL COMMENT 'ລະຫັດສິນຄ້າທີ່ຂາຍ (FK ຈາກ School_Store_Items)',
  `quantity` int(11) NOT NULL COMMENT 'ຈຳນວນທີ່ຂາຍ',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາຕໍ່ໜ່ວຍ (ໃນເວລາຂາຍ)',
  `total_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາລວມກ່ອນສ່ວນຫຼຸດ (ຄຳນວນ: quantity * unit_price)',
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ສ່ວນຫຼຸດສຳລັບລາຍການນີ້ (ຖ້າມີ)',
  `final_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາສຸດທິຫຼັງສ່ວນຫຼຸດ (ຄຳນວນ: total_price - discount)',
  `buyer_type` enum('student','teacher','parent','other') DEFAULT NULL COMMENT 'ປະເພດຜູ້ຊື້: student, teacher, parent, other',
  `buyer_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ຊື້ (ອາດຈະອ້າງອີງເຖິງ Students, Teachers, Parents ຂຶ້ນກັບ buyer_type, ຫຼື NULL)',
  `sale_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາທີ່ຂາຍ',
  `payment_method` enum('cash','credit','other') NOT NULL DEFAULT 'cash' COMMENT 'ວິທີການຊຳລະ: cash, credit, other',
  `sold_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ຂາຍ ຫຼື ຜູ້ບັນທຶກ (FK ຈາກ Users)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`sale_id`),
  KEY `IDX_StoreSales_item` (`item_id`),
  KEY `IDX_StoreSales_seller` (`sold_by`),
  KEY `IDX_StoreSales_buyer` (`buyer_type`,`buyer_id`),
  KEY `IDX_StoreSales_date` (`sale_date`),
  KEY `IDX_StoreSales_payment_method` (`payment_method`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D53: ຕາຕະລາງເກັບຂໍ້ມູນທຸລະກຳການຂາຍສິນຄ້າ';

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດນັກຮຽນ (PK)',
  `student_code` varchar(20) NOT NULL COMMENT 'ລະຫັດປະຈຳຕົວນັກຮຽນ',
  `first_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ນັກຮຽນ (ພາສາລາວ)',
  `last_name_lao` varchar(100) NOT NULL COMMENT 'ນາມສະກຸນນັກຮຽນ (ພາສາລາວ)',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ນັກຮຽນ (ພາສາອັງກິດ)',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'ນາມສະກຸນນັກຮຽນ (ພາສາອັງກິດ)',
  `nickname` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຫຼິ້ນ',
  `gender` enum('male','female','other') NOT NULL COMMENT 'ເພດ: male, female, other',
  `date_of_birth` date NOT NULL COMMENT 'ວັນເດືອນປີເກີດ',
  `nationality_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດສັນຊາດ (FK ຈາກ Nationalities)',
  `religion_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດສາສະໜາ (FK ຈາກ Religions)',
  `ethnicity_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຊົນເຜົ່າ (FK ຈາກ Ethnicities)',
  `village_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK ຈາກ Villages)',
  `district_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK ຈາກ Districts)',
  `province_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK ຈາກ Provinces)',
  `current_address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ ເລກເຮືອນ, ຮ່ອມ, ...)',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌',
  `blood_type` enum('A','B','AB','O','unknown') DEFAULT 'unknown' COMMENT 'ກຸ່ມເລືອດ: A, B, AB, O, unknown',
  `status` enum('active','inactive','graduated','transferred') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະນັກຮຽນ: active, inactive, graduated, transferred',
  `user_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດບັນຊີຜູ້ໃຊ້ຂອງນັກຮຽນ (FK ຈາກ Users). ອາດຈະ NULL ຖ້ານັກຮຽນຍັງບໍ່ມີບັນຊີ.',
  `admission_date` date NOT NULL COMMENT 'ວັນທີເຂົ້າຮຽນ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `UQ_Students_code` (`student_code`),
  UNIQUE KEY `UQ_Students_user` (`user_id`),
  KEY `IDX_Students_name_lao` (`last_name_lao`,`first_name_lao`),
  KEY `IDX_Students_name_en` (`last_name_en`,`first_name_en`),
  KEY `IDX_Students_nationality` (`nationality_id`),
  KEY `IDX_Students_religion` (`religion_id`),
  KEY `IDX_Students_ethnicity` (`ethnicity_id`),
  KEY `IDX_Students_village` (`village_id`),
  KEY `IDX_Students_district` (`district_id`),
  KEY `IDX_Students_province` (`province_id`),
  KEY `IDX_Students_user` (`user_id`),
  KEY `IDX_Students_status` (`status`),
  KEY `IDX_Students_admission_date` (`admission_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D14: ຕາຕະລາງຫຼັກເກັບຂໍ້ມູນນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_achievements`
--

DROP TABLE IF EXISTS `student_achievements`;
CREATE TABLE IF NOT EXISTS `student_achievements` (
  `achievement_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຜົນງານ/ລາງວັນ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `achievement_type` varchar(100) DEFAULT NULL COMMENT 'ປະເພດຜົນງານ (ເຊັ່ນ: ວິຊາການ, ກິລາ, ສິລະປະ, ການແຂ່ງຂັນ)',
  `title` varchar(255) NOT NULL COMMENT 'ຊື່ຜົນງານ ຫຼື ລາງວັນທີ່ໄດ້ຮັບ',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດກ່ຽວກັບຜົນງານ ຫຼື ລາງວັນ',
  `award_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ໄດ້ຮັບລາງວັນ/ຜົນງານ',
  `issuer` varchar(255) DEFAULT NULL COMMENT 'ຜູ້ມອບລາງວັນ ຫຼື ໜ່ວຍງານທີ່ຈັດການແຂ່ງຂັນ/ກິດຈະກຳ',
  `certificate_path` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ໃບຢັ້ງຢືນ/ຮູບພາບ (ຖ້າມີ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`achievement_id`),
  KEY `IDX_StudAchieve_student` (`student_id`),
  KEY `IDX_StudAchieve_type` (`achievement_type`),
  KEY `IDX_StudAchieve_date` (`award_date`),
  KEY `IDX_StudAchieve_issuer` (`issuer`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D22: ຕາຕະລາງເກັບຂໍ້ມູນຜົນງານ ແລະ ລາງວັນຕ່າງໆຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_activities`
--

DROP TABLE IF EXISTS `student_activities`;
CREATE TABLE IF NOT EXISTS `student_activities` (
  `student_activity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການເຂົ້າຮ່ວມກິດຈະກຳ (PK)',
  `activity_id` int(11) NOT NULL COMMENT 'ລະຫັດກິດຈະກຳ (FK ຈາກ Extracurricular_Activities)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `join_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ນັກຮຽນເລີ່ມເຂົ້າຮ່ວມ ຫຼື ລົງທະບຽນ',
  `status` enum('active','completed','dropped') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະການເຂົ້າຮ່ວມ: active, completed, dropped',
  `performance` varchar(100) DEFAULT NULL COMMENT 'ບັນທຶກຜົນງານ ຫຼື ລະດັບການເຂົ້າຮ່ວມ (ຕົວຢ່າງ: ດີເດັ່ນ, ຜ່ານ)',
  `notes` text DEFAULT NULL COMMENT 'ໝາຍເຫດເພີ່ມເຕີມ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`student_activity_id`),
  UNIQUE KEY `UQ_StudAct_student_activity` (`student_id`,`activity_id`) COMMENT 'ປ້ອງກັນນັກຮຽນຄົນດຽວກັນລົງທະບຽນເຂົ້າກິດຈະກຳດຽວກັນຊ້ຳ',
  KEY `IDX_StudAct_activity` (`activity_id`),
  KEY `IDX_StudAct_student` (`student_id`),
  KEY `IDX_StudAct_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D57: ຕາຕະລາງເຊື່ອມໂຍງນັກຮຽນ ແລະ ກິດຈະກຳນອກຫຼັກສູດ';

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance_summary`
--

DROP TABLE IF EXISTS `student_attendance_summary`;
CREATE TABLE IF NOT EXISTS `student_attendance_summary` (
  `summary_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຂໍ້ມູນສະຫຼຸບ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `class_id` int(11) NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK ຈາກ Classes)',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK ຈາກ Academic_Years)',
  `month` int(11) NOT NULL COMMENT 'ເດືອນທີ່ສະຫຼຸບຂໍ້ມູນ (1-12)',
  `year` int(11) NOT NULL COMMENT 'ປີ ຄ.ສ. ທີ່ສະຫຼຸບຂໍ້ມູນ',
  `total_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນຮຽນທັງໝົດໃນໄລຍະເວລາສະຫຼຸບ',
  `present_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ມາຮຽນ (Present)',
  `absent_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ຂາດຮຽນ (Absent)',
  `late_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ມາຊ້າ (Late)',
  `excused_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ລາພັກ (Excused)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`summary_id`),
  UNIQUE KEY `UQ_StudAttSumm_student_period` (`student_id`,`academic_year_id`,`year`,`month`) COMMENT 'ປ້ອງກັນການມີຂໍ້ມູນສະຫຼຸບຊ້ຳຊ້ອນສຳລັບ ນັກຮຽນ/ສົກຮຽນ/ປີ/ເດືອນ ດຽວກັນ',
  KEY `IDX_StudAttSumm_student` (`student_id`),
  KEY `IDX_StudAttSumm_class` (`class_id`),
  KEY `IDX_StudAttSumm_acad_year` (`academic_year_id`),
  KEY `IDX_StudAttSumm_period` (`academic_year_id`,`year`,`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D38: ຕາຕະລາງເກັບຂໍ້ມູນສະຫຼຸບການຂາດ-ມາ ປະຈຳເດືອນຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_behavior_records`
--

DROP TABLE IF EXISTS `student_behavior_records`;
CREATE TABLE IF NOT EXISTS `student_behavior_records` (
  `behavior_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດບັນທຶກພຶດຕິກຳ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `record_type` enum('positive','negative','neutral') NOT NULL COMMENT 'ປະເພດພຶດຕິກຳ: positive, negative, neutral',
  `description` text NOT NULL COMMENT 'ລາຍລະອຽດພຶດຕິກຳທີ່ສັງເກດເຫັນ ຫຼື ເຫດການ',
  `teacher_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຄູສອນ ຫຼື ຜູ້ບັນທຶກ (FK ຈາກ Teachers). ອາດຈະ NULL ຖ້າບັນທຶກໂດຍພາກສ່ວນອື່ນ.',
  `record_date` date NOT NULL COMMENT 'ວັນທີທີ່ສັງເກດເຫັນພຶດຕິກຳ ຫຼື ບັນທຶກ',
  `action_taken` text DEFAULT NULL COMMENT 'ການດຳເນີນການທີ່ໄດ້ເຮັດໄປແລ້ວ (ເຊັ່ນ: ຕັກເຕືອນ, ລາຍງານຜູ້ປົກຄອງ)',
  `follow_up` text DEFAULT NULL COMMENT 'ການຕິດຕາມຜົນ ຫຼື ຂໍ້ສັງເກດເພີ່ມເຕີມ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`behavior_id`),
  KEY `IDX_StudBehavior_student` (`student_id`),
  KEY `IDX_StudBehavior_teacher` (`teacher_id`),
  KEY `IDX_StudBehavior_type` (`record_type`),
  KEY `IDX_StudBehavior_date` (`record_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D23: ຕາຕະລາງບັນທຶກພຶດຕິກຳທີ່ສັງເກດເຫັນຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_discounts`
--

DROP TABLE IF EXISTS `student_discounts`;
CREATE TABLE IF NOT EXISTS `student_discounts` (
  `student_discount_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການມອບໝາຍສ່ວນຫຼຸດ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `discount_id` int(11) NOT NULL COMMENT 'ລະຫັດປະເພດສ່ວນຫຼຸດ (FK ຈາກ Discounts)',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK ຈາກ Academic_Years)',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ສ່ວນຫຼຸດເລີ່ມມີຜົນ',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ສ່ວນຫຼຸດໝົດຜົນ (NULL ໝາຍເຖິງບໍ່ມີກຳນົດ)',
  `reason` text DEFAULT NULL COMMENT 'ເຫດຜົນ ຫຼື ທີ່ມາຂອງການໄດ້ຮັບສ່ວນຫຼຸດ',
  `approved_by` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ອະນຸມັດສ່ວນຫຼຸດ (FK ຈາກ Users). ອາດຈະ NULL.',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະການນຳໃຊ້ສ່ວນຫຼຸດນີ້: active, inactive',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`student_discount_id`),
  UNIQUE KEY `UQ_StudDisc_student_discount_year` (`student_id`,`discount_id`,`academic_year_id`) COMMENT 'ສົມມຸດວ່ານັກຮຽນໄດ້ຮັບສ່ວນຫຼຸດປະເພດດຽວພຽງຄັ້ງດຽວຕໍ່ປີ (ອາດຈະຕ້ອງປັບປຸງຕາມນະໂຍບາຍ)',
  KEY `IDX_StudDisc_student` (`student_id`),
  KEY `IDX_StudDisc_discount` (`discount_id`),
  KEY `IDX_StudDisc_acad_year` (`academic_year_id`),
  KEY `IDX_StudDisc_approver` (`approved_by`),
  KEY `IDX_StudDisc_status` (`status`),
  KEY `IDX_StudDisc_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D43: ຕາຕະລາງເກັບຂໍ້ມູນການມອບໝາຍສ່ວນຫຼຸດໃຫ້ນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

DROP TABLE IF EXISTS `student_documents`;
CREATE TABLE IF NOT EXISTS `student_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດເອກະສານ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `document_type` varchar(100) NOT NULL COMMENT 'ປະເພດເອກະສານ (ເຊັ່ນ: ໃບແຈ້ງໂທດ, ໃບຄະແນນ, ສຳມະໂນຄົວ, ບັດປະຈຳຕົວ)',
  `document_name` varchar(255) NOT NULL COMMENT 'ຊື່ເອກະສານ ຫຼື ຊື່ໄຟລ໌',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ເກັບໄຟລ໌ໃນລະບົບ',
  `file_size` int(11) DEFAULT NULL COMMENT 'ຂະໜາດໄຟລ໌ (ເປັນ bytes)',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ຊະນິດຂອງໄຟລ໌ (MIME Type ຫຼື ນາມສະກຸນ, ເຊັ່ນ: application/pdf, image/jpeg)',
  `upload_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາອັບໂຫຼດ',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບເອກະສານ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`document_id`),
  KEY `IDX_StudDocs_student` (`student_id`),
  KEY `IDX_StudDocs_type` (`document_type`),
  KEY `IDX_StudDocs_upload_date` (`upload_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D18: ຕາຕະລາງເກັບຂໍ້ມູນເອກະສານທີ່ກ່ຽວຂ້ອງກັບນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_emergency_contacts`
--

DROP TABLE IF EXISTS `student_emergency_contacts`;
CREATE TABLE IF NOT EXISTS `student_emergency_contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຜູ້ຕິດຕໍ່ສຸກເສີນ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `contact_name` varchar(255) NOT NULL COMMENT 'ຊື່ ແລະ ນາມສະກຸນຂອງຜູ້ຕິດຕໍ່',
  `relationship` varchar(100) DEFAULT NULL COMMENT 'ຄວາມສຳພັນກັບນັກຮຽນ (ເຊັ່ນ: ພໍ່, ແມ່, ປ້າ, ລຸງ)',
  `phone` varchar(20) NOT NULL COMMENT 'ເບີໂທລະສັບຫຼັກທີ່ຕິດຕໍ່ໄດ້',
  `alternative_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບສຳຮອງ (ຖ້າມີ)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງຜູ້ຕິດຕໍ່ (ຖ້າມີ)',
  `priority` int(11) DEFAULT 1 COMMENT 'ລຳດັບຄວາມສຳຄັນໃນການຕິດຕໍ່ (ຕົວຢ່າງ: 1 = ຕິດຕໍ່ກ່ອນ, 2 = ລຳດັບຖັດໄປ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`contact_id`),
  KEY `IDX_StudEmergContacts_student` (`student_id`),
  KEY `IDX_StudEmergContacts_priority` (`student_id`,`priority`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D20: ຕາຕະລາງເກັບຂໍ້ມູນຜູ້ຕິດຕໍ່ກໍລະນີສຸກເສີນຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

DROP TABLE IF EXISTS `student_enrollments`;
CREATE TABLE IF NOT EXISTS `student_enrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການລົງທະບຽນ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `class_id` int(11) NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນທີ່ລົງທະບຽນ (FK ຈາກ Classes)',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນທີ່ລົງທະບຽນ (FK ຈາກ Academic_Years)',
  `enrollment_date` date NOT NULL COMMENT 'ວັນທີລົງທະບຽນເຂົ້າຫ້ອງນີ້',
  `enrollment_status` enum('enrolled','transferred','dropped') NOT NULL DEFAULT 'enrolled' COMMENT 'ສະຖານະການລົງທະບຽນ: enrolled, transferred, dropped',
  `previous_class_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນກ່ອນໜ້າ (FK ຈາກ Classes). ອາດຈະ NULL.',
  `is_new_student` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ເປັນນັກຮຽນໃໝ່ໃນສົກຮຽນນີ້ ຫຼື ບໍ່',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`enrollment_id`),
  UNIQUE KEY `UQ_StudEnroll_student_year` (`student_id`,`academic_year_id`) COMMENT 'ສົມມຸດວ່ານັກຮຽນໜຶ່ງຄົນມີ record ລົງທະບຽນຫຼັກອັນດຽວຕໍ່ສົກຮຽນ',
  KEY `IDX_StudEnroll_student` (`student_id`),
  KEY `IDX_StudEnroll_class` (`class_id`),
  KEY `IDX_StudEnroll_acad_year` (`academic_year_id`),
  KEY `IDX_StudEnroll_prev_class` (`previous_class_id`),
  KEY `IDX_StudEnroll_status` (`enrollment_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D33: ຕາຕະລາງເກັບຂໍ້ມູນການລົງທະບຽນເຂົ້າຫ້ອງຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_fees`
--

DROP TABLE IF EXISTS `student_fees`;
CREATE TABLE IF NOT EXISTS `student_fees` (
  `student_fee_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດລາຍການຄ່າທຳນຽມນັກຮຽນ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `fee_type_id` int(11) NOT NULL COMMENT 'ລະຫັດປະເພດຄ່າທຳນຽມ (FK ຈາກ Fee_Types)',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK ຈາກ Academic_Years)',
  `amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນເບື້ອງຕົ້ນ (ກ່ອນສ່ວນຫຼຸດ)',
  `due_date` date DEFAULT NULL COMMENT 'ວັນທີຄົບກຳນົດຊຳລະ',
  `status` enum('pending','partial','paid','waived') NOT NULL DEFAULT 'pending' COMMENT 'ສະຖານະການຊຳລະ: pending, partial, paid, waived',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ຈຳນວນເງິນສ່ວນຫຼຸດທີ່ນຳໃຊ້',
  `final_amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນສຸດທິທີ່ຕ້ອງຈ່າຍ (amount - discount_amount)',
  `description` text DEFAULT NULL COMMENT 'ໝາຍເຫດ ຫຼື ລາຍລະອຽດເພີ່ມເຕີມສຳລັບລາຍການນີ້',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`student_fee_id`),
  UNIQUE KEY `UQ_StudFees_student_type_year` (`student_id`,`fee_type_id`,`academic_year_id`) COMMENT 'ປ້ອງກັນການສ້າງລາຍການຄ່າທຳນຽມປະເພດດຽວກັນຊ້ຳໃຫ້ນັກຮຽນຄົນດຽວໃນປີດຽວ (ອາດຈະຕ້ອງປັບສຳລັບ recurring fees)',
  KEY `IDX_StudFees_student` (`student_id`),
  KEY `IDX_StudFees_fee_type` (`fee_type_id`),
  KEY `IDX_StudFees_acad_year` (`academic_year_id`),
  KEY `IDX_StudFees_status` (`status`),
  KEY `IDX_StudFees_due_date` (`due_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D40: ຕາຕະລາງເກັບລາຍການຄ່າທຳນຽມທີ່ນັກຮຽນຕ້ອງຈ່າຍ';

-- --------------------------------------------------------

--
-- Table structure for table `student_health_records`
--

DROP TABLE IF EXISTS `student_health_records`;
CREATE TABLE IF NOT EXISTS `student_health_records` (
  `health_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຂໍ້ມູນສຸຂະພາບ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `health_condition` varchar(255) DEFAULT NULL COMMENT 'ສະພາບສຸຂະພາບທົ່ວໄປ ຫຼື ພະຍາດປະຈຳຕົວ',
  `medications` text DEFAULT NULL COMMENT 'ລາຍການຢາທີ່ນັກຮຽນໃຊ້ ຫຼື ແພ້',
  `allergies` text DEFAULT NULL COMMENT 'ປະຫວັດການແພ້ຕ່າງໆ (ອາຫານ, ອາກາດ, ...)',
  `special_needs` text DEFAULT NULL COMMENT 'ຄວາມຕ້ອງການພິເສດດ້ານສຸຂະພາບ ຫຼື ການດູແລ',
  `doctor_name` varchar(100) DEFAULT NULL COMMENT 'ຊື່ແພດປະຈຳຕົວ (ຖ້າມີ)',
  `doctor_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທຕິດຕໍ່ແພດປະຈຳຕົວ (ຖ້າມີ)',
  `record_date` date NOT NULL COMMENT 'ວັນທີບັນທຶກ ຫຼື ອັບເດດຂໍ້ມູນສຸຂະພາບນີ້',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`health_id`),
  KEY `IDX_StudHealth_student` (`student_id`),
  KEY `IDX_StudHealth_record_date` (`record_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D19: ຕາຕະລາງເກັບຂໍ້ມູນດ້ານສຸຂະພາບຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_interests`
--

DROP TABLE IF EXISTS `student_interests`;
CREATE TABLE IF NOT EXISTS `student_interests` (
  `interest_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຄວາມສົນໃຈ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `interest_category` varchar(100) DEFAULT NULL COMMENT 'ໝວດໝູ່ຄວາມສົນໃຈ (ເຊັ່ນ: ກິລາ, ດົນຕີ, ສິລະປະ, ວິຊາການ)',
  `interest_name` varchar(255) NOT NULL COMMENT 'ຊື່ຄວາມສົນໃຈສະເພາະ (ເຊັ່ນ: ບານເຕະ, ເປຍໂນ, ແຕ້ມຮູບ, Math Club)',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດເພີ່ມເຕີມກ່ຽວກັບຄວາມສົນໃຈນີ້',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`interest_id`),
  UNIQUE KEY `UQ_StudInterests_student_interest` (`student_id`,`interest_name`) USING HASH COMMENT 'ນັກຮຽນໜຶ່ງຄົນບໍ່ຄວນມີຄວາມສົນໃຈອັນດຽວກັນຊ້ຳຊ້ອນ',
  KEY `IDX_StudInterests_student` (`student_id`),
  KEY `IDX_StudInterests_category` (`interest_category`),
  KEY `IDX_StudInterests_name` (`interest_name`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D16: ຕາຕະລາງເກັບຂໍ້ມູນຄວາມສົນໃຈ ຫຼື ຄວາມສາມາດພິເສດຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_parent`
--

DROP TABLE IF EXISTS `student_parent`;
CREATE TABLE IF NOT EXISTS `student_parent` (
  `student_parent_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການເຊື່ອມໂຍງ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `parent_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ປົກຄອງ (FK ຈາກ Parents)',
  `relationship` enum('father','mother','guardian','other') NOT NULL COMMENT 'ຄວາມສຳພັນກັບນັກຮຽນ: father, mother, guardian, other',
  `is_primary_contact` tinyint(1) DEFAULT 0 COMMENT 'ເປັນຜູ້ຕິດຕໍ່ຫຼັກສຳລັບນັກຮຽນຄົນນີ້ ຫຼື ບໍ່',
  `has_custody` tinyint(1) DEFAULT 1 COMMENT 'ມີສິດໃນການດູແລນັກຮຽນຄົນນີ້ ຫຼື ບໍ່',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`student_parent_id`),
  UNIQUE KEY `UQ_StudentParent_pair` (`student_id`,`parent_id`) COMMENT 'ການຈັບຄູ່ລະຫວ່າງນັກຮຽນ ແລະ ຜູ້ປົກຄອງຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_StudentParent_student` (`student_id`),
  KEY `IDX_StudentParent_parent` (`parent_id`),
  KEY `IDX_StudentParent_primary` (`is_primary_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D26: ຕາຕະລາງເຊື່ອມໂຍງລະຫວ່າງນັກຮຽນ ແລະ ຜູ້ປົກຄອງ';

-- --------------------------------------------------------

--
-- Table structure for table `student_previous_education`
--

DROP TABLE IF EXISTS `student_previous_education`;
CREATE TABLE IF NOT EXISTS `student_previous_education` (
  `education_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດປະຫວັດການສຶກສາ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `school_name` varchar(255) NOT NULL COMMENT 'ຊື່ໂຮງຮຽນເກົ່າ',
  `education_level` varchar(100) DEFAULT NULL COMMENT 'ລະດັບການສຶກສາທີ່ຈົບຈາກໂຮງຮຽນນັ້ນ (ເຊັ່ນ: ປະຖົມ, ມັດທະຍົມຕົ້ນ)',
  `from_year` int(11) DEFAULT NULL COMMENT 'ປີ ຄ.ສ. ທີ່ເລີ່ມຮຽນ',
  `to_year` int(11) DEFAULT NULL COMMENT 'ປີ ຄ.ສ. ທີ່ຈົບ',
  `certificate` varchar(255) DEFAULT NULL COMMENT 'ຊື່ປະກາດນີຍະບັດ ຫຼື ທີ່ຢູ່ໄຟລ໌',
  `gpa` decimal(3,2) DEFAULT NULL COMMENT 'ຄະແນນສະເລ່ຍສະສົມ (GPA) ຖ້າມີ',
  `description` text DEFAULT NULL COMMENT 'ໝາຍເຫດ ຫຼື ລາຍລະອຽດເພີ່ມເຕີມ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`education_id`),
  KEY `IDX_StudPrevEdu_student` (`student_id`),
  KEY `IDX_StudPrevEdu_school` (`school_name`(250)),
  KEY `IDX_StudPrevEdu_level` (`education_level`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `student_previous_locations`
--

DROP TABLE IF EXISTS `student_previous_locations`;
CREATE TABLE IF NOT EXISTS `student_previous_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດທີ່ຢູ່ເກົ່າ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ລະອຽດ (ເລກເຮືອນ, ຮ່ອມ...)',
  `village_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK ຈາກ Villages). ອາດຈະ NULL ຖ້າຢູ່ນອກ ຫຼື ບໍ່ຮູ້.',
  `district_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK ຈາກ Districts). ອາດຈະ NULL.',
  `province_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK ຈາກ Provinces). ອາດຈະ NULL.',
  `country` varchar(100) DEFAULT 'Laos' COMMENT 'ປະເທດ',
  `from_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ເລີ່ມອາໄສຢູ່ທີ່ຢູ່ນີ້',
  `to_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ຍ້າຍອອກຈາກທີ່ຢູ່ນີ້',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`location_id`),
  KEY `IDX_StudPrevLoc_student` (`student_id`),
  KEY `IDX_StudPrevLoc_village` (`village_id`),
  KEY `IDX_StudPrevLoc_district` (`district_id`),
  KEY `IDX_StudPrevLoc_province` (`province_id`),
  KEY `IDX_StudPrevLoc_country` (`country`),
  KEY `IDX_StudPrevLoc_dates` (`from_date`,`to_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D15: ຕາຕະລາງເກັບຂໍ້ມູນທີ່ຢູ່ອາໄສເກົ່າຂອງນັກຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `student_siblings`
--

DROP TABLE IF EXISTS `student_siblings`;
CREATE TABLE IF NOT EXISTS `student_siblings` (
  `sibling_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການເຊື່ອມໂຍງພີ່ນ້ອງ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນຄົນທີໜຶ່ງ (FK ຈາກ Students)',
  `sibling_student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນຄົນທີສອງ (ພີ່ນ້ອງ) (FK ຈາກ Students)',
  `relationship` enum('brother','sister','step_brother','step_sister') NOT NULL COMMENT 'ຄວາມສຳພັນ: brother, sister, step_brother, step_sister',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`sibling_id`),
  UNIQUE KEY `UQ_StudSiblings_pair` (`student_id`,`sibling_student_id`) COMMENT 'ຄູ່ພີ່ນ້ອງ (student_id, sibling_student_id) ຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_StudSiblings_student` (`student_id`),
  KEY `IDX_StudSiblings_sibling` (`sibling_student_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `student_special_needs`
--

DROP TABLE IF EXISTS `student_special_needs`;
CREATE TABLE IF NOT EXISTS `student_special_needs` (
  `special_need_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຄວາມຕ້ອງການພິເສດ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `need_type` varchar(100) NOT NULL COMMENT 'ປະເພດຄວາມຕ້ອງການພິເສດ (ເຊັ່ນ: Learning Difficulty, Physical Disability, Gifted/Talented)',
  `description` text NOT NULL COMMENT 'ລາຍລະອຽດຂອງຄວາມຕ້ອງການພິເສດ',
  `recommendations` text DEFAULT NULL COMMENT 'ຂໍ້ສະເໜີແນະໃນການຊ່ວຍເຫຼືອ ຫຼື ການຈັດການຮຽນການສອນ',
  `support_required` text DEFAULT NULL COMMENT 'ການສະໜັບສະໜູນສະເພາະທີ່ຕ້ອງການຈາກທາງໂຮງຮຽນ',
  `external_support` varchar(255) DEFAULT NULL COMMENT 'ຂໍ້ມູນການສະໜັບສະໜູນຈາກພາຍນອກ (ຖ້າມີ, ເຊັ່ນ: ຊື່ຜູ້ຊ່ຽວຊານ, ໜ່ວຍງານ)',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມຕົ້ນ (ວັນທີກວດພົບ ຫຼື ວັນທີເລີ່ມໃຫ້ການຊ່ວຍເຫຼືອ)',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດ (ຖ້າຄວາມຕ້ອງການ ຫຼື ການຊ່ວຍເຫຼືອສິ້ນສຸດລົງ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`special_need_id`),
  UNIQUE KEY `UQ_StudSpecialNeeds_student_type` (`student_id`,`need_type`) COMMENT 'ສົມມຸດວ່ານັກຮຽນໜຶ່ງຄົນມີບັນທຶກຄວາມຕ້ອງການພິເສດແຕ່ລະປະເພດໄດ້ພຽງອັນດຽວ',
  KEY `IDX_StudSpecialNeeds_student` (`student_id`),
  KEY `IDX_StudSpecialNeeds_type` (`need_type`),
  KEY `IDX_StudSpecialNeeds_dates` (`start_date`,`end_date`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດວິຊາຮຽນ (PK)',
  `subject_code` varchar(20) NOT NULL COMMENT 'ລະຫັດວິຊາ (ຕົວຢ່າງ: MTH101)',
  `subject_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ວິຊາ (ພາສາລາວ)',
  `subject_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ວິຊາ (ພາສາອັງກິດ)',
  `credit_hours` int(11) DEFAULT NULL COMMENT 'ຈຳນວນໜ່ວຍກິດ (ຖ້າມີ)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍກ່ຽວກັບວິຊາ',
  `category` varchar(50) DEFAULT NULL COMMENT 'ໝວດໝູ່ຂອງວິຊາ (ຕົວຢ່າງ: ວິທະຍາສາດ, ຄະນິດສາດ, ພາສາ)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ຍັງເປີດສອນ, FALSE = ບໍ່ເປີດສອນແລ້ວ)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `UQ_Subjects_code` (`subject_code`) COMMENT 'ລະຫັດວິຊາຕ້ອງບໍ່ຊ້ຳ',
  UNIQUE KEY `UQ_Subjects_name_lao` (`subject_name_lao`) COMMENT 'ຊື່ວິຊາ (ລາວ) ຕ້ອງບໍ່ຊ້ຳ',
  UNIQUE KEY `UQ_Subjects_name_en` (`subject_name_en`) COMMENT 'ຊື່ວິຊາ (ອັງກິດ) ຖ້າມີ, ຕ້ອງບໍ່ຊ້ຳ',
  KEY `IDX_Subjects_category` (`category`),
  KEY `IDX_Subjects_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D31: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຊື່ວິຊາຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
CREATE TABLE IF NOT EXISTS `system_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດ Log ລະບົບ (PK)',
  `log_level` enum('info','warning','error','critical') NOT NULL COMMENT 'ລະດັບຄວາມສຳຄັນຂອງ Log: info, warning, error, critical',
  `log_source` varchar(100) DEFAULT NULL COMMENT 'ແຫຼ່ງທີ່ມາຂອງ Log (ເຊັ່ນ: ຊື່ Module, Function, Class)',
  `message` text NOT NULL COMMENT 'ຂໍ້ຄວາມ ຫຼື ລາຍລະອຽດຂອງ Log',
  `context` text DEFAULT NULL COMMENT 'ຂໍ້ມູນເພີ່ມເຕີມ (Context) ເຊັ່ນ: Stack trace, Request data (JSON/XML)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ທີ່ຢູ່ IP Address ທີ່ກ່ຽວຂ້ອງ (ຖ້າມີ)',
  `user_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ກ່ຽວຂ້ອງກັບເຫດການນີ້ (FK ຈາກ Users). ອາດຈະ NULL.',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາທີ່ສ້າງ Log',
  PRIMARY KEY (`log_id`),
  KEY `IDX_SystemLogs_level` (`log_level`),
  KEY `IDX_SystemLogs_source` (`log_source`),
  KEY `IDX_SystemLogs_user` (`user_id`),
  KEY `IDX_SystemLogs_ip` (`ip_address`),
  KEY `IDX_SystemLogs_created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D60: ຕາຕະລາງເກັບ Log ການເຮັດວຽກ ແລະ ຂໍ້ຜິດພາດຂອງລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE IF NOT EXISTS `teachers` (
  `teacher_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຄູສອນ (PK)',
  `teacher_code` varchar(20) NOT NULL COMMENT 'ລະຫັດປະຈຳຕົວຄູສອນ',
  `first_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ຄູສອນ (ພາສາລາວ)',
  `last_name_lao` varchar(100) NOT NULL COMMENT 'ນາມສະກຸນຄູສອນ (ພາສາລາວ)',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຄູສອນ (ພາສາອັງກິດ)',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'ນາມສະກຸນຄູສອນ (ພາສາອັງກິດ)',
  `gender` enum('male','female','other') NOT NULL COMMENT 'ເພດ',
  `date_of_birth` date NOT NULL COMMENT 'ວັນເດືອນປີເກີດ',
  `national_id` varchar(50) DEFAULT NULL COMMENT 'ເລກບັດປະຈຳຕົວປະຊາຊົນ',
  `phone` varchar(20) NOT NULL COMMENT 'ເບີໂທລະສັບຫຼັກ',
  `alternative_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບສຳຮອງ',
  `email` varchar(100) NOT NULL COMMENT 'ອີເມວ (ໃຊ້ສຳລັບເຂົ້າລະບົບ)',
  `village_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK ຈາກ Villages)',
  `district_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK ຈາກ Districts)',
  `province_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK ຈາກ Provinces)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ)',
  `highest_education` varchar(100) DEFAULT NULL COMMENT 'ລະດັບການສຶກສາສູງສຸດ',
  `specialization` varchar(255) DEFAULT NULL COMMENT 'ຄວາມຊຳນານ/ວິຊາເອກ',
  `employment_date` date NOT NULL COMMENT 'ວັນທີເລີ່ມຈ້າງງານ/ເຮັດວຽກ',
  `contract_type` enum('full_time','part_time','contract') DEFAULT NULL COMMENT 'ປະເພດສັນຍາຈ້າງ: full_time, part_time, contract',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະການເຮັດວຽກ: active, inactive',
  `user_id` int(11) NOT NULL COMMENT 'ລະຫັດບັນຊີຜູ້ໃຊ້ຂອງຄູສອນ (FK ຈາກ Users)',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`teacher_id`),
  UNIQUE KEY `UQ_Teachers_code` (`teacher_code`),
  UNIQUE KEY `UQ_Teachers_email` (`email`),
  UNIQUE KEY `UQ_Teachers_user` (`user_id`),
  UNIQUE KEY `UQ_Teachers_national_id` (`national_id`),
  KEY `IDX_Teachers_name_lao` (`last_name_lao`,`first_name_lao`),
  KEY `IDX_Teachers_name_en` (`last_name_en`,`first_name_en`),
  KEY `IDX_Teachers_village` (`village_id`),
  KEY `IDX_Teachers_district` (`district_id`),
  KEY `IDX_Teachers_province` (`province_id`),
  KEY `IDX_Teachers_user` (`user_id`),
  KEY `IDX_Teachers_status` (`status`),
  KEY `IDX_Teachers_specialization` (`specialization`(250)),
  KEY `IDX_Teachers_employment_date` (`employment_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D27: ຕາຕະລາງເກັບຂໍ້ມູນຄູສອນ';

-- --------------------------------------------------------

--
-- Table structure for table `teacher_documents`
--

DROP TABLE IF EXISTS `teacher_documents`;
CREATE TABLE IF NOT EXISTS `teacher_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດເອກະສານ (PK)',
  `teacher_id` int(11) NOT NULL COMMENT 'ລະຫັດຄູສອນ (FK ຈາກ Teachers)',
  `document_type` varchar(100) NOT NULL COMMENT 'ປະເພດເອກະສານ (ເຊັ່ນ: ໃບປະກາດ, ສັນຍາຈ້າງ, ບັດປະຈຳຕົວ)',
  `document_name` varchar(255) NOT NULL COMMENT 'ຊື່ເອກະສານ ຫຼື ຊື່ໄຟລ໌',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ເກັບໄຟລ໌ໃນລະບົບ',
  `file_size` int(11) DEFAULT NULL COMMENT 'ຂະໜາດໄຟລ໌ (ເປັນ bytes)',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ຊະນິດຂອງໄຟລ໌ (MIME Type ຫຼື ນາມສະກຸນ)',
  `upload_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາອັບໂຫຼດ',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບເອກະສານ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record ຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`document_id`),
  KEY `IDX_TeacherDocs_teacher` (`teacher_id`),
  KEY `IDX_TeacherDocs_type` (`document_type`),
  KEY `IDX_TeacherDocs_upload_date` (`upload_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D28: ຕາຕະລາງເກັບຂໍ້ມູນເອກະສານທີ່ກ່ຽວຂ້ອງກັບຄູສອນ';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຜູ້ໃຊ້ (PK)',
  `username` varchar(50) NOT NULL COMMENT 'ຊື່ຜູ້ໃຊ້ສຳລັບເຂົ້າລະບົບ',
  `password` varchar(255) NOT NULL COMMENT 'ລະຫັດຜ່ານ (ເກັບແບບເຂົ້າລະຫັດ)',
  `email` varchar(100) NOT NULL COMMENT 'ອີເມວ',
  `phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບ',
  `role_id` int(11) NOT NULL COMMENT 'ລະຫັດບົດບາດ (FK)',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະຜູ້ໃຊ້: active, inactive, suspended',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'ເວລາເຂົ້າລະບົບຄັ້ງສຸດທ້າຍ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UQ_Users_username` (`username`),
  UNIQUE KEY `UQ_Users_email` (`email`),
  UNIQUE KEY `UQ_Users_phone` (`phone`),
  KEY `IDX_Users_role_id` (`role_id`),
  KEY `IDX_Users_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D1: ຕາຕະລາງເກັບຂໍ້ມູນຜູ້ໃຊ້ລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

DROP TABLE IF EXISTS `user_activities`;
CREATE TABLE IF NOT EXISTS `user_activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດກິດຈະກຳຜູ້ໃຊ້ (PK)',
  `user_id` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ກະທຳ (FK ຈາກ Users)',
  `activity_type` varchar(50) NOT NULL COMMENT 'ປະເພດກິດຈະກຳ (ເຊັ່ນ: login, logout, update_profile, create_student)',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດເພີ່ມເຕີມກ່ຽວກັບກິດຈະກຳ',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ທີ່ຢູ່ IP ຂອງຜູ້ໃຊ້ທີ່ເຮັດກິດຈະກຳ',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'ຂໍ້ມູນ Browser ຫຼື Client ທີ່ຜູ້ໃຊ້ງານ',
  `activity_time` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາທີ່ເກີດກິດຈະກຳ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາທີ່ບັນທຶກ Log ນີ້',
  PRIMARY KEY (`activity_id`),
  KEY `IDX_UserActivities_user` (`user_id`),
  KEY `IDX_UserActivities_type` (`activity_type`),
  KEY `IDX_UserActivities_time` (`activity_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D5: ຕາຕະລາງບັນທຶກກິດຈະກຳຕ່າງໆຂອງຜູ້ໃຊ້ໃນລະບົບ';

-- --------------------------------------------------------

--
-- Table structure for table `villages`
--

DROP TABLE IF EXISTS `villages`;
CREATE TABLE IF NOT EXISTS `villages` (
  `village_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດບ້ານ (PK)',
  `village_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ບ້ານ (ພາສາລາວ)',
  `village_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ບ້ານ (ພາສາອັງກິດ)',
  `district_id` int(11) NOT NULL COMMENT 'ລະຫັດເມືອງທີ່ບ້ານນີ້ສັງກັດ (FK ຈາກ Districts)',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນຄັ້ງສຸດທ້າຍ',
  PRIMARY KEY (`village_id`),
  UNIQUE KEY `UQ_Villages_district_name_lao` (`district_id`,`village_name_lao`) COMMENT 'ຊື່ບ້ານ (ລາວ) ຕ້ອງບໍ່ຊ້ຳກັນພາຍໃນເມືອງດຽວກັນ',
  UNIQUE KEY `UQ_Villages_district_name_en` (`district_id`,`village_name_en`) COMMENT 'ຊື່ບ້ານ (ອັງກິດ) ຖ້າມີ, ຕ້ອງບໍ່ຊ້ຳກັນພາຍໃນເມືອງດຽວກັນ',
  KEY `IDX_Villages_district` (`district_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='D10: ຕາຕະລາງເກັບຂໍ້ມູນລາຍຊື່ບ້ານ';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;