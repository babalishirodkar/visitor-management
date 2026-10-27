-- ========================================================================
-- GateDesk - Visitor Management & Gate Pass System
-- Database Schema Definition
-- Database: visitor_management
-- ========================================================================

CREATE DATABASE IF NOT EXISTS `visitor_management`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `visitor_management`;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------
-- 1. Organization & Multi-Site Configuration
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `organizations`;
CREATE TABLE `organizations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `org_name` VARCHAR(150) NOT NULL,
  `tagline` VARCHAR(255) DEFAULT 'Enterprise Visitor & Gate Pass Management',
  `logo_url` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `website` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `org_id` INT UNSIGNED NOT NULL,
  `site_name` VARCHAR(100) NOT NULL,
  `site_code` VARCHAR(20) DEFAULT NULL,
  `city` VARCHAR(50) DEFAULT NULL,
  `state` VARCHAR(50) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_sites_org` (`org_id`),
  CONSTRAINT `fk_sites_org` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `dept_name` VARCHAR(100) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_dept_site` (`site_id`),
  CONSTRAINT `fk_dept_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `gates`;
CREATE TABLE `gates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `gate_name` VARCHAR(50) NOT NULL,
  `gate_type` ENUM('visitor', 'material', 'staff', 'mixed') DEFAULT 'mixed',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_gates_site` (`site_id`),
  CONSTRAINT `fk_gates_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 2. RBAC & Users
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_key` VARCHAR(50) UNIQUE NOT NULL,
  `role_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `org_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED DEFAULT NULL,
  `dept_id` INT UNSIGNED DEFAULT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `employee_id` VARCHAR(50) DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `designation` VARCHAR(100) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_site` (`site_id`),
  CONSTRAINT `fk_users_org` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_users_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 3. Visitor Master & Pre-registrations / Appointments
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `visitor_categories`;
CREATE TABLE `visitor_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(50) UNIQUE NOT NULL,
  `requires_approval` TINYINT(1) DEFAULT 1,
  `requires_id_proof` TINYINT(1) DEFAULT 1,
  `requires_safety_induction` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `visitors`;
CREATE TABLE `visitors` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `visitor_code` VARCHAR(50) UNIQUE NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `company` VARCHAR(150) DEFAULT 'Self',
  `category_id` INT UNSIGNED NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `id_proof_type` VARCHAR(50) DEFAULT NULL,
  `id_proof_number` VARCHAR(100) DEFAULT NULL,
  `photo_url` VARCHAR(255) DEFAULT NULL,
  `total_visits` INT UNSIGNED DEFAULT 0,
  `last_visit_at` DATETIME DEFAULT NULL,
  `flag` ENUM('', 'watch', 'deny') DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_visitors_phone` (`phone`),
  KEY `idx_visitors_name` (`name`),
  CONSTRAINT `fk_visitors_cat` FOREIGN KEY (`category_id`) REFERENCES `visitor_categories` (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `appointment_ref` VARCHAR(50) UNIQUE NOT NULL,
  `visitor_id` INT UNSIGNED NOT NULL,
  `host_user_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `dept_id` INT UNSIGNED DEFAULT NULL,
  `scheduled_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `duration` VARCHAR(30) DEFAULT '1 hour',
  `purpose` VARCHAR(255) NOT NULL,
  `destination` VARCHAR(100) DEFAULT NULL,
  `accompanying_count` INT UNSIGNED DEFAULT 0,
  `vehicle_number` VARCHAR(30) DEFAULT NULL,
  `attached_doc_url` VARCHAR(255) DEFAULT NULL,
  `is_walkin` TINYINT(1) DEFAULT 0,
  `status` ENUM('Pending approval', 'Approved', 'Checked in', 'Completed', 'Rejected', 'Info requested', 'Rescheduled', 'Cancelled', 'No-show') DEFAULT 'Pending approval',
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_apt_ref` (`appointment_ref`),
  KEY `idx_apt_date` (`scheduled_date`),
  KEY `idx_apt_site` (`site_id`),
  CONSTRAINT `fk_apt_visitor` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_apt_host` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_apt_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_apt_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_apt_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `appointment_companions`;
CREATE TABLE `appointment_companions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `id_proof_number` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_companions_apt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 4. Gate Passes, Badges & Entry/Exit Logs
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `gate_passes`;
CREATE TABLE `gate_passes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pass_number` VARCHAR(50) UNIQUE NOT NULL,
  `pass_type` ENUM('Visitor', 'Contractor', 'Delivery', 'Material', 'Temporary') NOT NULL DEFAULT 'Visitor',
  `appointment_id` INT UNSIGNED DEFAULT NULL,
  `visitor_id` INT UNSIGNED DEFAULT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `host_user_id` INT UNSIGNED DEFAULT NULL,
  `issued_at` DATETIME NOT NULL,
  `valid_until` DATETIME NOT NULL,
  `qr_payload` TEXT DEFAULT NULL,
  `badge_printed` TINYINT(1) DEFAULT 0,
  `status` ENUM('Active', 'Used', 'Expired', 'Revoked', 'Cancelled') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_pass_num` (`pass_number`),
  KEY `idx_pass_site` (`site_id`),
  KEY `idx_pass_status` (`status`),
  CONSTRAINT `fk_passes_apt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_passes_visitor` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_passes_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_passes_host` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `gate_logs`;
