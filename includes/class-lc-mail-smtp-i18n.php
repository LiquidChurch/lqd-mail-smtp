<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://codesigma.tech
 * @since      1.0.0
 *
 * @package    Lc_Mail_Smtp
 * @subpackage Lc_Mail_Smtp/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Lc_Mail_Smtp
 * @subpackage Lc_Mail_Smtp/includes
 * @author     Codesigma <office@codesigma.tech>
 */
class Lc_Mail_Smtp_i18n
{

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'lc-mail-smtp',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
