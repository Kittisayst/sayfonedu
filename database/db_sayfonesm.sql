-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 17, 2025 at 03:20 AM
-- Server version: 10.10.2-MariaDB
-- PHP Version: 8.2.0

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
  `academic_year_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `year_name` varchar(50) NOT NULL COMMENT 'ຊື່/ປີຂອງສົກຮຽນ (ຕົວຢ່າງ: 2024-2025)',
  `start_date` date NOT NULL COMMENT 'ວັນທີເລີ່ມຕົ້ນສົກຮຽນ',
  `end_date` date NOT NULL COMMENT 'ວັນທີສິ້ນສຸດສົກຮຽນ',
  `is_current` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ກຳນົດວ່າແມ່ນສົກຮຽນປັດຈຸບັນ ຫຼື ບໍ່',
  `status` enum('upcoming','active','completed') NOT NULL DEFAULT 'upcoming' COMMENT 'ສະຖານະຂອງສົກຮຽນ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`academic_year_id`),
  UNIQUE KEY `academic_years_year_name_unique` (`year_name`),
  KEY `IDX_AcademicYears_current` (`is_current`),
  KEY `IDX_AcademicYears_status` (`status`),
  KEY `IDX_AcademicYears_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE IF NOT EXISTS `announcements` (
  `announcement_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'ຫົວຂໍ້ປະກາດ',
  `content` text DEFAULT NULL COMMENT 'ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງປະກາດ',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມສະແດງປະກາດນີ້',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດການສະແດງປະກາດ (NULL = ບໍ່ມີກຳນົດ)',
  `target_group` enum('all','teachers','students','parents') NOT NULL DEFAULT 'all' COMMENT 'ກຸ່ມເປົ້າໝາຍທີ່ເຫັນປະກາດ',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ປັກໝຸດປະກາດນີ້ໄວ້ເທິງສຸດ ຫຼື ບໍ່',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ແນບ (ຖ້າມີ)',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ສ້າງປະກາດ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `IDX_Announcements_creator` (`created_by`),
  KEY `IDX_Announcements_target` (`target_group`),
  KEY `IDX_Announcements_dates` (`start_date`,`end_date`),
  KEY `IDX_Announcements_pinned` (`is_pinned`),
  KEY `IDX_Announcements_created_at` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `class_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK)',
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດວິຊາ (FK)',
  `attendance_date` date NOT NULL COMMENT 'ວັນທີທີ່ບັນທຶກ',
  `status` enum('present','absent','late','excused') NOT NULL COMMENT 'ສະຖານະ: present, absent, late, excused',
  `reason` text DEFAULT NULL COMMENT 'ເຫດຜົນການຂາດ/ລາ/ມາຊ້າ',
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ບັນທຶກ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `UQ_Attendance_stud_date_subj` (`student_id`,`attendance_date`,`subject_id`),
  KEY `IDX_Attendance_student` (`student_id`),
  KEY `IDX_Attendance_class` (`class_id`),
  KEY `IDX_Attendance_subject` (`subject_id`),
  KEY `IDX_Attendance_recorder` (`recorded_by`),
  KEY `IDX_Attendance_date` (`attendance_date`),
  KEY `IDX_Attendance_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
CREATE TABLE IF NOT EXISTS `backups` (
  `backup_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `backup_name` varchar(255) NOT NULL COMMENT 'ຊື່ໄຟລ໌ ຫຼື ຊື່ການສຳຮອງຂໍ້ມູນ',
  `backup_type` enum('full','partial') NOT NULL COMMENT 'ປະເພດການສຳຮອງຂໍ້ມູນ',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ເກັບໄຟລ໌ backup',
  `file_size` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ຂະໜາດໄຟລ໌ (bytes)',
  `backup_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາສຳຮອງຂໍ້ມູນ',
  `status` enum('success','failed','in_progress') NOT NULL DEFAULT 'in_progress' COMMENT 'ສະຖານະ',
  `initiated_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ເລີ່ມດຳເນີນການ (FK)',
  `description` text DEFAULT NULL COMMENT 'ໝາຍເຫດເພີ່ມເຕີມ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`backup_id`),
  UNIQUE KEY `backups_backup_name_unique` (`backup_name`(191)),
  KEY `IDX_Backups_initiator` (`initiated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_data`
--

DROP TABLE IF EXISTS `biometric_data`;
CREATE TABLE IF NOT EXISTS `biometric_data` (
  `biometric_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ເຈົ້າຂອງຂໍ້ມູນ (FK ຈາກ Users)',
  `biometric_type` enum('fingerprint','face') NOT NULL COMMENT 'ປະເພດຂໍ້ມູນຊີວະມິຕິ: fingerprint, face',
  `biometric_data` longtext NOT NULL COMMENT 'ຂໍ້ມູນຊີວະມິຕິຕົວຈິງ (template/binary data)',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະຂໍ້ມູນນີ້: active, inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`biometric_id`),
  KEY `IDX_BiometricData_user` (`user_id`),
  KEY `IDX_BiometricData_type` (`biometric_type`),
  KEY `IDX_BiometricData_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_logs`
--

DROP TABLE IF EXISTS `biometric_logs`;
CREATE TABLE IF NOT EXISTS `biometric_logs` (
  `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ພະຍາຍາມສະແກນ (FK ຈາກ Users)',
  `biometric_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຂໍ້ມູນຊີວະມິຕິທີ່ໃຊ້ (FK ຈາກ Biometric_Data). ອາດຈະ NULL ຖ້າສະແກນບໍ່ຜ່ານ ຫຼື ຫາ User ບໍ່ພົບ.',
  `log_type` enum('check_in','check_out','authentication') NOT NULL COMMENT 'ປະເພດການໃຊ້ງານ: check_in, check_out, authentication',
  `status` enum('success','failed') NOT NULL COMMENT 'ຜົນລັບການສະແກນ: success, failed',
  `device_id` varchar(100) DEFAULT NULL COMMENT 'ລະຫັດເຄື່ອງສະແກນ/ອຸປະກອນທີ່ໃຊ້',
  `location` varchar(100) DEFAULT NULL COMMENT 'ສະຖານທີ່ຕິດຕັ້ງເຄື່ອງສະແກນ (ເຊັ່ນ: ປະຕູໜ້າ, ຫ້ອງການ)',
  `log_time` timestamp NULL DEFAULT '2025-04-11 07:51:58' COMMENT 'ເວລາທີ່ເກີດເຫດການສະແກນ',
  `created_at` timestamp NOT NULL DEFAULT '2025-04-11 07:51:58' COMMENT 'ເວລາທີ່ບັນທຶກ Log ນີ້',
  PRIMARY KEY (`log_id`),
  KEY `IDX_BiometricLogs_user` (`user_id`),
  KEY `IDX_BiometricLogs_bio_data` (`biometric_id`),
  KEY `IDX_BiometricLogs_log_type` (`log_type`),
  KEY `IDX_BiometricLogs_status` (`status`),
  KEY `IDX_BiometricLogs_log_time` (`log_time`),
  KEY `IDX_BiometricLogs_device` (`device_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `class_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL COMMENT 'ຊື່ຫ້ອງຮຽນ (ຕົວຢ່າງ: ມ.1/1)',
  `level_id` int(10) NOT NULL COMMENT 'school_levels ລະດັບຊັ້ນ  (ຕົວຢ່າງ: ມ.1)',
  `academic_year_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK)',
  `homeroom_teacher_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຄູປະຈຳຫ້ອງ (FK)',
  `room_number` varchar(20) DEFAULT NULL COMMENT 'ເລກຫ້ອງ ຫຼື ສະຖານທີ່ຂອງຫ້ອງຮຽນ',
  `capacity` int(11) DEFAULT NULL COMMENT 'ຈຳນວນນັກຮຽນສູງສຸດທີ່ຮອງຮັບໄດ້',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບຫ້ອງຮຽນ',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະຫ້ອງຮຽນ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `UQ_Classes_name_year` (`academic_year_id`,`class_name`),
  KEY `IDX_Classes_academic_year` (`academic_year_id`),
  KEY `IDX_Classes_teacher` (`homeroom_teacher_id`),
  KEY `IDX_Classes_grade_level` (`level_id`),
  KEY `IDX_Classes_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_subjects`
--

DROP TABLE IF EXISTS `class_subjects`;
CREATE TABLE IF NOT EXISTS `class_subjects` (
  `class_subject_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK)',
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດວິຊາຮຽນ (FK)',
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຄູສອນທີ່ຮັບຜິດຊອບ (FK)',
  `hours_per_week` int(11) DEFAULT NULL COMMENT 'ຈຳນວນຊົ່ວໂມງຕໍ່ອາທິດ (ໂດຍປະມານ)',
  `day_of_week` varchar(20) DEFAULT NULL COMMENT 'ມື້ທີ່ສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ)',
  `start_time` time DEFAULT NULL COMMENT 'ເວລາເລີ່ມສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ)',
  `end_time` time DEFAULT NULL COMMENT 'ເວລາເລີກສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ)',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະການມອບໝາຍ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`class_subject_id`),
  UNIQUE KEY `UQ_ClassSubjects_class_subj` (`class_id`,`subject_id`),
  KEY `IDX_ClassSubjects_class` (`class_id`),
  KEY `IDX_ClassSubjects_subject` (`subject_id`),
  KEY `IDX_ClassSubjects_teacher` (`teacher_id`),
  KEY `IDX_ClassSubjects_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `digital_library_resources`
--

DROP TABLE IF EXISTS `digital_library_resources`;
CREATE TABLE IF NOT EXISTS `digital_library_resources` (
  `resource_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'ຊື່ ຫຼື ຫົວຂໍ້ຂອງຊັບພະຍາກອນ',
  `author` varchar(255) DEFAULT NULL COMMENT 'ຊື່ຜູ້ແຕ່ງ ຫຼື ຜູ້ສ້າງ',
  `publisher` varchar(255) DEFAULT NULL COMMENT 'ຊື່ສຳນັກພິມ ຫຼື ຜູ້ເຜີຍແຜ່',
  `publication_year` year(4) DEFAULT NULL COMMENT 'ປີທີ່ພິມ ຫຼື ເຜີຍແຜ່ (ຄ.ສ.)',
  `resource_type` enum('book','document','video','audio','image') NOT NULL COMMENT 'ປະເພດຊັບພະຍາກອນ',
  `category` varchar(100) DEFAULT NULL COMMENT 'ໝວດໝູ່ (ຕົວຢ່າງ: Science, History)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍ ຫຼື ເນື້ອຫຍໍ້',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ຊັບພະຍາກອນຕົວຈິງ',
  `file_size` int(11) DEFAULT NULL COMMENT 'ຂະໜາດຂອງໄຟລ໌ (ເປັນ bytes)',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງໄຟລ໌ຮູບຕົວຢ່າງ/ໜ້າປົກ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ສາມາດເຂົ້າເຖິງໄດ້)',
  `added_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ເພີ່ມຊັບພະຍາກອນນີ້ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`resource_id`),
  KEY `IDX_DigLibRes_adder` (`added_by`),
  KEY `IDX_DigLibRes_title` (`title`(250)),
  KEY `IDX_DigLibRes_author` (`author`(250)),
  KEY `IDX_DigLibRes_type` (`resource_type`),
  KEY `IDX_DigLibRes_category` (`category`),
  KEY `IDX_DigLibRes_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `digital_resource_access`
