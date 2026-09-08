<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$db_prefix = 'psac_';
/* ---------------------------------------------------------- NEW  ---------------------------------------------------------------------------------- */
defined('USERS_ACTIVITY_LOG') 					OR define('USERS_ACTIVITY_LOG', $db_prefix.'activity_log');
defined('ADMIN_PAGE_MENU') 						OR define('ADMIN_PAGE_MENU', 	$db_prefix.'admin_page_menu');
defined('ADMINISTRATOR')				  		OR define('ADMINISTRATOR', 		$db_prefix.'administrator');
defined('ADMINISTRATOR_GROUP') 			  		OR define('ADMINISTRATOR_GROUP',$db_prefix.'administrator_group');
defined('ADMINISTRATOR_GROUP_PERMISSION') 		OR define('ADMINISTRATOR_GROUP_PERMISSION', $db_prefix.'administrator_group_permission');
defined('ADVANCEFISHERMAN') 					OR define('ADVANCEFISHERMAN', 	$db_prefix.'advancefisherman');
defined('BUSINESS_DETAIL') 						OR define('BUSINESS_DETAIL', 	$db_prefix.'business_detail');
defined('CASH_DEPOSITED_PAYMENT') 				OR define('CASH_DEPOSITED_PAYMENT', $db_prefix.'cash_deposited_payment');
defined('DAILYTOLL') 							OR define('DAILYTOLL', 			$db_prefix.'dailytoll');
defined('DAILYTOLLINFO') 						OR define('DAILYTOLLINFO', 		$db_prefix.'dailytollinfo');

defined('DATABASE_BACKUP') 						OR define('DATABASE_BACKUP', 	$db_prefix.'database_backup');
defined('DATABASE_BACKUP_TABLES') 				OR define('DATABASE_BACKUP_TABLES', $db_prefix.'database_backup_tables');
defined('DATABASE_BACKUP_YEARLY')				OR define('DATABASE_BACKUP_YEARLY', $db_prefix.'database_backup_yearly');
defined('DATABASE_SETTING') 					OR define('DATABASE_SETTING', 	$db_prefix.'database_setting');


defined('FISHERMAN') 							OR define('FISHERMAN', 			$db_prefix.'fisherman');
defined('FISHINGPOINTS') 						OR define('FISHINGPOINTS', 		$db_prefix.'fishingpoints');
defined('INWARD') 								OR define('INWARD', 			$db_prefix.'inward');
defined('INWARDNAV') 							OR define('INWARDNAV', 			$db_prefix.'inwardnav');
defined('INWARDRETURNS') 						OR define('INWARDRETURNS', 		$db_prefix.'inwardreturns');
defined('INWARDRETURNSNAV') 					OR define('INWARDRETURNSNAV', 	$db_prefix.'inwardreturnsnav');

defined('PRODUCT') 								OR define('PRODUCT', 			$db_prefix.'product');
defined('PRODUCT_TYPE') 						OR define('PRODUCT_TYPE', 		$db_prefix.'product_type');
defined('PRODUCT_CATEGORY') 					OR define('PRODUCT_CATEGORY', 	$db_prefix.'product_category');
defined('PRODUCT_OUTWARD') 						OR define('PRODUCT_OUTWARD', 	$db_prefix.'product_outward');
defined('PRODUCT_OUTWARD_ITEM') 				OR define('PRODUCT_OUTWARD_ITEM', $db_prefix.'product_outward_item');
defined('PRODUCT_OUTWARD_ITEM_RETURN') 			OR define('PRODUCT_OUTWARD_ITEM_RETURN', $db_prefix.'product_outward_item_return');
defined('PRODUCT_INWARD') 						OR define('PRODUCT_INWARD', 	$db_prefix.'product_inward');
defined('PRODUCT_INWARD_RETURN') 				OR define('PRODUCT_INWARD_RETURN', $db_prefix.'product_inward_return');

defined('JAALPRODUCT') 							OR define('JAALPRODUCT', 		$db_prefix.'jaalproduct');
defined('MAINGROUP_TYPE') 						OR define('MAINGROUP_TYPE', 	$db_prefix.'maingroup_type');
defined('MAINGROUP') 							OR define('MAINGROUP', 			$db_prefix.'maingroup');
defined('NAV') 									OR define('NAV', 				$db_prefix.'nav');
defined('OUTWARD') 								OR define('OUTWARD', 			$db_prefix.'outward');
defined('OUTWARDITEM') 							OR define('OUTWARDITEM', 		$db_prefix.'outwarditem');
defined('OUTWARDNAV') 							OR define('OUTWARDNAV', 		$db_prefix.'outwardnav');
defined('OUTWARDNAVITEM') 						OR define('OUTWARDNAVITEM', 	$db_prefix.'outwardnavitem');
defined('SECONDARYFISHERMAN') 					OR define('SECONDARYFISHERMAN', $db_prefix.'secondaryfisherman');
defined('SETTINGS') 							OR define('SETTINGS', 			$db_prefix.'settings');
defined('USER') 								OR define('USER', 				$db_prefix.'user');
defined('WAGES') 								OR define('WAGES', 				$db_prefix.'wages');
defined('WAGESITEM') 							OR define('WAGESITEM', 			$db_prefix.'wagesitem');
defined('WAGES_ADVANCE') 						OR define('WAGES_ADVANCE', 		$db_prefix.'wages_advance');
defined('TRANSFERRED_LIABILITY') 				OR define('TRANSFERRED_LIABILITY', $db_prefix.'transferred_liability');
defined('LIABILITY_DEDUCTION') 					OR define('LIABILITY_DEDUCTION', $db_prefix.'liability_deduction');


