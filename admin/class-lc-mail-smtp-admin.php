<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codesigma.tech
 * @since      1.0.0
 *
 * @package    Lc_Mail_Smtp
 * @subpackage Lc_Mail_Smtp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lc_Mail_Smtp
 * @subpackage Lc_Mail_Smtp/admin
 * @author     Codesigma <office@codesigma.tech>
 */
class Lc_Mail_Smtp_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Option key, and option page slug
     *
     * @var string
     * @since 1.0.0
     */
    public $key = 'lc_mail_smtp_settings_group';

    public $tableName = '';

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        global $wpdb;

        $this->tableName = $wpdb->prefix . "lc_mail_smtp_logs";
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Lc_Mail_Smtp_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Lc_Mail_Smtp_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/lc-mail-smtp-admin.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Lc_Mail_Smtp_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Lc_Mail_Smtp_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/lc-mail-smtp-admin.js', ['jquery'], $this->version, false);
    }

    /**
     * Add menu page, Register meta boxes and notices for admin area.
     */
    public function add_settings_page()
    {
        //register our settings
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_host');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_port');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_username');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_password');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_authorization');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_from_email');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_from_name');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_client_id');
        register_setting('lc_mail_smtp_settings_group', 'lc_mail_smtp_settings_client_secret');

        // hook in our save notices
        add_action('admin_notices', [$this, 'settings_notices'], 10, 2);

        add_menu_page(
            'Liquid Church Mail SMTP Settings',
            'LC Mail SMTP',
            'administrator',
            'lc-mail-smtp',
            [$this, 'admin_option_page_display'],
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiM5ZWEzYTgiIHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCIgdmlld0JveD0iMCAwIDQzIDM0Ij48cGF0aCBkPSJNMC4wMDcsMy41ODVWMjAuNDIxcTAsMy41ODYsMy43NTEsMy41ODVMMjAsMjRWMTlIMzBWMTQuMDE0bDAuOTkxLTFMMzQsMTNWMy41ODVRMzQsMCwzMC4yNDksMEgzLjc1OFEwLjAwNywwLC4wMDcsMy41ODVoMFpNMy41MjQsNi4xNTdhMS40OSwxLjQ5LDAsMCwxLS41MDgtMC45MzUsMS41ODEsMS41ODEsMCwwLDEsLjI3NC0xLjIwOCwxLjQ0OSwxLjQ0OSwwLDAsMSwxLjA5NC0uNjYzLDEuNzU2LDEuNzU2LDAsMCwxLDEuMjUuMzEybDExLjQwOSw3LjcxNkwyOC4zNzQsMy42NjNhMS45NiwxLjk2LDAsMCwxLDEuMjg5LS4zMTIsMS41NDYsMS41NDYsMCwwLDEsMS4wOTQuNjYzLDEuNCwxLjQsMCwwLDEsLjI3MywxLjIwOCwxLjY3LDEuNjcsMCwwLDEtLjU0Ny45MzVMMTcuMDQzLDE3LjIyNVoiLz48cGF0aCBkPSJNMjIsMjhIMzJsLTAuMDA5LDQuNjI0YTEuMTI2LDEuMTI2LDAsMCwwLDEuOTIyLjhsOC4yNS04LjIzNmExLjEyNiwxLjEyNiwwLDAsMCwwLTEuNTk0bC04LjI1LTguMjQxYTEuMTI2LDEuMTI2LDAsMCwwLTEuOTIyLjh2NC44NjZMMjIsMjF2N1oiLz48L3N2Zz4=',
            98
        );
        add_submenu_page(
            'lc-mail-smtp',
            'LC Mail SMTP Logs',
            'LC Mail Log',
            'administrator',
            'lc-mail-smtp-logs',
            [$this, 'admin_option_sub_menu_page_display']
        );
    }

    /**
     * Google Authentication File URI
     * @return string
     */
    private function get_google_auth_URL()
    {
        return LC_MAIL_SMTP_URI . 'includes/get_oauth_token.php';
    }

    /**
     * LC MAIL SMTP Settings FORM Display.
     */
    public function admin_option_page_display()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="options.php">

                <?php settings_fields('lc_mail_smtp_settings_group'); ?>
                <?php do_settings_sections('lc_mail_smtp_settings_group'); ?>

                <table class="form-table lc-mail-smtp-form-table">
                    <tr valign="top">
                        <th scope="row"><label for="lc_mail_smtp_settings_client_id">Client ID</label></th>
                        <td><input type="text" name="lc_mail_smtp_settings_client_id" value="<?php echo esc_attr(get_option('lc_mail_smtp_settings_client_id')); ?>" class="lc-mail-smtp-meta-inputs" required/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="lc_mail_smtp_settings_client_secret">Client Secret Key</label></th>
                        <td><input type="password" name="lc_mail_smtp_settings_client_secret" value="<?php echo esc_attr(get_option('lc_mail_smtp_settings_client_secret')); ?>" class="lc-mail-smtp-meta-inputs" required/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="lc_mail_smtp_settings_from_email">From Email</label></th>
                        <td>
                            <input type="email" name="lc_mail_smtp_settings_from_email" value="<?php echo esc_attr(get_option('lc_mail_smtp_settings_from_email')); ?>" class="lc-mail-smtp-meta-inputs" required/>
                            <hr>
                            <?php if (get_option('lc_mail_smtp_settings_client_id') && get_option('lc_mail_smtp_settings_client_secret')) : ?>
                                <?php if (get_option('lc_mail_smtp_google_refresh_token')) : ?>
                                    <a href="<?php echo esc_url($this->get_google_auth_URL()); ?>" target="_blank" class="button button-primary">
                                        <?php esc_html_e('Reconfigure your Google account', 'wp-mail-smtp'); ?>
                                    </a>
                                    <p class="desc">Click the button above to reconfirm authorization.</p>
                                <?php else : ?>
                                    <a href="<?php echo esc_url($this->get_google_auth_URL()); ?>" target="_blank" class="button button-primary">
                                        <?php esc_html_e('Allow plugin to send emails using your Google account', 'wp-mail-smtp'); ?>
                                    </a>
                                    <p class="desc">Click the button above to confirm authorization.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="lc_mail_smtp_settings_from_name">From Name</label></th>
                        <td><input type="text" name="lc_mail_smtp_settings_from_name" value="<?php echo esc_attr(get_option('lc_mail_smtp_settings_from_name')); ?>" class="lc-mail-smtp-meta-inputs" required/></td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }

    /**
     * LC MAIL SMTP Logs List Display.
     */
    public function admin_option_sub_menu_page_display()
    {
        global $wpdb;

        $this->check_perform_lc_smtp_logs_bulk_actions();

        $query = "select * from " . $this->tableName;

        $total_query = "SELECT COUNT(1) FROM " . $this->tableName;
        $total = $wpdb->get_var($total_query);
        $items_per_page = 20;
        $page = isset($_GET['paged']) ? abs((int)$_GET['paged']) : 1;
        $offset = ($page * $items_per_page) - $items_per_page;
        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'date_time';
        $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
        $search = isset($_GET['s']) ? $_GET['s'] : '';

        if ($search) {
            $latestLogs = $wpdb->get_results($query . " WHERE date_time LIKE '%$search%' OR receiver LIKE '%$search%' OR subject LIKE '%$search%' ORDER BY {$orderby} {$order} LIMIT ${offset}, ${items_per_page}");
        } else {
            $latestLogs = $wpdb->get_results($query . " ORDER BY {$orderby} {$order} LIMIT ${offset}, ${items_per_page}");
        }

        $dateTimeOrder = $orderby == 'date_time' ? $this->get_column_order($order) : 'desc';
        $receiverOrder = $orderby == 'receiver' ? $this->get_column_order($order) : 'desc';
        $subjectOrder = $orderby == 'subject' ? $this->get_column_order($order) : 'desc';

        ?>

        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="get" action="admin.php">
                <input type="hidden" name="page" value="lc-mail-smtp-logs">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('lc-mail-smtp-logs') ?>">
                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
                            <option value="-1" disabled selected>Bulk Actions</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                    </div>
                    <div class="tablenav-pages">
                        <p class="search-box">
                            <label class="screen-reader-text" for="s-search-input">Search:</label>
                            <input type="search" id="s-search-input" name="s" value="">
                            <input type="submit" id="search-submit" class="button" value="Search">
                        </p>
                    </div>
                </div>
                <table class="wp-list-table widefat fixed striped emails">
                    <thead>
                    <tr>
                        <?php ?>
                        <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                        <th class="manage-column column-timestamp"><a href="admin.php?page=lc-mail-smtp-logs&amp;orderby=date_time&amp;order=<?php echo $dateTimeOrder; ?>"><span>Time</span></a></th>
                        <th class="manage-column column-receiver"><a href="admin.php?page=lc-mail-smtp-logs&amp;orderby=receiver&amp;order=<?php echo $receiverOrder; ?>"><span>Receiver</span></a></th>
                        <th class="manage-column column-subject"><a href="admin.php?page=lc-mail-smtp-logs&amp;orderby=subject&amp;order=<?php echo $subjectOrder; ?>"><span>Subject</span></a></th>
                        <th class="manage-column column-error">Error</th>
                    </tr>
                    </thead>

                    <tbody id="the-list">
                    <?php
                    if ($latestLogs) {
                        foreach ($latestLogs as $key => $logData) {
                            echo '<tr>';
                            echo '<th scope="row" class="check-column"><input type="checkbox" name="email[]" value="' . $logData->id . '"></th>';
                            echo '<td class="timestamp column-timestamp">' . $logData->date_time . '</td>';
                            echo '<td class="receiver column-receiver">' . $logData->receiver . '</td>';
                            echo '<td class="subject column-subject">' . $logData->subject . '</td>';
                            echo '<td class="error column-error">';
                            if ($logData->is_error == 1) {
                                echo 'Error: ' . $logData->error;
                                echo '<a href="admin.php?page=lc-mail-smtp-logs&amp;action=resend&amp;nonce=' . wp_create_nonce('lc-mail-smtp-logs') . '&amp;email=' . $logData->id . '" class="button action">Resend</a>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr class="no-items"><td class="colspanchange no-items" colspan="5">No email found.</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </form>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links([
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'end_size' => 2,
                        'mid_size' => 1,
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => ceil($total / $items_per_page),
                        'current' => $page,
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Delete Mail Logs Bulk .
     * Resend mails which are failed.
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function check_perform_lc_smtp_logs_bulk_actions()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'lc-mail-smtp-logs') {

            if (isset($_GET['action'])) {

                if (wp_verify_nonce($_GET['nonce'], $_GET['page'])) {

                    if (isset($_GET['email'])) {

                        global $wpdb;

                        if ($_GET['action'] == 'delete') {

                            $emails = $_GET['email'];

                            $ids = implode(',', array_map('absint', $emails));

                            $wpdb->query("DELETE FROM $this->tableName WHERE id IN($ids)");
                        }

                        if ($_GET['action'] == 'resend') {

                            $id = $_GET['email'];

                            $data = $wpdb->get_results("SELECT * FROM $this->tableName WHERE id = $id", ARRAY_A);

                            if (!empty($data)) {

                                if ($data[0]['is_error']) {

                                    $args = [
                                        'to' => explode(',', $data[0]['receiver']),
                                        'subject' => $data[0]['subject'],
                                        'message' => $data[0]['message'],
                                        'headers' => [],
                                        'attachments' => [],
                                        'resend_mail' => $id,
                                    ];

                                    $sendMail = new Lc_Mail_Smtp_WP_Mail_Actions($this->plugin_name, $this->version);

                                    $sendMail->process_wp_mail($args);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get Column Order
     *
     * @param $order
     * @return string
     */
    private function get_column_order($order)
    {
        if ($order == 'asc') {
            $order = 'desc';
        } elseif ($order == 'desc') {
            $order = 'asc';
        } else {
            $order = 'desc';
        }

        return $order;
    }

    /**
     * Register settings notices for display
     *
     * @param int $object_id Option key
     * @param array $updated Array of updated fields
     *
     * @return void
     * @since  1.0.0
     *
     */
    public function settings_notices()
    {
        $screen = get_current_screen();

        if ($screen->id !== 'toplevel_page_lc-mail-smtp') {
            return;
        }

        if (!isset($_GET['settings-updated'])) {
            return;
        }

        add_settings_error($this->key . '-notices', '', __('Settings updated.', 'lc-mail-smtp'), 'updated');
        settings_errors($this->key . '-notices');
    }
}
