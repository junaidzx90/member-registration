<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Member_Registration
 * @subpackage Member_Registration/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Member_Registration
 * @subpackage Member_Registration/includes
 * @author     Developer Junayed <admin@easeare.com>
 */
class Member_Registration_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
		$member_records = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}member_records` (
			`ID` INT NOT NULL AUTO_INCREMENT,
			`fname` VARCHAR(55) NOT NULL,
			`lname` VARCHAR(55) NOT NULL,
			`email` VARCHAR(255) NOT NULL,
			`phone` VARCHAR(55) NOT NULL,
			`data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`ID`)) ENGINE = InnoDB";
		dbDelta($member_records);
	}

}
