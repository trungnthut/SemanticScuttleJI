<?php
/**
 * Configuration for SemanticScuttle.
 *
 * Copy this file to config.inc.php and adjust it.
 *
 * See config.default.inc.php for more options.
 */


/**
 * The name of this site.
 *
 * @var string
 */
$sitename = 'Chia se link';

/**
 * The welcome message on the homepage.
 *
 * @var string
 */
$welcomeMessage = 'Welcome to SemanticScuttle! Social bookmarking for small communities.';

/**
 * Translation from locales/ folder.
 *
 * Examples: de_DE, en_GB, fr_FR
 *
 * @var string
 */
$locale = 'en_GB';

/**
 * Use clean urls without .php filenames.
 * Requires mod_rewrite (for Apache) to be active.
 *
 * @var boolean
 */
$cleanurls = false;

/**
 * Show debug messages.
 * This setting is recommended when setting up SemanticScuttle,
 * and when hacking on it.
 *
 * @var boolean
 */
$debugMode = false;


/***************************************************
 * Database configuration
 */

/**
 * Database driver
 *
 * available:
 * mysql4, mysqli, mysql, oracle, postgres, sqlite, db2, firebird,
 * mssql, mssq-odbc
 *
 * @var string
 */
$dbtype = 'mysql4';
/**
 * Database username
 *
 * @var string
 */
$dbuser = 'root';

/**
 * Database password
 *
 * @var string
 */
$dbpass = '123456';

/**
 * Name of database
 *
 * @var string
 */
$dbname = 'doclib';

/**
 * Database hostname/IP
 *
 * @var string
 */
$dbhost = '127.0.0.1';

/**
 * Database table name prefix.
 * Do not use "-" since this is badly handled by MySQL.
 *
 * @var string
 */
$tableprefix = 'jos_sc_';


/***************************************************
 * Users
 */

/**
 * Contact address for the site administrator.
 * Used as the FROM address in password retrieval e-mails.
 *
 * @var string
 */
$adminemail = 'admin@example.org';

/**
 * Array of user names who have admin rights
 *
 * Example:
 * <code>
 * $admin_users = array('adminnickname', 'user1nick', 'user2nick');
 * </code>
 *
 * @var array
 */
$admin_users = array();


/***************************************************
 * Bookmarks 
 */

/**
 * Default privacy setting for bookmarks.
 * 0 - Public
 * 1 - Shared with Watchlist
 * 2 - Private
 *
 * @var integer 
 */
$defaults['privacy'] = 0;


/**
* You have completed the basic configuration!
* More options can be found in config.default.php.
*/

$serviceoverrides['User'] = 'SemanticScuttle_Service_JoomlaUser';

/****************************
 * External user authentication
 */

/**
 * Type of external authentication via PEAR Auth
 * To use this, you also need to set
 * $serviceoverrides['User'] = 'SemanticScuttle_Service_AuthUser';
 *
 * @link http://pear.php.net/manual/en/package.authentication.auth.intro-storage.php
 *
 * @var string
 */
$authType = "Custom";

/**
 * Options for external authentication via PEAR Auth
 *
 * @link http://pear.php.net/manual/en/package.authentication.auth.intro.php
 *
 * @var array
 */
$authOptions = array(
//    "dsn" => "mysql://root:123456@127.0.0.1/doclib2",
    "table" => "jos_users"
);

/**
 * Enable debugging for PEAR Authentication
 *
 * @var boolean
 */
$authDebug = false;

$enableRegistration = false;

/**** joomla integration **/
$joomlaHome = array (
    'url' => "http://localhost:8080/dl2",
    'title' => 'Joomla title'
);
?>
