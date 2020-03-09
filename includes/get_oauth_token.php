<?php

require '../../../../wp-load.php';

/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5
 * @package PHPMailer
 * @see https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2012 - 2017 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Get an OAuth2 token from an OAuth2 provider.
 * * Install this script on your server so that it's accessible
 * as [https/http]://<yourdomain>/<folder>/get_oauth_token.php
 * e.g.: http://localhost/phpmailer/get_oauth_token.php
 * * Ensure dependencies are installed with 'composer install'
 * * Set up an app in your Google/Yahoo/Microsoft account
 * * Set the script address as the app's redirect URL
 * If no refresh token is obtained when running this file,
 * revoke access to your app and run the script again.
 */


/**
 * Aliases for League Provider Classes
 * Make sure you have added these to your composer.json and run `composer install`
 * Plenty to choose from here:
 * @see http://oauth2-client.thephpleague.com/providers/thirdparty/
 */

// @see https://github.com/thephpleague/oauth2-google
use League\OAuth2\Client\Provider\Google;

require '../vendor/autoload.php';

//These details are obtained by setting up an app in the Google developer console,
//or whichever provider you're using.
$clientId = get_option('lc_mail_smtp_settings_client_id');
$clientSecret = get_option('lc_mail_smtp_settings_client_secret');

//If this automatic URL doesn't work, set it yourself manually to the URL of this script
$redirectUri = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//$redirectUri = 'http://localhost/PHPMailer/redirect';

$params = [
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
    'redirectUri' => $redirectUri,
    'accessType' => 'offline',
];

$provider = new Google($params);
$options = [
    'scope' => [
        'https://mail.google.com/',
    ],
];

if (null === $provider) {
    exit('Provider missing');
}

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl($options);
    update_option('lc_mail_smtp_oauth2state', $provider->getState());
    header('Location: ' . $authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== get_option('lc_mail_smtp_oauth2state'))) {
    update_option('lc_mail_smtp_oauth2state', '');
    exit('Invalid state');
} else {
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken(
        'authorization_code',
        [
            'code' => $_GET['code'],
        ]
    );

    // Use this to interact with an API on the users behalf
    // Use this to get a new access token if the old one expires
    update_option('lc_mail_smtp_google_refresh_token', $token->getRefreshToken());
}
?>
<script type="text/javascript">
  window.close();
</script>