CREATE TABLE `gate_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pass_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `gate_id` INT UNSIGNED NOT NULL,
  `check_in_time` DATETIME NOT NULL,
  `check_in_guard_id` INT UNSIGNED NOT NULL,
  `check_out_time` DATETIME DEFAULT NULL,
  `check_out_guard_id` INT UNSIGNED DEFAULT NULL,
  `check_out_gate_id` INT UNSIGNED DEFAULT NULL,
  `occupancy_status` ENUM('Inside', 'Exited') DEFAULT 'Inside',
  `remarks` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_logs_site_occ` (`site_id`, `occupancy_status`),
  KEY `idx_logs_pass` (`pass_id`),
  CONSTRAINT `fk_logs_pass` FOREIGN KEY (`pass_id`) REFERENCES `gate_passes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_logs_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_logs_gate` FOREIGN KEY (`gate_id`) REFERENCES `gates` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_logs_in_guard` FOREIGN KEY (`check_in_guard_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_logs_out_guard` FOREIGN KEY (`check_out_guard_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_logs_out_gate` FOREIGN KEY (`check_out_gate_id`) REFERENCES `gates` (`id`)
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 5. Approval Workflows & Requests
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `approval_workflows`;
CREATE TABLE `approval_workflows` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `request_type` ENUM('Visitor', 'Material inward', 'Material outward', 'Contractor', 'Walk-in') NOT NULL,
  `site_id` INT UNSIGNED DEFAULT NULL,
  `total_levels` TINYINT UNSIGNED DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  CONSTRAINT `fk_wf_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `approvals`;