--

DROP TABLE IF EXISTS `digital_resource_access`;
CREATE TABLE IF NOT EXISTS `digital_resource_access` (
  `access_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `resource_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຊັບພະຍາກອນທີ່ເຂົ້າເຖິງ (FK)',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ເຂົ້າເຖິງ (FK)',
  `access_time` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາທີ່ເຂົ້າເຖິງ',
  `access_type` enum('view','download','print') NOT NULL COMMENT 'ປະເພດການເຂົ້າເຖິງ',
  `device_info` varchar(255) DEFAULT NULL COMMENT 'ຂໍ້ມູນອຸປະກອນ (ເຊັ່ນ: User Agent)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ທີ່ຢູ່ IP ຂອງຜູ້ໃຊ້',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`access_id`),
  KEY `IDX_DigResAccess_resource` (`resource_id`),
  KEY `IDX_DigResAccess_user` (`user_id`),
  KEY `IDX_DigResAccess_time` (`access_time`),
  KEY `IDX_DigResAccess_type` (`access_type`),
  KEY `IDX_DigResAccess_ip` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE IF NOT EXISTS `discounts` (
  `discount_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດສ່ວນຫຼຸດ (PK)',
  `discount_name` varchar(100) NOT NULL COMMENT 'ຊື່ສ່ວນຫຼຸດ (ເຊັ່ນ: ສ່ວນຫຼຸດພີ່ນ້ອງ, ທຶນຮຽນດີ)',
  `discount_type` enum('percentage','fixed') NOT NULL COMMENT 'ປະເພດສ່ວນຫຼຸດ: percentage (ເປີເຊັນ), fixed (ຈຳນວນເງິນຄົງທີ່)',
  `discount_value` decimal(10,2) NOT NULL COMMENT 'ຄ່າຂອງສ່ວນຫຼຸດ (ຖ້າ percentage ແມ່ນ 0-100, ຖ້າ fixed ແມ່ນຈຳນວນເງິນ)',
  `applicable_to` enum('tuition','food','both') NOT NULL DEFAULT 'both' COMMENT 'ໃຊ້ໄດ້ກັບ: ຄ່າຮຽນ, ຄ່າອາຫານ, ຫຼື ທັງສອງ',
  `is_for_group` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ເປັນສ່ວນຫຼຸດສຳລັບກຸ່ມນັກຮຽນ (TRUE/FALSE)',
  `group_criteria` varchar(255) DEFAULT NULL COMMENT 'ເງື່ອນໄຂຂອງກຸ່ມທີ່ໄດ້ຮັບສ່ວນຫຼຸດນີ້ (ຖ້າເປັນສ່ວນຫຼຸດກຸ່ມ)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບສ່ວນຫຼຸດ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ: 1=ເປີດໃຊ້ງານ, 0=ປິດໃຊ້ງານ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງຂໍ້ມູນ',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດຂໍ້ມູນ',
  PRIMARY KEY (`discount_id`),
  UNIQUE KEY `UQ_Discounts_name` (`discount_name`) COMMENT 'ຊື່ສ່ວນຫຼຸດຕ້ອງບໍ່ຊ້ຳກັນ',
  KEY `IDX_Discounts_type` (`discount_type`),
  KEY `IDX_Discounts_active` (`is_active`),
  KEY `IDX_Discounts_group` (`is_for_group`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ຕາຕະລາງເກັບຂໍ້ມູນສ່ວນຫຼຸດ';

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
CREATE TABLE IF NOT EXISTS `districts` (
  `district_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `district_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ເມືອງ (ພາສາລາວ)',
  `district_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ເມືອງ (ພາສາອັງກິດ)',
  `province_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດແຂວງທີ່ເມືອງນີ້ສັງກັດ (FK ຈາກ Provinces)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `UQ_Districts_province_name_lao` (`province_id`,`district_name_lao`),
  UNIQUE KEY `UQ_Districts_province_name_en` (`province_id`,`district_name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ethnicities`
--

DROP TABLE IF EXISTS `ethnicities`;
CREATE TABLE IF NOT EXISTS `ethnicities` (
  `ethnicity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ethnicity_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ຊົນເຜົ່າ (ພາສາລາວ)',
  `ethnicity_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຊົນເຜົ່າ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ethnicity_id`),
  UNIQUE KEY `ethnicities_ethnicity_name_lao_unique` (`ethnicity_name_lao`),
  UNIQUE KEY `ethnicities_ethnicity_name_en_unique` (`ethnicity_name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `examinations`
--

DROP TABLE IF EXISTS `examinations`;
CREATE TABLE IF NOT EXISTS `examinations` (
  `exam_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_name` varchar(255) NOT NULL COMMENT 'ຊື່ການສອບເສັງ (ຕົວຢ່າງ: ເສັງພາກຮຽນ 1)',
  `exam_type` enum('midterm','final','quiz','assignment') NOT NULL COMMENT 'ປະເພດ: midterm, final, quiz, assignment',
  `academic_year_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK)',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມໄລຍະເວລາສອບເສັງ',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດໄລຍະເວລາສອບເສັງ',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມ',
  `total_marks` int(11) NOT NULL COMMENT 'ຄະແນນເຕັມສຳລັບການສອບເສັງນີ້',
  `passing_marks` int(11) DEFAULT NULL COMMENT 'ຄະແນນຂັ້ນຕ່ຳເພື່ອຖືວ່າຜ່ານ',
  `status` enum('upcoming','ongoing','completed') NOT NULL DEFAULT 'upcoming' COMMENT 'ສະຖານະ: upcoming, ongoing, completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`exam_id`),
  UNIQUE KEY `UQ_Exams_name_year` (`exam_name`(191), `academic_year_id`),
  KEY `IDX_Exams_academic_year` (`academic_year_id`),
  KEY `IDX_Exams_type` (`exam_type`),
  KEY `IDX_Exams_status` (`status`),
  KEY `IDX_Exams_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `expense_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_category` varchar(100) NOT NULL COMMENT 'ໝວດໝູ່ລາຍຈ່າຍ (ເຊັ່ນ: ອຸປະກອນ, ສ້ອມແປງ)',
  `amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນທີ່ຈ່າຍ',
  `expense_date` date NOT NULL COMMENT 'ວັນທີທີ່ເກີດລາຍຈ່າຍ',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດລາຍຈ່າຍ',
  `payment_method` enum('cash','bank_transfer','other') DEFAULT NULL COMMENT 'ວິທີການຈ່າຍເງິນ',
  `receipt_number` varchar(50) DEFAULT NULL COMMENT 'ເລກທີ່ໃບບິນ ຫຼື ເອກະສານອ້າງອີງ',
  `receipt_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ຮູບພາບໃບບິນ (ຖ້າມີ)',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ອະນຸມັດລາຍຈ່າຍ (FK)',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ບັນທຶກລາຍຈ່າຍ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`expense_id`),
  KEY `IDX_Expenses_approver` (`approved_by`),
  KEY `IDX_Expenses_creator` (`created_by`),
  KEY `IDX_Expenses_category` (`expense_category`),
  KEY `IDX_Expenses_date` (`expense_date`),
  KEY `IDX_Expenses_payment_method` (`payment_method`),
  KEY `IDX_Expenses_receipt_number` (`receipt_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extracurricular_activities`
--

DROP TABLE IF EXISTS `extracurricular_activities`;
CREATE TABLE IF NOT EXISTS `extracurricular_activities` (
  `activity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_name` varchar(255) NOT NULL COMMENT 'ຊື່ກິດຈະກຳ',
  `activity_type` varchar(100) DEFAULT NULL COMMENT 'ປະເພດກິດຈະກຳ (ເຊັ່ນ: ຊົມລົມ, ກິລາ)',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດກິດຈະກຳ',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມກິດຈະກຳ',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດກິດຈະກຳ',
  `schedule` varchar(255) DEFAULT NULL COMMENT 'ຕາຕະລາງເວລາ (ແບບຂໍ້ຄວາມ)',
  `location` varchar(255) DEFAULT NULL COMMENT 'ສະຖານທີ່ຈັດກິດຈະກຳ',
  `max_participants` int(11) DEFAULT NULL COMMENT 'ຈຳນວນຜູ້ເຂົ້າຮ່ວມສູງສຸດ (NULL=ບໍ່ຈຳກັດ)',
  `coordinator_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ປະສານງານ/ຮັບຜິດຊອບ (FK)',
  `academic_year_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE=ເປີດຮັບ/ດຳເນີນຢູ່)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  UNIQUE KEY `UQ_Activities_name_year` (`activity_name`(191), `academic_year_id`),
  KEY `IDX_Activities_coordinator` (`coordinator_id`),
  KEY `IDX_Activities_academic_year` (`academic_year_id`),
  KEY `IDX_Activities_type` (`activity_type`),
  KEY `IDX_Activities_dates` (`start_date`, `end_date`),
  KEY `IDX_Activities_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `generated_reports`
