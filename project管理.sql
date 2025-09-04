/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `specification_manager` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `specification_manager`;

CREATE TABLE IF NOT EXISTS `bullet_test_case_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `order_no` int unsigned NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `source_text` longtext COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bullet_test_case_groups_project_id_foreign` (`project_id`),
  CONSTRAINT `bullet_test_case_groups_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bullet_test_case_groups` (`id`, `project_id`, `order_no`, `title`, `source_text`, `created_at`, `updated_at`) VALUES
	(1, 2, 1, '1. 初期表示', '- TC1-1 | 初期表示 | 土壌改良資材マスター存在 | フラグ=1のデータが昇順で表示される\n- TC1-2 | 初期表示 | 作物マスター存在 | 作物名がプルダウンに設定される\n- TC1-3 | 初期表示 | ボカシマスター存在 | 分析値・効率が設定される\n- TC1-4 | 初期表示 | DB.土壌分析値あり | CEC～ホウ素が表示される\n- TC1-5 | 初期表示 | DB.土壌分析値なし | 空白＋警告「土壌分析値を登録してください」が表示される', '2025-09-04 07:02:03', '2025-09-04 07:02:03'),
	(2, 2, 2, '2. 再計算の実行時の判定処理', '- TC2-1 | 必須入力チェック | 作物名未選択 | エラー表示\n- TC2-2 | 必須入力チェック | 面積未入力 | エラー表示\n- TC2-3 | 必須入力チェック | ボカシ施肥量入力あり＋配合比率未入力 | エラー表示\n- TC2-4 | 必須入力チェック | ボカシ施肥量・明細部元肥施肥量いずれも未入力 | エラー表示\n- TC2-5 | ボカシ計算 | 施肥量・配合比率入力あり | 魚粉・油粕・米ぬかの元肥施肥量が算出される\n- TC2-6 | ボカシ計算 | ボカシ分析値=0 | ボカシ成分は空白\n- TC2-7 | ボカシ計算 | ボカシ施肥量入力あり | 圃場施肥量(元肥)が算出される\n- TC2-8 | 明細計算 | 元肥施肥量入力あり | 成分/窒素～ホウ素が算出される\n- TC2-9 | 明細計算 | 分析値=0 | 成分は空白\n- TC2-10 | 明細計算 | 元肥施肥量入力あり | 圃場施肥量(元肥)が算出される\n- TC2-11 | 成分計算 | 入力値あり | 肥料成分量が正しく算出される\n- TC2-12 | 成分計算 | 窒素計算 | 参考成分値=肥料成分量+0.1×(硝酸)²\n- TC2-13 | 成分計算 | リン酸～ホウ素 | 参考成分値=肥料成分量+土壌分析値\n- TC2-14 | 成分計算 | 上限超過 | 赤色表示\n- TC2-15 | 成分計算 | 下限未満 | 青色表示\n- TC2-16 | 保存処理 | 再計算後 | DBにCEC～ホウ素・マンガン～ホウ素が保存される', '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(3, 2, 3, '3. 改良資材押下', '- TC3-1 | 改良資材押下 | - | 管理画面が開く\n- TC3-2 | 改良資材押下 | - | フラグ=1の全件が昇順表示\n- TC3-3 | 改良資材押下 | - | 各元肥施肥量が空白にリセットされる', '2025-09-04 07:03:51', '2025-09-04 07:03:51'),
	(4, 2, 4, '4. 作物押下', '- TC4-1 | 作物押下 | - | 作物マスター管理画面が開く\n- TC4-2 | 作物押下 | - | プルダウン選択が未選択に\n- TC4-3 | 作物押下 | - | 上限・基準・下限が空白', '2025-09-04 07:04:14', '2025-09-04 07:04:14'),
	(5, 2, 5, '5. ボカシ押下', '- TC5-1 | ボカシ押下 | - | ボカシ管理画面が開く', '2025-09-04 07:04:39', '2025-09-04 07:04:39'),
	(6, 2, 6, '6. 作物名プルダウン選択', '- TC6-1 | プルダウン選択 | 作物選択 | 元肥基準/窒素～カリウムが設定される\n- TC6-2 | プルダウン選択 | 作物選択 | Ca=K×6、Mg=Kが算出される\n- TC6-3 | プルダウン選択 | 作物選択 | 上限=基準×1.2、下限=基準×0.8\n- TC6-4 | プルダウン選択 | 上限超過 | 赤色表示\n- TC6-5 | プルダウン選択 | 下限未満 | 青色表示', '2025-09-04 07:05:15', '2025-09-04 07:05:15'),
	(7, 2, 7, '7. 出力押下', '- TC7-1 | 出力押下 | - | 再計算イベントが実施される\n- TC7-2 | 出力押下 | - | Excel帳票が出力される（ボタン非表示）', '2025-09-04 07:05:48', '2025-09-04 07:05:48'),
	(8, 2, 8, '8. 画面を閉じる', '- TC8-1 | 画面を閉じる | - | CEC～ホウ素、マンガン～ホウ素がDBに保存される', '2025-09-04 07:06:14', '2025-09-04 07:06:14');

CREATE TABLE IF NOT EXISTS `bullet_test_case_rows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint unsigned NOT NULL,
  `order_no` int unsigned NOT NULL DEFAULT '1',
  `no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `feature` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `input_condition` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expected` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bullet_test_case_rows_group_id_foreign` (`group_id`),
  CONSTRAINT `bullet_test_case_rows_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `bullet_test_case_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bullet_test_case_rows` (`id`, `group_id`, `order_no`, `no`, `feature`, `input_condition`, `expected`, `is_done`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'TC1-1', '初期表示', '土壌改良資材マスター存在', 'フラグ=1のデータが昇順で表示される', 1, '2025-09-04 07:02:03', '2025-09-04 07:06:23'),
	(2, 1, 2, 'TC1-2', '初期表示', '作物マスター存在', '作物名がプルダウンに設定される', 0, '2025-09-04 07:02:03', '2025-09-04 07:02:03'),
	(3, 1, 3, 'TC1-3', '初期表示', 'ボカシマスター存在', '分析値・効率が設定される', 0, '2025-09-04 07:02:03', '2025-09-04 07:02:03'),
	(4, 1, 4, 'TC1-4', '初期表示', 'DB.土壌分析値あり', 'CEC～ホウ素が表示される', 0, '2025-09-04 07:02:03', '2025-09-04 07:02:03'),
	(5, 1, 5, 'TC1-5', '初期表示', 'DB.土壌分析値なし', '空白＋警告「土壌分析値を登録してください」が表示される', 0, '2025-09-04 07:02:03', '2025-09-04 07:02:03'),
	(6, 2, 1, 'TC2-1', '必須入力チェック', '作物名未選択', 'エラー表示', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(7, 2, 2, 'TC2-2', '必須入力チェック', '面積未入力', 'エラー表示', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(8, 2, 3, 'TC2-3', '必須入力チェック', 'ボカシ施肥量入力あり＋配合比率未入力', 'エラー表示', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(9, 2, 4, 'TC2-4', '必須入力チェック', 'ボカシ施肥量・明細部元肥施肥量いずれも未入力', 'エラー表示', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(10, 2, 5, 'TC2-5', 'ボカシ計算', '施肥量・配合比率入力あり', '魚粉・油粕・米ぬかの元肥施肥量が算出される', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(11, 2, 6, 'TC2-6', 'ボカシ計算', 'ボカシ分析値=0', 'ボカシ成分は空白', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(12, 2, 7, 'TC2-7', 'ボカシ計算', 'ボカシ施肥量入力あり', '圃場施肥量(元肥)が算出される', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(13, 2, 8, 'TC2-8', '明細計算', '元肥施肥量入力あり', '成分/窒素～ホウ素が算出される', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(14, 2, 9, 'TC2-9', '明細計算', '分析値=0', '成分は空白', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(15, 2, 10, 'TC2-10', '明細計算', '元肥施肥量入力あり', '圃場施肥量(元肥)が算出される', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(16, 2, 11, 'TC2-11', '成分計算', '入力値あり', '肥料成分量が正しく算出される', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(17, 2, 12, 'TC2-12', '成分計算', '窒素計算', '参考成分値=肥料成分量+0.1×(硝酸)²', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(18, 2, 13, 'TC2-13', '成分計算', 'リン酸～ホウ素', '参考成分値=肥料成分量+土壌分析値', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(19, 2, 14, 'TC2-14', '成分計算', '上限超過', '赤色表示', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(20, 2, 15, 'TC2-15', '成分計算', '下限未満', '青色表示', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(21, 2, 16, 'TC2-16', '保存処理', '再計算後', 'DBにCEC～ホウ素・マンガン～ホウ素が保存される', 0, '2025-09-04 07:03:08', '2025-09-04 07:03:08'),
	(22, 3, 1, 'TC3-1', '改良資材押下', '-', '管理画面が開く', 0, '2025-09-04 07:03:51', '2025-09-04 07:03:51'),
	(23, 3, 2, 'TC3-2', '改良資材押下', '-', 'フラグ=1の全件が昇順表示', 0, '2025-09-04 07:03:51', '2025-09-04 07:03:51'),
	(24, 3, 3, 'TC3-3', '改良資材押下', '-', '各元肥施肥量が空白にリセットされる', 0, '2025-09-04 07:03:51', '2025-09-04 07:03:51'),
	(25, 4, 1, 'TC4-1', '作物押下', '-', '作物マスター管理画面が開く', 0, '2025-09-04 07:04:14', '2025-09-04 07:04:14'),
	(26, 4, 2, 'TC4-2', '作物押下', '-', 'プルダウン選択が未選択に', 0, '2025-09-04 07:04:14', '2025-09-04 07:04:14'),
	(27, 4, 3, 'TC4-3', '作物押下', '-', '上限・基準・下限が空白', 0, '2025-09-04 07:04:14', '2025-09-04 07:04:14'),
	(28, 5, 1, 'TC5-1', 'ボカシ押下', '-', 'ボカシ管理画面が開く', 0, '2025-09-04 07:04:39', '2025-09-04 07:04:39'),
	(29, 6, 1, 'TC6-1', 'プルダウン選択', '作物選択', '元肥基準/窒素～カリウムが設定される', 0, '2025-09-04 07:05:15', '2025-09-04 07:05:15'),
	(30, 6, 2, 'TC6-2', 'プルダウン選択', '作物選択', 'Ca=K×6、Mg=Kが算出される', 0, '2025-09-04 07:05:15', '2025-09-04 07:05:15'),
	(31, 6, 3, 'TC6-3', 'プルダウン選択', '作物選択', '上限=基準×1.2、下限=基準×0.8', 0, '2025-09-04 07:05:15', '2025-09-04 07:05:15'),
	(32, 6, 4, 'TC6-4', 'プルダウン選択', '上限超過', '赤色表示', 0, '2025-09-04 07:05:15', '2025-09-04 07:05:15'),
	(33, 6, 5, 'TC6-5', 'プルダウン選択', '下限未満', '青色表示', 0, '2025-09-04 07:05:15', '2025-09-04 07:05:15'),
	(34, 7, 1, 'TC7-1', '出力押下', '-', '再計算イベントが実施される', 0, '2025-09-04 07:05:48', '2025-09-04 07:05:48'),
	(35, 7, 2, 'TC7-2', '出力押下', '-', 'Excel帳票が出力される（ボタン非表示）', 0, '2025-09-04 07:05:48', '2025-09-04 07:05:48'),
	(36, 8, 1, 'TC8-1', '画面を閉じる', '-', 'CEC～ホウ素、マンガン～ホウ素がDBに保存される', 0, '2025-09-04 07:06:14', '2025-09-04 07:06:14');

CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `change_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requirement_id` bigint unsigned NOT NULL,
  `old_version_id` bigint unsigned NOT NULL,
  `new_version_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `change_requests_requirement_id_foreign` (`requirement_id`),
  KEY `change_requests_old_version_id_foreign` (`old_version_id`),
  KEY `change_requests_new_version_id_foreign` (`new_version_id`),
  KEY `change_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `change_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `change_requests_new_version_id_foreign` FOREIGN KEY (`new_version_id`) REFERENCES `spec_versions` (`id`),
  CONSTRAINT `change_requests_old_version_id_foreign` FOREIGN KEY (`old_version_id`) REFERENCES `spec_versions` (`id`),
  CONSTRAINT `change_requests_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2025_09_04_071206_create_core_tables', 1),
	(2, '2025_09_04_072050_create_users_table', 1),
	(3, '2025_09_04_072319_create_cache_table', 1),
	(4, '2025_09_04_072422_create_permission_tables', 1),
	(5, '2025_09_04_091340_create_sessions_table', 1),
	(6, '2025_09_04_093046_create_change_requests_table', 2),
	(7, '2025_09_04_093046_create_requirements_table', 3),
	(8, '2025_09_04_093046_create_spec_versions_table', 4),
	(9, '2025_09_04_094658_create_test_steps_table', 4),
	(10, '2025_09_04_094755_create_test_steps_table', 5),
	(11, '2025_09_04_100626_create_test_cases_table', 6),
	(12, '2025_09_04_142517_create_bullet_test_cases_table', 7);

CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(1, 'App\\Models\\User', 2);

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'manage projects', 'web', '2025-09-04 04:11:41', '2025-09-04 04:11:41'),
	(2, 'manage requirements', 'web', '2025-09-04 04:11:41', '2025-09-04 04:11:41'),
	(3, 'manage test cases', 'web', '2025-09-04 04:11:41', '2025-09-04 04:11:41'),
	(4, 'approve changes', 'web', '2025-09-04 04:11:42', '2025-09-04 04:11:42'),
	(5, 'run tests', 'web', '2025-09-04 04:11:42', '2025-09-04 04:11:42');

CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `projects` (`id`, `key`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'whisper', '東広島市：相談業務支援録音アプリ', 'WhisperとOpenAIの文字起こしアプリ', '2025-09-04 04:26:40', '2025-09-04 04:26:40'),
	(2, 'cscustomtable', '七福神の会：施肥設計アプリ', 'WindowsFormアプリケーション\n自作カスタムコントロールのテーブル使用', '2025-09-04 04:29:01', '2025-09-04 04:29:01');

CREATE TABLE IF NOT EXISTS `requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `current_version_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requirements_project_id_foreign` (`project_id`),
  KEY `requirements_current_version_id_foreign` (`current_version_id`),
  CONSTRAINT `requirements_current_version_id_foreign` FOREIGN KEY (`current_version_id`) REFERENCES `spec_versions` (`id`),
  CONSTRAINT `requirements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'web', '2025-09-04 04:11:42', '2025-09-04 04:11:42'),
	(2, 'manager', 'web', '2025-09-04 04:11:42', '2025-09-04 04:11:42'),
	(3, 'tester', 'web', '2025-09-04 04:11:42', '2025-09-04 04:11:42');

CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(1, 2),
	(2, 1),
	(2, 2),
	(3, 1),
	(3, 2),
	(4, 1),
	(4, 2),
	(5, 1),
	(5, 3);

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `payload` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('1LAP9Fugm9rDDfRVvy0z90ziappIguPlNTi3suzA', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiV3M1QjV0ZWhPekprcERxWWtxaXB6bVNqbm5oUmhFNkZzTlI0dlBZOCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wcm9qZWN0cy8yL2J1bGxldC1jYXNlcyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRMQzdWaHR0NjlBOGJXVkZqUzV4TGVlbmpDcDlaNEJLOUowVy5laC50eDFpYmRnWXNNemViNiI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1757001984);

CREATE TABLE IF NOT EXISTS `spec_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requirement_id` bigint unsigned NOT NULL,
  `version` int NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spec_versions_requirement_id_foreign` (`requirement_id`),
  KEY `spec_versions_created_by_foreign` (`created_by`),
  CONSTRAINT `spec_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `spec_versions_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `test_cases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `requirement_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `preconditions` text COLLATE utf8mb4_general_ci,
  `expected_result` text COLLATE utf8mb4_general_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_cases_project_id_foreign` (`project_id`),
  KEY `test_cases_requirement_id_foreign` (`requirement_id`),
  KEY `test_cases_created_by_foreign` (`created_by`),
  CONSTRAINT `test_cases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_cases_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_cases_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `test_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `test_case_id` bigint unsigned NOT NULL,
  `passed` tinyint(1) NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_results_test_case_id_foreign` (`test_case_id`),
  CONSTRAINT `test_results_test_case_id_foreign` FOREIGN KEY (`test_case_id`) REFERENCES `test_cases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `test_steps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `login_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `entry_date` date DEFAULT NULL,
  `exit_date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_code` (`login_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `name`, `login_code`, `password`, `email`, `email_verified_at`, `remember_token`, `is_admin`, `entry_date`, `exit_date`, `note`, `created_at`, `updated_at`) VALUES
	(2, 'Admin', 'hanako', '$2y$12$LC7Vhtt69A8bWVFjS5xLeenjCp9Z4BK9J0W.eh.tx1ibdgYsMzeb6', 'admin@example.com', '2025-09-04 04:12:56', NULL, 1, NULL, NULL, '初期管理者アカウント', '2025-09-04 04:12:56', '2025-09-04 04:12:56');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
