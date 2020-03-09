<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\OAuth;

// Alias the League Google OAuth2 provider class
use League\OAuth2\Client\Provider\Google;

// Load Composer's autoloader
require LC_MAIL_SMTP_PATH . 'vendor/autoload.php';


/**
 * The admin-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-facing stylesheet and JavaScript.
 *
 * @package    Lc_Mail_Smtp
 * @subpackage Lc_Mail_Smtp/admin
 * @author     Liquid Church <webmaster@liquidchurch.com>
 */
class Lc_Mail_Smtp_WP_Mail_Actions
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
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Override wp_mail function
     *
     * @param $args
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function process_wp_mail($args)
    {
        $receiver = '';

        $resend_mail_log_id = isset($args['resend_mail']) ? $args['resend_mail'] : '';

        if (isset($args['to'])) {
            $to = $receiver = $args['to'];
        }

        if (!is_array($to)) {
            $to = explode(',', $args['to']);
        }

        if (isset($args['subject'])) {
            $subject = $args['subject'];
        }

        if (isset($args['message'])) {
            $message = $args['message'];
        }

        if (isset($args['headers'])) {
            $headers = $args['headers'];
        }

        if (isset($args['attachments'])) {
            $attachments = $args['attachments'];
        }

        if (!is_array($attachments)) {
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
        }

        $toContentType = 'text/html';

        // Override SMTP Settings
        //Create a new PHPMailer instance
        $phpMailer = new PHPMailer(true);

        //Tell PHPMailer to use SMTP
        $phpMailer->isSMTP();

        //Enable SMTP debugging
        // 0 = off (for production use) //default
        // 1 = client messages
        // 2 = client and server messages
//        $phpMailer->SMTPDebug = 2;

        //Set the hostname of the mail server
        $phpMailer->Host = 'smtp.gmail.com';

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $phpMailer->Port = 587;

        //Set the encryption system to use - ssl (deprecated) or tls
        $phpMailer->SMTPSecure = 'tls';

        //Whether to use SMTP authentication
        $phpMailer->SMTPAuth = true;

        //Set AuthType to use XOAUTH2
        $phpMailer->AuthType = 'XOAUTH2';

        //Fill in authentication details here
        //Either the gmail account owner, or the user that gave consent
        $fromEmail = get_option('lc_mail_smtp_settings_from_email');
        $fromEmailName = get_option('lc_mail_smtp_settings_from_name');
        $clientId = get_option('lc_mail_smtp_settings_client_id');
        $clientSecret = get_option('lc_mail_smtp_settings_client_secret');

        //Obtained by configuring and running get_oauth_token.php
        //after setting up an app in Google Developer Console.
        $refreshToken = get_option('lc_mail_smtp_google_refresh_token');

        //Create a new OAuth2 provider instance
        $provider = new Google(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
            ]
        );

        //Pass the OAuth provider instance to PHPMailer
        $phpMailer->setOAuth(
            new OAuth(
                [
                    'provider' => $provider,
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'refreshToken' => $refreshToken,
                    'userName' => $fromEmail,
                ]
            )
        );

        //Set who the message is to be sent from
        //For gmail, this generally needs to be the same as the user you logged in as
        $phpMailer->setFrom($fromEmail, $fromEmailName);

        if (!empty($to)) {
            foreach ($to as $ke => $toEmail) {
                //Set who the message is to be sent to
                $phpMailer->addAddress($toEmail);
            }
        }

        //Set the subject line
        $phpMailer->Subject = $subject;

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->Body = $message;

        //Replace the plain text body with one created manually
        $phpMailer->AltBody = '';

        $phpMailer->ContentType = $toContentType;

        // Set whether it's plaintext, depending on $content_type
        if ('text/html' == $toContentType) {
            $phpMailer->isHTML(true);
        }

        // If Gravity Form submitted then use Message-ID
        if (isset($_POST['gform_submit'])) {

            $gravity_Form_ID = 'gravity_form_id_' . $_POST['gform_submit'];

            $message_ID = $this->generate_message_ID($gravity_Form_ID);

            $phpMailer->MessageID = $message_ID;
        }

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                try {
                    $phpMailer->addAttachment($attachment);
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        // Send!
        try {

            if ($phpMailer->send()) {

                if ($resend_mail_log_id) {
                    $this->save_email_log($receiver, $subject, $message, $attachments, false, '', true, $resend_mail_log_id);
                } else {
                    $this->save_email_log($receiver, $subject, $message, $attachments);
                }

                return true;

            } else {

                $this->save_email_log($receiver, $subject, $message, $attachments, true, $e->getMessage(), true, $resend_mail_log_id);

                return false;
            }

        } catch (Exception $e) {

            $this->save_email_log($receiver, $subject, $message, $attachments, true, $e->getMessage(), true, $resend_mail_log_id);

            return false;
        }
    }

    /**
     * Generate a unique Message-ID by gravity form ID and date wise
     * Based on RFC 2822 and RFC 5322 standard
     *
     * @return string
     */
    private function generate_message_ID($gravity_Form_ID)
    {
        return sprintf(
            "<%s.%s@%s>",
            base_convert(bin2hex($gravity_Form_ID), 10, 18),
            bin2hex(date('Y-m-d')),
            $_SERVER['SERVER_NAME']
        );
    }

    /**
     * @param $receiver
     * @param string $subject
     * @param string $messageBody
     * @param array $attachments
     * @param bool $error
     * @param null $errorData
     * @param bool $update
     * @param null $id
     */
    private function save_email_log($receiver, $subject = '', $messageBody = '', $attachments = [], $error = false, $errorData = null, $update = false, $id = null)
    {
        global $wpdb;

        if ($update) {

            $wpdb->update($wpdb->prefix . 'lc_mail_smtp_logs', [
                'receiver' => is_array($receiver) ? implode(',', $receiver) : $receiver,
                'subject' => $subject,
                'message' => $messageBody,
                'attachments' => json_encode($attachments),
                'is_error' => $error,
                'error' => $errorData,
                'date_time' => date('Y-m-d H:i:s'),
            ], ['id' => $id]);

        } else {

            $wpdb->insert($wpdb->prefix . 'lc_mail_smtp_logs', [
                'receiver' => is_array($receiver) ? implode(',', $receiver) : $receiver,
                'subject' => $subject,
                'message' => $messageBody,
                'attachments' => json_encode($attachments),
                'is_error' => $error,
                'error' => $errorData,
                'date_time' => date('Y-m-d H:i:s'),
            ]);

        }
    }

}