--

DROP TABLE IF EXISTS `generated_reports`;
CREATE TABLE IF NOT EXISTS `generated_reports` (
  `report_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_name` varchar(255) NOT NULL COMMENT 'ຊື່ລາຍງານ (ທີ່ຜູ້ໃຊ້ຕັ້ງ ຫຼື ລະບົບສ້າງ)',
  `template_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດແມ່ແບບທີ່ໃຊ້ (FK)',
  `report_type` varchar(50) DEFAULT NULL COMMENT 'ປະເພດຂອງລາຍງານ (ຄວນກົງກັບແມ່ແບບ)',
  `report_data` longtext DEFAULT NULL COMMENT 'ຂໍ້ມູນດິບທີ່ໃຊ້ສ້າງ (JSON, XML, etc.) - ອາດຈະບໍ່ເກັບ',
  `report_format` enum('pdf','excel','word','html') NOT NULL COMMENT 'ຮູບແບບຜົນລັບ',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ເກັບໄຟລ໌ລາຍງານ (ຖ້າບັນທຶກເປັນໄຟລ໌)',
  `generated_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ສ້າງລາຍງານ (FK)',
  `generated_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາທີ່ສ້າງລາຍງານ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `IDX_GenReports_template` (`template_id`),
  KEY `IDX_GenReports_generator` (`generated_by`),
  KEY `IDX_GenReports_name` (`report_name`(250)),
  KEY `IDX_GenReports_type` (`report_type`),
  KEY `IDX_GenReports_format` (`report_format`),
  KEY `IDX_GenReports_generated_at` (`generated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `grade_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `class_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK)',
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດວິຊາ (FK)',
  `exam_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດການສອບເສັງ (FK)',
  `marks` decimal(5,2) NOT NULL COMMENT 'ຄະແນນທີ່ໄດ້ຮັບ',
  `grade_letter` varchar(5) DEFAULT NULL COMMENT 'ຄະແນນຕົວອັກສອນ (ເກຣດ)',
  `comments` text DEFAULT NULL COMMENT 'ໝາຍເຫດ/ຄຳຄິດເຫັນຈາກຄູ',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ສະຖານະເຜີຍແຜ່ (TRUE=ເຜີຍແຜ່ແລ້ວ)',
  `graded_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ໃຫ້ຄະແນນ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `UQ_Grades_student_exam_subject` (`student_id`,`exam_id`,`subject_id`),
  KEY `IDX_Grades_student` (`student_id`),
  KEY `IDX_Grades_class` (`class_id`),
  KEY `IDX_Grades_subject` (`subject_id`),
  KEY `IDX_Grades_exam` (`exam_id`),
  KEY `IDX_Grades_grader` (`graded_by`),
  KEY `IDX_Grades_published` (`is_published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

DROP TABLE IF EXISTS `income`;
CREATE TABLE IF NOT EXISTS `income` (
  `income_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `income_category` varchar(100) NOT NULL COMMENT 'ໝວດໝູ່ລາຍຮັບ (ຕົວຢ່າງ: ເງິນບໍລິຈາກ)',
  `amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນທີ່ໄດ້ຮັບ',
  `income_date` date NOT NULL COMMENT 'ວັນທີທີ່ໄດ້ຮັບລາຍຮັບ',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດ ຫຼື ແຫຼ່ງທີ່ມາ',
  `payment_method` enum('cash','bank_transfer','qr_code','other') DEFAULT NULL COMMENT 'ວິທີການຮັບເງິນ',
  `receipt_number` varchar(50) DEFAULT NULL COMMENT 'ເລກທີ່ໃບຮັບເງິນ ຫຼື ເອກະສານອ້າງອີງ',
  `received_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ຮັບເງິນ/ບັນທຶກ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`income_id`),
  KEY `IDX_Income_receiver` (`received_by`),
  KEY `IDX_Income_category` (`income_category`),
  KEY `IDX_Income_date` (`income_date`),
  KEY `IDX_Income_payment_method` (`payment_method`),
  KEY `IDX_Income_receipt_number` (`receipt_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ສົ່ງ (FK)',
  `receiver_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ຮັບ (FK)',
  `subject` varchar(255) DEFAULT NULL COMMENT 'ຫົວຂໍ້ຂໍ້ຄວາມ',
  `message_content` text DEFAULT NULL COMMENT 'ເນື້ອໃນຂໍ້ຄວາມ',
  `read_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ສະຖານະການອ່ານ (TRUE=ອ່ານແລ້ວ)',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'ເວລາທີ່ອ່ານ (NULL=ຍັງບໍ່ອ່ານ)',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ແນບ (ຖ້າມີ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `IDX_Messages_sender` (`sender_id`),
  KEY `IDX_Messages_receiver` (`receiver_id`),
  KEY `IDX_Messages_receiver_read` (`receiver_id`,`read_status`),
  KEY `IDX_Messages_created_at` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nationalities`
--

DROP TABLE IF EXISTS `nationalities`;
CREATE TABLE IF NOT EXISTS `nationalities` (
  `nationality_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nationality_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ສັນຊາດ (ພາສາລາວ)',
  `nationality_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ສັນຊາດ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nationality_id`),
  UNIQUE KEY `nationalities_nationality_name_lao_unique` (`nationality_name_lao`),
  UNIQUE KEY `nationalities_nationality_name_en_unique` (`nationality_name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ໄດ້ຮັບການແຈ້ງເຕືອນ (FK)',
  `title` varchar(255) NOT NULL COMMENT 'ຫົວຂໍ້ການແຈ້ງເຕືອນ',
  `content` text DEFAULT NULL COMMENT 'ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງການແຈ້ງເຕືອນ',
  `notification_type` varchar(50) DEFAULT NULL COMMENT 'ປະເພດຂອງການແຈ້ງເຕືອນ (ຕົວຢ່າງ: new_message, request_approved)',
  `related_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID ຂອງຂໍ້ມູນທີ່ກ່ຽວຂ້ອງ (ເຊັ່ນ: message_id, request_id)',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ສະຖານະການອ່ານ (TRUE = ອ່ານແລ້ວ)',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'ວັນທີ ແລະ ເວລາທີ່ອ່ານ (NULL = ຍັງບໍ່ອ່ານ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `IDX_Notifications_user` (`user_id`),
  KEY `IDX_Notifications_user_read` (`user_id`,`is_read`),
  KEY `IDX_Notifications_related` (`notification_type`,`related_id`),
  KEY `IDX_Notifications_created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
CREATE TABLE IF NOT EXISTS `parents` (
  `parent_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ຜູ້ປົກຄອງ (ພາສາລາວ)',
  `last_name_lao` varchar(100) NOT NULL COMMENT 'ນາມສະກຸນຜູ້ປົກຄອງ (ພາສາລາວ)',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຜູ້ປົກຄອງ (ພາສາອັງກິດ)',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'ນາມສະກຸນຜູ້ປົກຄອງ (ພາສາອັງກິດ)',
  `gender` enum('male','female','other') DEFAULT NULL COMMENT 'ເພດ',
  `date_of_birth` date DEFAULT NULL COMMENT 'ວັນເດືອນປີເກີດ',
  `national_id` varchar(50) DEFAULT NULL COMMENT 'ເລກບັດປະຈຳຕົວ',
  `occupation` varchar(100) DEFAULT NULL COMMENT 'ອາຊີບ',
  `workplace` varchar(255) DEFAULT NULL COMMENT 'ສະຖານທີ່ເຮັດວຽກ',
  `education_level` varchar(100) DEFAULT NULL COMMENT 'ລະດັບການສຶກສາ',
  `income_level` varchar(100) DEFAULT NULL COMMENT 'ລະດັບລາຍຮັບ (ອາດຈະບໍ່ເກັບ)',
  `phone` varchar(20) NOT NULL COMMENT 'ເບີໂທລະສັບຫຼັກ',
  `alternative_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບສຳຮອງ',
  `email` varchar(100) DEFAULT NULL COMMENT 'ອີເມວ',
  `village_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK)',
  `district_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK)',
  `province_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ)',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບໂປຣໄຟລ໌',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`parent_id`),
  UNIQUE KEY `parents_national_id_unique` (`national_id`),
  UNIQUE KEY `parents_email_unique` (`email`),
  KEY `IDX_Parents_name_lao` (`last_name_lao`,`first_name_lao`),
  KEY `IDX_Parents_name_en` (`last_name_en`,`first_name_en`),
  KEY `IDX_Parents_village` (`village_id`),
  KEY `IDX_Parents_district` (`district_id`),
  KEY `IDX_Parents_province` (`province_id`),
  KEY `IDX_Parents_phone` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດການຊຳລະເງິນ (PK)',
  `student_id` int(11) NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `academic_year_id` int(11) NOT NULL COMMENT 'ລະຫັດສົກຮຽນທີ່ຈ່າຍ (FK ຈາກ Academic_Years)',
  `receipt_number` varchar(100) NOT NULL COMMENT 'ເລກທີ່ໃບບິນ',
  `payment_date` datetime NOT NULL COMMENT 'ວັນທີຊຳລະເງິນ',
  `cash` int(10) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນເງິນສົດ',
  `transfer` int(10) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນເງິນໂອນ',
  `food_money` int(10) NOT NULL DEFAULT 0 COMMENT 'ຄ່າອາຫານ',
  `tuition_months` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'ເດືອນຈ່າຍຄ່າຮຽນ Json',
  `food_months` longtext NOT NULL COMMENT 'ເດືອນຈ່າຍຄ່າອາຫານ Json',
  `discount_id` int(11) DEFAULT NULL COMMENT 'ລະຫັດສ່ວນຫຼຸດທີ່ໃຊ້ (FK ຈາກ Discounts)',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ຈຳນວນເງິນສ່ວນຫຼຸດ',
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ຄ່າປັບຈ່າຍຊ້າ',
  `total_amount` decimal(10,2) NOT NULL COMMENT 'ຈຳນວນເງິນລວມທັງໝົດ',
  `note` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມ',
  `received_by` int(11) NOT NULL COMMENT 'ລະຫັດຜູ້ຮັບຊຳລະ (FK ຈາກ Users)',
  `payment_status` enum('pending','confirmed','cancelled','refunded') NOT NULL DEFAULT 'pending' COMMENT 'ສະຖານະຢືນຢັນການຊຳລະ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record',
  PRIMARY KEY (`payment_id`),
  KEY `IDX_Payments_student` (`student_id`),
  KEY `IDX_Payments_academic_year` (`academic_year_id`),
  KEY `IDX_Payments_discount` (`discount_id`),
  KEY `IDX_Payments_receiver` (`received_by`),
  KEY `IDX_Payments_date` (`payment_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ຕາຕະລາງເກັບຂໍ້ມູນການຊຳລະຄ່າຮຽນ';

-- --------------------------------------------------------

--
-- Table structure for table `payment_images`
--

DROP TABLE IF EXISTS `payment_images`;
CREATE TABLE IF NOT EXISTS `payment_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດຮູບພາບ (PK)',
  `payment_id` int(11) NOT NULL COMMENT 'ລະຫັດການຊຳລະເງິນ (FK ຈາກ Payments)',
  `image_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ຮູບພາບ',
  `upload_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີອັບໂຫຼດ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'ເວລາສ້າງ record',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ເວລາອັບເດດ record',
  PRIMARY KEY (`image_id`),
  KEY `IDX_PaymentImages_payment` (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ຕາຕະລາງເກັບຮູບພາບໃບບິນຊຳລະ';

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) NOT NULL COMMENT 'ຊື່ສິດທິ (ເຊັ່ນ: create_user, edit_grades, view_reports)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍວ່າສິດທິນີ້ອະນຸຍາດໃຫ້ເຮັດຫຍັງ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permissions_permission_name_unique` (`permission_name`)
) ENGINE=MyISAM AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

DROP TABLE IF EXISTS `provinces`;
CREATE TABLE IF NOT EXISTS `provinces` (
  `province_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `province_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ແຂວງ (ພາສາລາວ)',
  `province_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ແຂວງ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`province_id`),
  UNIQUE KEY `provinces_province_name_lao_unique` (`province_name_lao`),
  UNIQUE KEY `provinces_province_name_en_unique` (`province_name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `religions`
--

DROP TABLE IF EXISTS `religions`;
CREATE TABLE IF NOT EXISTS `religions` (
  `religion_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `religion_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ສາສະໜາ (ພາສາລາວ)',
  `religion_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ສາສະໜາ (ພາສາອັງກິດ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`religion_id`),
  UNIQUE KEY `religions_religion_name_lao_unique` (`religion_name_lao`),
  UNIQUE KEY `religions_religion_name_en_unique` (`religion_name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_templates`
--

DROP TABLE IF EXISTS `report_templates`;
CREATE TABLE IF NOT EXISTS `report_templates` (
  `template_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL COMMENT 'ຊື່ແມ່ແບບລາຍງານ',
  `template_type` varchar(50) DEFAULT NULL COMMENT 'ປະເພດຂອງແມ່ແບບ (ຕົວຢ່າງ: Transcript, Attendance)',
  `template_content` longtext DEFAULT NULL COMMENT 'ເນື້ອຫາ/ໂຄງສ້າງຂອງແມ່ແບບ (HTML, XML, etc.)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ໃຊ້ງານໄດ້)',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ສ້າງ/ອັບໂຫຼດ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `report_templates_template_name_unique` (`template_name`),
  KEY `IDX_ReportTemplates_creator` (`created_by`),
  KEY `IDX_ReportTemplates_type` (`template_type`),
  KEY `IDX_ReportTemplates_active` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `request_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ຍື່ນຄຳຮ້ອງ (FK)',
  `request_type` varchar(100) NOT NULL COMMENT 'ປະເພດຄຳຮ້ອງ (ຕົວຢ່າງ: Document Request)',
  `subject` varchar(255) NOT NULL COMMENT 'ຫົວຂໍ້ຂອງຄຳຮ້ອງ',
  `content` text DEFAULT NULL COMMENT 'ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງຄຳຮ້ອງ',
  `status` enum('pending','approved','rejected','processing') NOT NULL DEFAULT 'pending' COMMENT 'ສະຖານະ: pending, approved, rejected, processing',
  `response` text DEFAULT NULL COMMENT 'ຄຳຕອບ ຫຼື ຜົນການດຳເນີນການ',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ແນບທີ່ຜູ້ຮ້ອງສົ່ງມາ (ຖ້າມີ)',
  `handled_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ດຳເນີນການ/ອະນຸມັດ (FK)',
  `handled_at` timestamp NULL DEFAULT NULL COMMENT 'ວັນທີ ແລະ ເວລາທີ່ດຳເນີນການສຳເລັດ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `IDX_Requests_user` (`user_id`),
  KEY `IDX_Requests_handler` (`handled_by`),
  KEY `IDX_Requests_type` (`request_type`),
  KEY `IDX_Requests_status` (`status`),
  KEY `IDX_Requests_created_at` (`created_at`),
  KEY `IDX_Requests_handled_at` (`handled_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL COMMENT 'ຊື່ບົດບາດ (ເຊັ່ນ: Admin, Teacher, Student, Parent)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍໜ້າທີ່ຂອງບົດບາດ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `roles_role_name_unique` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_permission_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດບົດບາດ (FK ຈາກ Roles)',
  `permission_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສິດທິ (FK ຈາກ Permissions)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_permission_id`),
  UNIQUE KEY `UQ_RolePermissions_role_perm` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `schedule_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK)',
  `subject_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດວິຊາ (FK)',
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຄູສອນ (FK)',
  `day_of_week` varchar(20) NOT NULL COMMENT 'ມື້ໃນອາທິດ (ຕົວຢ່າງ: Monday)',
  `start_time` time NOT NULL COMMENT 'ເວລາເລີ່ມສອນ',
  `end_time` time NOT NULL COMMENT 'ເວລາເລີກສອນ',
  `room` varchar(50) DEFAULT NULL COMMENT 'ຫ້ອງ ຫຼື ສະຖານທີ່ສອນ',
  `academic_year_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະຕາຕະລາງ (TRUE = ໃຊ້ງານ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`schedule_id`),
  UNIQUE KEY `UQ_Schedules_class_time` (`academic_year_id`,`class_id`,`day_of_week`,`start_time`),
  UNIQUE KEY `UQ_Schedules_room_time` (`academic_year_id`,`room`,`day_of_week`,`start_time`),
  KEY `IDX_Schedules_class` (`class_id`),
  KEY `IDX_Schedules_subject` (`subject_id`),
  KEY `IDX_Schedules_teacher` (`teacher_id`),
  KEY `IDX_Schedules_acad_year` (`academic_year_id`),
  KEY `IDX_Schedules_day` (`day_of_week`),
  KEY `IDX_Schedules_room` (`room`),
  KEY `IDX_Schedules_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_levels`
--

DROP TABLE IF EXISTS `school_levels`;
CREATE TABLE IF NOT EXISTS `school_levels` (
  `level_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດລະດັບການສຶກສາ',
  `level_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ລະດັບການສຶກສາ (ພາສາລາວ)',
  `level_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ລະດັບການສຶກສາ (ພາສາອັງກິດ)',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'ລຳດັບສຳລັບການສະແດງຜົນ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (1=ໃຊ້ງານ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`level_id`),
  UNIQUE KEY `UK_SchoolLevels_name_lao` (`level_name_lao`),
  UNIQUE KEY `UK_SchoolLevels_name_en` (`level_name_en`),
  KEY `IDX_SchoolLevels_active` (`is_active`),
  KEY `IDX_SchoolLevels_sort` (`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ຕາຕະລາງລະດັບການສຶກສາ';

-- --------------------------------------------------------

--
-- Table structure for table `school_store_items`
--

DROP TABLE IF EXISTS `school_store_items`;
CREATE TABLE IF NOT EXISTS `school_store_items` (
  `item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL COMMENT 'ຊື່ສິນຄ້າ',
  `item_code` varchar(50) DEFAULT NULL COMMENT 'ລະຫັດສິນຄ້າ/ບາໂຄດ',
  `category` varchar(100) DEFAULT NULL COMMENT 'ໝວດໝູ່ສິນຄ້າ (ຕົວຢ່າງ: ເຄື່ອງຂຽນ)',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດສິນຄ້າ',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາຂາຍຕໍ່ໜ່ວຍ',
  `stock_quantity` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນສິນຄ້າທີ່ມີໃນສາງ',
  `reorder_level` int(11) DEFAULT NULL COMMENT 'ລະດັບຄົງເຫຼືອຂັ້ນຕ່ຳທີ່ຄວນສັ່ງຊື້ໃໝ່',
  `item_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ຮູບພາບສິນຄ້າ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ຍັງມີຂາຍ/ໃຊ້ງານ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `school_store_items_item_code_unique` (`item_code`),
  UNIQUE KEY `school_store_items_item_name_unique` (`item_name`(191)),
  KEY `IDX_Store_Items_category` (`category`),
  KEY `IDX_Store_Items_active` (`is_active`),
  KEY `IDX_Store_Items_stock` (`stock_quantity`),
  KEY `IDX_Store_Items_price` (`unit_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL COMMENT 'ຊື່ Key ຂອງການຕັ້ງຄ່າ (ຕ້ອງບໍ່ຊ້ຳ)',
  `setting_value` text DEFAULT NULL COMMENT 'ຄ່າຂອງການຕັ້ງຄ່າ (ເກັບເປັນ Text)',
  `setting_group` varchar(50) DEFAULT NULL COMMENT 'ກຸ່ມຂອງການຕັ້ງຄ່າ (ເພື່ອຈັດໝວດໝູ່)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍວ່າການຕັ້ງຄ່ານີ້ແມ່ນຫຍັງ',
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ເປັນການຕັ້ງຄ່າຫຼັກຂອງລະບົບ?',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `settings_setting_key_unique` (`setting_key`),
  KEY `IDX_Settings_group` (`setting_group`),
  KEY `IDX_Settings_system` (`is_system`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_sales`
--

DROP TABLE IF EXISTS `store_sales`;
CREATE TABLE IF NOT EXISTS `store_sales` (
  `sale_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສິນຄ້າທີ່ຂາຍ (FK)',
  `quantity` int(11) NOT NULL COMMENT 'ຈຳນວນທີ່ຂາຍ',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາຕໍ່ໜ່ວຍ (ໃນເວລາຂາຍ)',
  `total_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາລວມ (ຄຳນວນ: quantity * unit_price)',
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ສ່ວນຫຼຸດສຳລັບລາຍການນີ້',
  `final_price` decimal(10,2) NOT NULL COMMENT 'ລາຄາສຸດທິ (ຄຳນວນ: total_price - discount)',
  `buyer_type` enum('student','teacher','parent','other') DEFAULT NULL COMMENT 'ປະເພດຜູ້ຊື້',
  `buyer_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ຊື້ (ອາດຈະແມ່ນ student_id, teacher_id, parent_id)',
  `sale_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາຂາຍ',
  `payment_method` enum('cash','credit','other') NOT NULL DEFAULT 'cash' COMMENT 'ວິທີການຊຳລະ',
  `sold_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ຂາຍ/ບັນທຶກ (FK)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sale_id`),
  KEY `IDX_StoreSales_item` (`item_id`),
  KEY `IDX_StoreSales_seller` (`sold_by`),
  KEY `IDX_StoreSales_buyer` (`buyer_type`,`buyer_id`),
  KEY `IDX_StoreSales_date` (`sale_date`),
  KEY `IDX_StoreSales_payment_method` (`payment_method`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_code` varchar(20) NOT NULL COMMENT 'ລະຫັດປະຈຳຕົວນັກຮຽນ',
  `first_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ນັກຮຽນ (ພາສາລາວ)',
  `last_name_lao` varchar(100) NOT NULL COMMENT 'ນາມສະກຸນນັກຮຽນ (ພາສາລາວ)',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ນັກຮຽນ (ພາສາອັງກິດ)',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'ນາມສະກຸນນັກຮຽນ (ພາສາອັງກິດ)',
  `nickname` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຫຼິ້ນ',
  `gender` enum('male','female','other') NOT NULL COMMENT 'ເພດ: male, female, other',
  `date_of_birth` date NOT NULL COMMENT 'ວັນເດືອນປີເກີດ',
  `nationality_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດສັນຊາດ (FK ຈາກ Nationalities)',
  `religion_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດສາສະໜາ (FK ຈາກ Religions)',
  `ethnicity_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຊົນເຜົ່າ (FK ຈາກ Ethnicities)',
  `village_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK ຈາກ Villages)',
  `district_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK ຈາກ Districts)',
  `province_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK ຈາກ Provinces)',
  `current_address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ ເລກເຮືອນ, ຮ່ອມ, ...)',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌',
  `blood_type` enum('A','B','AB','O','unknown') NOT NULL DEFAULT 'unknown' COMMENT 'ກຸ່ມເລືອດ: A, B, AB, O, unknown',
  `status` enum('active','inactive','graduated','transferred') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະນັກຮຽນ: active, inactive, graduated, transferred',
  `admission_date` date NOT NULL COMMENT 'ວັນທີເຂົ້າຮຽນ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `students_student_code_unique` (`student_code`),
  KEY `IDX_Students_name_lao` (`last_name_lao`,`first_name_lao`),
  KEY `IDX_Students_name_en` (`last_name_en`,`first_name_en`),
  KEY `IDX_Students_nationality` (`nationality_id`),
  KEY `IDX_Students_religion` (`religion_id`),
  KEY `IDX_Students_ethnicity` (`ethnicity_id`),
  KEY `IDX_Students_village` (`village_id`),
  KEY `IDX_Students_district` (`district_id`),
  KEY `IDX_Students_province` (`province_id`),
  KEY `IDX_Students_status` (`status`),
  KEY `IDX_Students_admission_date` (`admission_date`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_achievements`
--

DROP TABLE IF EXISTS `student_achievements`;
CREATE TABLE IF NOT EXISTS `student_achievements` (
  `achievement_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `achievement_type` varchar(100) DEFAULT NULL COMMENT 'ປະເພດຜົນງານ (ເຊັ່ນ: ວິຊາການ, ກິລາ, ສິລະປະ)',
  `title` varchar(255) NOT NULL COMMENT 'ຊື່ຜົນງານ ຫຼື ລາງວັນທີ່ໄດ້ຮັບ',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດກ່ຽວກັບຜົນງານ ຫຼື ລາງວັນ',
  `award_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ໄດ້ຮັບລາງວັນ/ຜົນງານ',
  `issuer` varchar(255) DEFAULT NULL COMMENT 'ຜູ້ມອບລາງວັນ ຫຼື ໜ່ວຍງານທີ່ຈັດ',
  `certificate_path` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ໄຟລ໌ໃບຢັ້ງຢືນ/ຮູບພາບ (ຖ້າມີ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`achievement_id`),
  KEY `IDX_StudAchieve_student` (`student_id`),
  KEY `IDX_StudAchieve_type` (`achievement_type`),
  KEY `IDX_StudAchieve_date` (`award_date`),
  KEY `IDX_StudAchieve_issuer` (`issuer`(250))
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_activities`
--

DROP TABLE IF EXISTS `student_activities`;
CREATE TABLE IF NOT EXISTS `student_activities` (
  `student_activity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດກິດຈະກຳ (FK)',
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `join_date` date DEFAULT NULL COMMENT 'ວັນທີເຂົ້າຮ່ວມ/ລົງທະບຽນ',
  `status` enum('active','completed','dropped') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະ: active, completed, dropped',
  `performance` varchar(100) DEFAULT NULL COMMENT 'ຜົນງານ/ລະດັບການເຂົ້າຮ່ວມ (ຖ້າມີ)',
  `notes` text DEFAULT NULL COMMENT 'ໝາຍເຫດເພີ່ມເຕີມ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`student_activity_id`),
  UNIQUE KEY `UQ_StudAct_student_activity` (`student_id`,`activity_id`),
  KEY `IDX_StudAct_activity` (`activity_id`),
  KEY `IDX_StudAct_student` (`student_id`),
  KEY `IDX_StudAct_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance_summary`
--

DROP TABLE IF EXISTS `student_attendance_summary`;
CREATE TABLE IF NOT EXISTS `student_attendance_summary` (
  `summary_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `class_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນ (FK)',
  `academic_year_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສົກຮຽນ (FK)',
  `month` int(11) NOT NULL COMMENT 'ເດືອນທີ່ສະຫຼຸບ (1-12)',
  `year` int(11) NOT NULL COMMENT 'ປີ ຄ.ສ. ທີ່ສະຫຼຸບ',
  `total_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນຮຽນທັງໝົດໃນເດືອນ',
  `present_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ມາຮຽນ',
  `absent_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ຂາດຮຽນ',
  `late_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ມາຊ້າ',
  `excused_days` int(11) NOT NULL DEFAULT 0 COMMENT 'ຈຳນວນວັນທີ່ລາພັກ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`summary_id`),
  UNIQUE KEY `UQ_StudAttSumm_student_period` (`student_id`,`academic_year_id`,`year`,`month`),
  KEY `IDX_StudAttSumm_student` (`student_id`),
  KEY `IDX_StudAttSumm_class` (`class_id`),
  KEY `IDX_StudAttSumm_acad_year` (`academic_year_id`),
  KEY `IDX_StudAttSumm_period` (`academic_year_id`,`year`,`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_behavior_records`
--

DROP TABLE IF EXISTS `student_behavior_records`;
CREATE TABLE IF NOT EXISTS `student_behavior_records` (
  `behavior_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `record_type` enum('positive','negative','neutral') NOT NULL COMMENT 'ປະເພດພຶດຕິກຳ (ບວກ, ລົບ, ກາງ)',
  `description` text NOT NULL COMMENT 'ລາຍລະອຽດພຶດຕິກຳທີ່ສັງເກດເຫັນ',
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຄູສອນ ຫຼື ຜູ້ບັນທຶກ (FK)',
  `record_date` date NOT NULL COMMENT 'ວັນທີທີ່ສັງເກດເຫັນ/ບັນທຶກ',
  `action_taken` text DEFAULT NULL COMMENT 'ການດຳເນີນການທີ່ໄດ້ເຮັດໄປແລ້ວ (ຖ້າມີ)',
  `follow_up` text DEFAULT NULL COMMENT 'ການຕິດຕາມຜົນ ຫຼື ໝາຍເຫດເພີ່ມເຕີມ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`behavior_id`),
  KEY `IDX_StudBehavior_student` (`student_id`),
  KEY `IDX_StudBehavior_teacher` (`teacher_id`),
  KEY `IDX_StudBehavior_type` (`record_type`),
  KEY `IDX_StudBehavior_date` (`record_date`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

DROP TABLE IF EXISTS `student_documents`;
CREATE TABLE IF NOT EXISTS `student_documents` (
  `document_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `document_type` varchar(100) NOT NULL COMMENT 'ປະເພດເອກະສານ (ເຊັ່ນ: ໃບແຈ້ງໂທດ, ໃບຄະແນນ, ສຳມະໂນຄົວ, ບັດປະຈຳຕົວ)',
  `document_name` varchar(255) NOT NULL COMMENT 'ຊື່ເອກະສານ ຫຼື ຊື່ໄຟລ໌',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ເກັບໄຟລ໌ໃນລະບົບ',
  `file_size` int(11) DEFAULT NULL COMMENT 'ຂະໜາດໄຟລ໌ (ເປັນ bytes)',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ຊະນິດຂອງໄຟລ໌ (MIME Type ຫຼື ນາມສະກຸນ, ເຊັ່ນ: application/pdf, image/jpeg)',
  `upload_date` timestamp NULL DEFAULT '2025-04-11 07:51:59' COMMENT 'ວັນທີ ແລະ ເວລາອັບໂຫຼດ',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບເອກະສານ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`document_id`),
  KEY `IDX_StudDocs_student` (`student_id`),
  KEY `IDX_StudDocs_type` (`document_type`),
  KEY `IDX_StudDocs_upload_date` (`upload_date`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_emergency_contacts`
--

DROP TABLE IF EXISTS `student_emergency_contacts`;
CREATE TABLE IF NOT EXISTS `student_emergency_contacts` (
  `contact_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `contact_name` varchar(255) NOT NULL COMMENT 'ຊື່ ແລະ ນາມສະກຸນຂອງຜູ້ຕິດຕໍ່',
  `relationship` varchar(100) DEFAULT NULL COMMENT 'ຄວາມສຳພັນກັບນັກຮຽນ (ເຊັ່ນ: ພໍ່, ແມ່)',
  `phone` varchar(20) NOT NULL COMMENT 'ເບີໂທລະສັບຫຼັກທີ່ຕິດຕໍ່ໄດ້',
  `alternative_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບສຳຮອງ (ຖ້າມີ)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ຂອງຜູ້ຕິດຕໍ່ (ຖ້າມີ)',
  `priority` int(11) DEFAULT 1 COMMENT 'ລຳດັບຄວາມສຳຄັນໃນການຕິດຕໍ່ (1 = ຕິດຕໍ່ກ່ອນ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `IDX_StudEmergContacts_student` (`student_id`),
  KEY `IDX_StudEmergContacts_priority` (`student_id`,`priority`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

DROP TABLE IF EXISTS `student_enrollments`;
CREATE TABLE IF NOT EXISTS `student_enrollments` (
  `enrollment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `class_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນທີ່ລົງທະບຽນ (FK)',
  `academic_year_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດສົກຮຽນທີ່ລົງທະບຽນ (FK)',
  `enrollment_date` date NOT NULL COMMENT 'ວັນທີລົງທະບຽນເຂົ້າຫ້ອງນີ້',
  `enrollment_status` enum('enrolled','transferred','dropped') NOT NULL DEFAULT 'enrolled' COMMENT 'ສະຖານະ: enrolled, transferred, dropped',
  `previous_class_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຫ້ອງຮຽນກ່ອນໜ້າ (FK)',
  `is_new_student` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ເປັນນັກຮຽນໃໝ່ໃນສົກຮຽນນີ້',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`enrollment_id`),
  UNIQUE KEY `UQ_StudEnroll_student_year` (`student_id`,`academic_year_id`),
  KEY `IDX_StudEnroll_student` (`student_id`),
  KEY `IDX_StudEnroll_class` (`class_id`),
  KEY `IDX_StudEnroll_acad_year` (`academic_year_id`),
  KEY `IDX_StudEnroll_prev_class` (`previous_class_id`),
  KEY `IDX_StudEnroll_status` (`enrollment_status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_health_records`
--

DROP TABLE IF EXISTS `student_health_records`;
CREATE TABLE IF NOT EXISTS `student_health_records` (
  `health_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `health_condition` varchar(255) DEFAULT NULL COMMENT 'ສະພາບສຸຂະພາບທົ່ວໄປ ຫຼື ພະຍາດປະຈຳຕົວ',
  `medications` text DEFAULT NULL COMMENT 'ລາຍການຢາທີ່ນັກຮຽນໃຊ້ປະຈຳ',
  `allergies` text DEFAULT NULL COMMENT 'ປະຫວັດການແພ້ຢາ/ອາຫານ',
  `special_needs` text DEFAULT NULL COMMENT 'ຄວາມຕ້ອງການພິເສດດ້ານສຸຂະພາບ',
  `doctor_name` varchar(100) DEFAULT NULL COMMENT 'ຊື່ແພດປະຈຳຕົວ (ຖ້າມີ)',
  `doctor_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທແພດປະຈຳຕົວ (ຖ້າມີ)',
  `record_date` date NOT NULL COMMENT 'ວັນທີບັນທຶກ/ອັບເດດຂໍ້ມູນ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`health_id`),
  KEY `IDX_StudHealth_student` (`student_id`),
  KEY `IDX_StudHealth_record_date` (`record_date`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_interests`
--

DROP TABLE IF EXISTS `student_interests`;
CREATE TABLE IF NOT EXISTS `student_interests` (
  `interest_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `interest_category` varchar(100) DEFAULT NULL COMMENT 'ໝວດໝູ່ຄວາມສົນໃຈ (ເຊັ່ນ: ກິລາ, ດົນຕີ, ສິລະປະ, ວິຊາການ)',
  `interest_name` varchar(255) NOT NULL COMMENT 'ຊື່ຄວາມສົນໃຈສະເພາະ (ເຊັ່ນ: ບານເຕະ, ເປຍໂນ, ແຕ້ມຮູບ, Math Club)',
  `description` text DEFAULT NULL COMMENT 'ລາຍລະອຽດເພີ່ມເຕີມກ່ຽວກັບຄວາມສົນໃຈນີ້',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`interest_id`),
  UNIQUE KEY `UQ_StudInterests_student_interest` (`student_id`, `interest_name`(191)),
  KEY `IDX_StudInterests_student` (`student_id`),
  KEY `IDX_StudInterests_category` (`interest_category`),
  KEY `IDX_StudInterests_name` (`interest_name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_parent`
--

DROP TABLE IF EXISTS `student_parent`;
CREATE TABLE IF NOT EXISTS `student_parent` (
  `student_parent_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `parent_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ປົກຄອງ (FK)',
  `relationship` enum('father','mother','guardian','other') NOT NULL COMMENT 'ຄວາມສຳພັນ (ພໍ່, ແມ່, ຜູ້ປົກຄອງ, ອື່ນໆ)',
  `is_primary_contact` tinyint(1) DEFAULT 0 COMMENT 'ເປັນຜູ້ຕິດຕໍ່ຫຼັກ ຫຼື ບໍ່ (TRUE/FALSE)',
  `has_custody` tinyint(1) DEFAULT 1 COMMENT 'ມີສິດໃນການດູແລ ຫຼື ບໍ່ (TRUE/FALSE)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`student_parent_id`),
  UNIQUE KEY `UQ_StudentParent_pair` (`student_id`,`parent_id`),
  KEY `IDX_StudentParent_student` (`student_id`),
  KEY `IDX_StudentParent_parent` (`parent_id`),
  KEY `IDX_StudentParent_primary` (`is_primary_contact`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_previous_education`
--

DROP TABLE IF EXISTS `student_previous_education`;
CREATE TABLE IF NOT EXISTS `student_previous_education` (
  `education_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `school_name` varchar(255) NOT NULL COMMENT 'ຊື່ໂຮງຮຽນເກົ່າ',
  `education_level` varchar(100) DEFAULT NULL COMMENT 'ລະດັບການສຶກສາທີ່ຈົບ (ເຊັ່ນ: ປະຖົມ, ມັດທະຍົມຕົ້ນ)',
  `from_year` int(11) DEFAULT NULL COMMENT 'ປີທີ່ເລີ່ມຮຽນ (ຄ.ສ.)',
  `to_year` int(11) DEFAULT NULL COMMENT 'ປີທີ່ຈົບ (ຄ.ສ.)',
  `certificate` varchar(255) DEFAULT NULL COMMENT 'ຊື່ ຫຼື ທີ່ຢູ່ໄຟລ໌ປະກາດ/ໃບຢັ້ງຢືນ',
  `gpa` decimal(3,2) DEFAULT NULL COMMENT 'ຄະແນນສະເລ່ຍ (GPA)',
  `description` text DEFAULT NULL COMMENT 'ໝາຍເຫດ/ລາຍລະອຽດເພີ່ມເຕີມ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`education_id`),
  KEY `IDX_StudPrevEdu_student` (`student_id`),
  KEY `IDX_StudPrevEdu_school` (`school_name`(250)),
  KEY `IDX_StudPrevEdu_level` (`education_level`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_previous_locations`
--

DROP TABLE IF EXISTS `student_previous_locations`;
CREATE TABLE IF NOT EXISTS `student_previous_locations` (
  `location_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK ຈາກ Students)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ລະອຽດ (ເລກເຮືອນ, ຮ່ອມ...)',
  `village_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK ຈາກ Villages). ອາດຈະ NULL ຖ້າຢູ່ນອກ ຫຼື ບໍ່ຮູ້.',
  `district_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK ຈາກ Districts). ອາດຈະ NULL.',
  `province_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK ຈາກ Provinces). ອາດຈະ NULL.',
  `country` varchar(100) NOT NULL DEFAULT 'Laos' COMMENT 'ປະເທດ',
  `from_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ເລີ່ມອາໄສຢູ່ທີ່ຢູ່ນີ້',
  `to_date` date DEFAULT NULL COMMENT 'ວັນທີທີ່ຍ້າຍອອກຈາກທີ່ຢູ່ນີ້',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `IDX_StudPrevLoc_student` (`student_id`),
  KEY `IDX_StudPrevLoc_village` (`village_id`),
  KEY `IDX_StudPrevLoc_district` (`district_id`),
  KEY `IDX_StudPrevLoc_province` (`province_id`),
  KEY `IDX_StudPrevLoc_country` (`country`),
  KEY `IDX_StudPrevLoc_dates` (`from_date`,`to_date`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_siblings`
--

DROP TABLE IF EXISTS `student_siblings`;
CREATE TABLE IF NOT EXISTS `student_siblings` (
  `sibling_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນຄົນທີໜຶ່ງ (FK ຈາກ Students)',
  `sibling_student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນຄົນທີສອງ (ພີ່ນ້ອງ) (FK ຈາກ Students)',
  `relationship` enum('brother','sister','step_brother','step_sister') NOT NULL COMMENT 'ຄວາມສຳພັນ: brother, sister, step_brother, step_sister',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sibling_id`),
  UNIQUE KEY `UQ_StudSiblings_pair` (`student_id`,`sibling_student_id`),
  KEY `IDX_StudSiblings_student` (`student_id`),
  KEY `IDX_StudSiblings_sibling` (`sibling_student_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_special_needs`
--

DROP TABLE IF EXISTS `student_special_needs`;
CREATE TABLE IF NOT EXISTS `student_special_needs` (
  `special_need_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດນັກຮຽນ (FK)',
  `need_type` varchar(100) NOT NULL COMMENT 'ປະເພດຄວາມຕ້ອງການພິເສດ (ເຊັ່ນ: ด้านการเรียนรู้, ด้านร่างกาย)',
  `description` text NOT NULL COMMENT 'ລາຍລະອຽດຂອງຄວາມຕ້ອງການ',
  `recommendations` text DEFAULT NULL COMMENT 'ຂໍ້ສະເໜີແນະໃນການຊ່ວຍເຫຼືອ/ຈັດການຮຽນ',
  `support_required` text DEFAULT NULL COMMENT 'ການສະໜັບສະໜູນທີ່ຕ້ອງການຈາກໂຮງຮຽນ',
  `external_support` varchar(255) DEFAULT NULL COMMENT 'ຂໍ້ມູນການສະໜັບສະໜູນຈາກພາຍນອກ (ຖ້າມີ)',
  `start_date` date DEFAULT NULL COMMENT 'ວັນທີເລີ່ມຕົ້ນ (ທີ່ພົບ ຫຼື ເລີ່ມຊ່ວຍເຫຼືອ)',
  `end_date` date DEFAULT NULL COMMENT 'ວັນທີສິ້ນສຸດ (ຖ້າມີ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`special_need_id`),
  UNIQUE KEY `UQ_StudSpecialNeeds_student_type` (`student_id`,`need_type`),
  KEY `IDX_StudSpecialNeeds_student` (`student_id`),
  KEY `IDX_StudSpecialNeeds_type` (`need_type`),
  KEY `IDX_StudSpecialNeeds_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) NOT NULL COMMENT 'ລະຫັດວິຊາ (ຕົວຢ່າງ: MTH101)',
  `subject_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ວິຊາ (ພາສາລາວ)',
  `subject_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ວິຊາ (ພາສາອັງກິດ)',
  `credit_hours` int(11) DEFAULT NULL COMMENT 'ຈຳນວນໜ່ວຍກິດ (ຖ້າມີ)',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍກ່ຽວກັບວິຊາ',
  `category` varchar(50) DEFAULT NULL COMMENT 'ໝວດໝູ່ຂອງວິຊາ (ຕົວຢ່າງ: ວິທະຍາສາດ)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ສະຖານະ (TRUE = ຍັງເປີດສອນ)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `subjects_subject_code_unique` (`subject_code`),
  UNIQUE KEY `subjects_subject_name_lao_unique` (`subject_name_lao`),
  UNIQUE KEY `subjects_subject_name_en_unique` (`subject_name_en`),
  KEY `IDX_Subjects_category` (`category`),
  KEY `IDX_Subjects_active` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
CREATE TABLE IF NOT EXISTS `system_logs` (
  `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_level` enum('info','warning','error','critical') NOT NULL COMMENT 'ລະດັບ Log',
  `log_source` varchar(100) DEFAULT NULL COMMENT 'ແຫຼ່ງທີ່ມາຂອງ Log (ເຊັ່ນ: Module, Function)',
  `message` text NOT NULL COMMENT 'ຂໍ້ຄວາມ Log',
  `context` text DEFAULT NULL COMMENT 'ຂໍ້ມູນເພີ່ມເຕີມ (Context) ເຊັ່ນ: Stack trace, JSON',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ທີ່ຢູ່ IP ທີ່ກ່ຽວຂ້ອງ (ຖ້າມີ)',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດຜູ້ໃຊ້ທີ່ກ່ຽວຂ້ອງ (FK)',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `IDX_SystemLogs_level` (`log_level`),
  KEY `IDX_SystemLogs_source` (`log_source`),
  KEY `IDX_SystemLogs_user` (`user_id`),
  KEY `IDX_SystemLogs_ip` (`ip_address`),
  KEY `IDX_SystemLogs_created_at` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE IF NOT EXISTS `teachers` (
  `teacher_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_code` varchar(20) NOT NULL COMMENT 'ລະຫັດປະຈຳຕົວຄູສອນ',
  `first_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ຄູສອນ (ພາສາລາວ)',
  `last_name_lao` varchar(100) NOT NULL COMMENT 'ນາມສະກຸນຄູສອນ (ພາສາລາວ)',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ຄູສອນ (ພາສາອັງກິດ)',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'ນາມສະກຸນຄູສອນ (ພາສາອັງກິດ)',
  `gender` enum('male','female','other') NOT NULL COMMENT 'ເພດ',
  `date_of_birth` date NOT NULL COMMENT 'ວັນເດືອນປີເກີດ',
  `national_id` varchar(50) DEFAULT NULL COMMENT 'ເລກບັດປະຈຳຕົວ',
  `phone` varchar(20) NOT NULL COMMENT 'ເບີໂທລະສັບຫຼັກ',
  `alternative_phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບສຳຮອງ',
  `email` varchar(100) NOT NULL COMMENT 'ອີເມວ (ໃຊ້ສຳລັບເຂົ້າລະບົບ)',
  `village_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດບ້ານ (FK)',
  `district_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດເມືອງ (FK)',
  `province_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ລະຫັດແຂວງ (FK)',
  `address` text DEFAULT NULL COMMENT 'ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ)',
  `highest_education` varchar(100) DEFAULT NULL COMMENT 'ລະດັບການສຶກສາສູງສຸດ',
  `specialization` varchar(255) DEFAULT NULL COMMENT 'ຄວາມຊຳນານ/ວິຊາເອກ',
  `employment_date` date NOT NULL COMMENT 'ວັນທີເລີ່ມຈ້າງງານ/ເຮັດວຽກ',
  `contract_type` enum('full_time','part_time','contract') DEFAULT NULL COMMENT 'ປະເພດສັນຍາຈ້າງ',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະການເຮັດວຽກ',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`teacher_id`),
  UNIQUE KEY `teachers_teacher_code_unique` (`teacher_code`),
  UNIQUE KEY `teachers_email_unique` (`email`),
  UNIQUE KEY `teachers_national_id_unique` (`national_id`),
  KEY `IDX_Teachers_name_lao` (`last_name_lao`,`first_name_lao`),
  KEY `IDX_Teachers_name_en` (`last_name_en`,`first_name_en`),
  KEY `IDX_Teachers_village` (`village_id`),
  KEY `IDX_Teachers_district` (`district_id`),
  KEY `IDX_Teachers_province` (`province_id`),
  KEY `IDX_Teachers_status` (`status`),
  KEY `IDX_Teachers_specialization` (`specialization`(250)),
  KEY `IDX_Teachers_employment_date` (`employment_date`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_documents`
--

DROP TABLE IF EXISTS `teacher_documents`;
CREATE TABLE IF NOT EXISTS `teacher_documents` (
  `document_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຄູສອນ (FK)',
  `document_type` varchar(100) NOT NULL COMMENT 'ປະເພດເອກະສານ (ເຊັ່ນ: ໃບປະກາດ, ສັນຍາຈ້າງ)',
  `document_name` varchar(255) NOT NULL COMMENT 'ຊື່ເອກະສານ/ຊື່ໄຟລ໌',
  `file_path` varchar(255) NOT NULL COMMENT 'ທີ່ຢູ່ເກັບໄຟລ໌ໃນລະບົບ',
  `file_size` int(11) DEFAULT NULL COMMENT 'ຂະໜາດໄຟລ໌ (ເປັນ bytes)',
  `file_type` varchar(100) DEFAULT NULL COMMENT 'ຊະນິດຂອງໄຟລ໌ (MIME Type ຫຼື ນາມສະກຸນ)',
  `upload_date` timestamp NULL DEFAULT current_timestamp() COMMENT 'ວັນທີ ແລະ ເວລາອັບໂຫຼດ',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບເອກະສານ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`document_id`),
  KEY `IDX_TeacherDocs_teacher` (`teacher_id`),
  KEY `IDX_TeacherDocs_type` (`document_type`),
  KEY `IDX_TeacherDocs_upload_date` (`upload_date`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_tasks`
--

DROP TABLE IF EXISTS `teacher_tasks`;
CREATE TABLE IF NOT EXISTS `teacher_tasks` (
  `task_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດເອກະລັກຂອງວຽກທີ່ເພີ່ມອັດຕະໂນມັດ',
  `title` varchar(191) NOT NULL COMMENT 'ຫົວຂໍ້ຫຼືຊື່ຂອງວຽກທີ່ມອບໝາຍ',
  `description` text DEFAULT NULL COMMENT 'ຄຳອະທິບາຍລາຍລະອຽດຂອງວຽກ',
  `assigned_by` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຜູ້ບໍລິຫານທີ່ເປັນຜູ້ມອບໝາຍວຽກ',
  `assigned_to` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດຄູທີ່ໄດ້ຮັບມອບໝາຍວຽກ',
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium' COMMENT 'ລະດັບຄວາມສຳຄັນຂອງວຽກ: ຕໍ່າ, ປານກາງ, ສູງ',
  `start_date` date NOT NULL COMMENT 'ວັນທີເລີ່ມຕົ້ນຂອງວຽກ',
  `due_date` date NOT NULL COMMENT 'ວັນທີກຳນົດສົ່ງວຽກ',
  `status` enum('pending','in_progress','completed','overdue') NOT NULL DEFAULT 'pending' COMMENT 'ສະຖານະປະຈຸບັນຂອງວຽກ: ລໍຖ້າ, ກຳລັງດຳເນີນການ, ສຳເລັດແລ້ວ, ເກີນກຳນົດ',
  `progress` int(11) NOT NULL DEFAULT 0 COMMENT 'ເປີເຊັນຄວາມຄືບໜ້າຂອງວຽກ (0-100)',
  `latest_update` text DEFAULT NULL COMMENT 'ບັນທຶກການອັບເດດຄວາມຄືບໜ້າຫຼ້າສຸດ',
  `update_history` longtext DEFAULT NULL COMMENT 'ປະຫວັດການອັບເດດທັງໝົດໃນຮູບແບບ JSON',
  `comments` longtext DEFAULT NULL COMMENT 'ຄຳເຫັນແລະການສົນທະນາກ່ຽວກັບວຽກໃນຮູບແບບ JSON',
  `completion_note` text DEFAULT NULL COMMENT 'ບັນທຶກຫຼືໝາຍເຫດເມື່ອວຽກສຳເລັດແລ້ວ',
  `completion_date` datetime DEFAULT NULL COMMENT 'ວັນທີແລະເວລາທີ່ສຳເລັດວຽກ',
  `rating` tinyint(4) DEFAULT NULL COMMENT 'ຄະແນນປະເມີນຄຸນນະພາບຂອງວຽກທີ່ສຳເລັດ (1-5)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`task_id`),
  KEY `teacher_tasks_assigned_by_foreign` (`assigned_by`),
  KEY `teacher_tasks_assigned_to_foreign` (`assigned_to`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT 'ຊື່ຜູ້ໃຊ້ສຳລັບເຂົ້າລະບົບ',
  `password` varchar(255) NOT NULL COMMENT 'ລະຫັດຜ່ານ (ເກັບແບບເຂົ້າລະຫັດ)',
  `email` varchar(100) NOT NULL COMMENT 'ອີເມວ',
  `phone` varchar(20) DEFAULT NULL COMMENT 'ເບີໂທລະສັບ',
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດບົດບາດ (FK)',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active' COMMENT 'ສະຖານະຜູ້ໃຊ້: active, inactive, suspended',
  `user_type` varchar(191) DEFAULT NULL,
  `related_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'ເວລາເຂົ້າລະບົບຄັ້ງສຸດທ້າຍ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_user_type_related_id_foreign` (`user_type`,`related_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `phone`, `role_id`, `status`, `user_type`, `related_id`, `profile_image`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$0PQcLiOkUQHHKMs6GSA3Z.5aMSjZ0PB/RHaWErQQWC5WgtPgfJJ9G', 'admin@sayfone.la', '02055555555', 1, 'active', 'teacher', 1, 'profile-images/01JRPS38KQS2R9YMMW7ZW6AF9H.png', '2025-06-02 02:17:39', '2025-04-11 19:54:49', '2025-06-02 02:17:39'),
(2, 'school_admin', '$2y$12$zycQEHVc2P/gUpc1k0utKOzud/YD1fZsFC7A13r9PgT3Y1VLoDgam', 'schooladmin@sayfone.la', '02066666666', 2, 'active', 'student', 1, NULL, '2025-04-29 21:21:07', '2025-04-11 19:54:49', '2025-04-29 21:21:07'),
(3, 'finance', '$2y$12$btGmu4.DnscIXxlA/Qk6yOkZP2reeoVQ3ggpqEt8szLanaol1DWpe', 'finance@sayfone.la', '02077777777', 4, 'active', 'parent', 2, NULL, NULL, '2025-04-11 19:54:49', '2025-04-20 01:44:03'),
(4, 'user01', '$2y$12$75d9CtuDdTzRVAtK6SxohuMEav76QyXzqqDdn.8AlUo9eLbPFAlZ6', 'user1@example.com', '020977417015', 6, 'active', NULL, NULL, 'profile-images/01JRSXVNEA2CXB4BP2MGBKK8KV.jpg', NULL, '2025-04-14 03:34:58', '2025-04-14 03:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

DROP TABLE IF EXISTS `user_activities`;
CREATE TABLE IF NOT EXISTS `user_activities` (
  `activity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity_type` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `activity_time` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`activity_id`),
  KEY `user_activities_user_id_foreign` (`user_id`),
  KEY `user_activities_activity_type_index` (`activity_type`),
  KEY `user_activities_activity_time_index` (`activity_time`)
) ENGINE=MyISAM AUTO_INCREMENT=3207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `villages`
--

DROP TABLE IF EXISTS `villages`;
CREATE TABLE IF NOT EXISTS `villages` (
  `village_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `village_name_lao` varchar(100) NOT NULL COMMENT 'ຊື່ບ້ານ (ພາສາລາວ)',
  `village_name_en` varchar(100) DEFAULT NULL COMMENT 'ຊື່ບ້ານ (ພາສາອັງກິດ)',
  `district_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ລະຫັດເມືອງທີ່ບ້ານນີ້ສັງກັດ (FK ຈາກ Districts)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`village_id`),
  UNIQUE KEY `UQ_Villages_district_name_lao` (`district_id`,`village_name_lao`),
  UNIQUE KEY `UQ_Villages_district_name_en` (`district_id`,`village_name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
