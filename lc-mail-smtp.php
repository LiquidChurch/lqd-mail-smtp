<?php

/**
 *
 * @link              https://github.com/liquidchurch/lqd-mail-smtp/
 * @since             1.0.0
 * @package           Lc_Mail_Smtp
 *
 * @wordpress-plugin
 * Plugin Name:       Liquid Church Mail SMTP
 * Plugin URI:        lqd-mail-smtp
 * Description:       Reconfigures the wp_mail() function to use PHPMailer instead of the default mail() and creates an options page to manage the settings. Logs each email sent by WordPress.
 * Version:           1.0.0
 * Author:            Liquid Church, Liquid Church
 * Author URI:        https://liquidchurch.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lc-mail-smtp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('LC_MAIL_SMTP_VERSION', '1.0.0');
define('LC_MAIL_SMTP_PATH', plugin_dir_path(__FILE__));
define('LC_MAIL_SMTP_URI', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lc-mail-smtp-activator.php
 */
function activate_lc_mail_smtp()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-lc-mail-smtp-activator.php';
    Lc_Mail_Smtp_Activator::activate();

    global $wpdb;

    $table_name = $wpdb->prefix . 'lc_mail_smtp_logs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		    id bigint(20) NOT NULL AUTO_INCREMENT,
            `receiver` varchar(255) NOT NULL,
            `subject` text NOT NULL,
            `message` text NOT NULL,
            `is_error` tinyint(1) NOT NULL DEFAULT '0',
            `error` text NULL,
            `date_time` datetime NOT NULL,
		    PRIMARY KEY  (id)
	) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lc-mail-smtp-deactivator.php
 */
function deactivate_lc_mail_smtp()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-lc-mail-smtp-deactivator.php';
    Lc_Mail_Smtp_Deactivator::deactivate();

    global $wpdb;

    $table_name = $wpdb->prefix . 'lc_mail_smtp_logs';

    $sql = "DROP TABLE IF EXISTS $table_name;";

//    $wpdb->query($sql);
}

register_activation_hook(__FILE__, 'activate_lc_mail_smtp');
register_deactivation_hook(__FILE__, 'deactivate_lc_mail_smtp');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-lc-mail-smtp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lc_mail_smtp()
{
    $plugin = new Lc_Mail_Smtp();
    $plugin->run();
}

run_lc_mail_smtp();