CREATE TABLE `approvals` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `approval_code` VARCHAR(50) UNIQUE NOT NULL,
  `request_type` ENUM('Visitor', 'Material inward', 'Material outward', 'Contractor', 'Walk-in') NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `requester_user_id` INT UNSIGNED DEFAULT NULL,
  `approver_user_id` INT UNSIGNED DEFAULT NULL,
  `approver_role_id` INT UNSIGNED DEFAULT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `level_info` VARCHAR(50) DEFAULT '1 of 1',
  `subject` VARCHAR(255) NOT NULL,
  `status` ENUM('Pending approval', 'Approved', 'Rejected', 'Info requested') DEFAULT 'Pending approval',
  `due_status` ENUM('on time', 'escalated', 'reminder sent') DEFAULT 'on time',
  `comments` TEXT DEFAULT NULL,
  `action_taken_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_appr_requester` FOREIGN KEY (`requester_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_appr_approver` FOREIGN KEY (`approver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_appr_role` FOREIGN KEY (`approver_role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_appr_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 6. Material Movements (Inward / Outward) & Items
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `material_movements`;
CREATE TABLE `material_movements` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `movement_type` ENUM('inward', 'outward') NOT NULL,
  `pass_code` VARCHAR(50) UNIQUE NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `dept_id` INT UNSIGNED DEFAULT NULL,
  `requester_user_id` INT UNSIGNED DEFAULT NULL,
  `vendor_supplier_name` VARCHAR(150) NOT NULL,
  `dest_or_source_address` VARCHAR(255) DEFAULT NULL,
  `reference_doc` VARCHAR(100) DEFAULT NULL,
  `vehicle_number` VARCHAR(50) DEFAULT NULL,
  `kind` ENUM('Returnable', 'Non-returnable') DEFAULT 'Non-returnable',
  `expected_return_date` DATE DEFAULT NULL,
  `status` ENUM('Pending approval', 'Approved', 'Received', 'Received – shortage', 'Received – damage', 'Released', 'Overdue', 'Closed') DEFAULT 'Pending approval',
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_mat_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mat_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mat_requester` FOREIGN KEY (`requester_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mat_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `material_items`;
CREATE TABLE `material_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `movement_id` INT UNSIGNED NOT NULL,
  `item_description` VARCHAR(255) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `unit` VARCHAR(20) DEFAULT 'nos',
  `serial_number` VARCHAR(100) DEFAULT NULL,
  `item_condition` ENUM('New', 'Good', 'Needs repair', 'Scrap') DEFAULT 'Good',
  `received_quantity` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_mat_items_mov` FOREIGN KEY (`movement_id`) REFERENCES `material_movements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 7. Returnable Material Reconciliation
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `material_returns`;
CREATE TABLE `material_returns` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `movement_id` INT UNSIGNED NOT NULL,
  `material_item_id` INT UNSIGNED DEFAULT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `issued_qty` DECIMAL(10,2) NOT NULL,
  `returned_qty` DECIMAL(10,2) DEFAULT 0.00,
  `unit` VARCHAR(20) DEFAULT 'nos',
  `due_date` DATE NOT NULL,
  `owner_user_id` INT UNSIGNED DEFAULT NULL,
  `owner_label` VARCHAR(100) DEFAULT NULL,
  `state` ENUM('Open', 'Partial', 'Closed', 'Overdue') DEFAULT 'Open',
  `last_returned_date` DATE DEFAULT NULL,
  `remarks` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_returns_mov` FOREIGN KEY (`movement_id`) REFERENCES `material_movements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_returns_item` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_returns_owner` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 8. Contractor Workforce Management
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `contractors`;
CREATE TABLE `contractors` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_name` VARCHAR(150) NOT NULL,
  `representative_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `total_workers` INT UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `contractor_work_orders`;
CREATE TABLE `contractor_work_orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `contractor_id` INT UNSIGNED NOT NULL,
  `work_order_no` VARCHAR(50) NOT NULL,
  `project_name` VARCHAR(150) NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `dept_id` INT UNSIGNED DEFAULT NULL,
  `valid_from` DATE NOT NULL,
  `valid_to` DATE NOT NULL,
  `doc_status` ENUM('Valid', 'Expiring in 20 days', 'Expired') DEFAULT 'Valid',
  `induction_status` ENUM('Complete', 'Pending', 'Partial') DEFAULT 'Complete',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_wo_contractor` FOREIGN KEY (`contractor_id`) REFERENCES `contractors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wo_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wo_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `contractor_workers`;
CREATE TABLE `contractor_workers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `contractor_id` INT UNSIGNED NOT NULL,
  `work_order_id` INT UNSIGNED DEFAULT NULL,
  `worker_name` VARCHAR(100) NOT NULL,
  `id_proof_masked` VARCHAR(50) DEFAULT NULL,
  `trade` VARCHAR(50) DEFAULT NULL,
  `medical_valid_until` DATE DEFAULT NULL,
  `status` ENUM('Inside', 'Outside', 'Blocked') DEFAULT 'Outside',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_workers_contractor` FOREIGN KEY (`contractor_id`) REFERENCES `contractors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_workers_wo` FOREIGN KEY (`work_order_id`) REFERENCES `contractor_work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 9. Safety, PPE & Restricted Areas
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `safety_inductions`;
CREATE TABLE `safety_inductions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `entity_type` ENUM('visitor', 'contractor_worker') NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `induction_date` DATE NOT NULL,
  `valid_until` DATE NOT NULL,
  `ppe_helmet` TINYINT(1) DEFAULT 0,
  `ppe_shoes` TINYINT(1) DEFAULT 0,
  `ppe_vest` TINYINT(1) DEFAULT 0,
  `ppe_goggles` TINYINT(1) DEFAULT 0,
  `acknowledged_by` VARCHAR(100) DEFAULT NULL,
  `officer_user_id` INT UNSIGNED DEFAULT NULL,
  `status` ENUM('Passed', 'Pending', 'Expired') DEFAULT 'Passed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_safety_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_safety_officer` FOREIGN KEY (`officer_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `restricted_areas`;
CREATE TABLE `restricted_areas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT UNSIGNED NOT NULL,
  `area_name` VARCHAR(100) NOT NULL,
  `requires_special_permit` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_restricted_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 10. Vehicles & Parking Management
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE `vehicles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `registration_number` VARCHAR(30) UNIQUE NOT NULL,
  `vehicle_type` ENUM('Truck', 'Truck (LCV)', 'Tempo', 'Car', 'Two-wheeler', 'Other') NOT NULL,
  `driver_name` VARCHAR(100) DEFAULT NULL,
  `driver_phone` VARCHAR(30) DEFAULT NULL,
  `permit_status` ENUM('Valid', 'Expired', 'Blocked') DEFAULT 'Valid',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `vehicle_logs`;
CREATE TABLE `vehicle_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `site_id` INT UNSIGNED NOT NULL,
  `gate_id` INT UNSIGNED NOT NULL,
  `linked_ref` VARCHAR(50) DEFAULT NULL,
  `parking_bay` VARCHAR(50) DEFAULT NULL,
  `entry_time` DATETIME NOT NULL,
  `exit_time` DATETIME DEFAULT NULL,
  `guard_user_id` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_vlog_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vlog_site` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vlog_gate` FOREIGN KEY (`gate_id`) REFERENCES `gates` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_vlog_guard` FOREIGN KEY (`guard_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 11. Watchlist & Restricted Visitor Alerts
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `watchlist`;
CREATE TABLE `watchlist` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `person_name` VARCHAR(100) NOT NULL,
  `company_name` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `reason` TEXT NOT NULL,
  `action_to_take` ENUM('Warn only', 'Deny entry') DEFAULT 'Warn only',
  `added_by` VARCHAR(100) DEFAULT 'Plant Head',
  `added_date` DATE NOT NULL,
  `expires_date` DATE DEFAULT NULL,
  `status` ENUM('Active', 'Expired', 'Revoked') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_watchlist_name` (`person_name`)
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------
-- 12. Notifications & System Audit Trail
-- ------------------------------------------------------------------------

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `channel` ENUM('in-app', 'email', 'sms') DEFAULT 'in-app',
  `link_url` VARCHAR(255) DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `actor_id` INT UNSIGNED DEFAULT NULL,
  `actor_name` VARCHAR(100) NOT NULL,
  `actor_role` VARCHAR(50) NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `record_ref` VARCHAR(50) NOT NULL,
  `reason` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_audit_created` (`created_at`),
  CONSTRAINT `fk_audit_actor` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
