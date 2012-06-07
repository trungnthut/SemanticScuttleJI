<?php

/**
 * SemanticScuttle - your social bookmark manager.
 *
 * PHP version 5.
 *
 * @category Bookmarking
 * @package  SemanticScuttle
 * @author   Benjamin Huynh-Kim-Bang <mensonge@users.sourceforge.net>
 * @author   Christian Weiske <cweiske@cweiske.de>
 * @author   Eric Dane <ericdane@users.sourceforge.net>
 * @license  GPL http://www.gnu.org/licenses/gpl.html
 * @link     http://sourceforge.net/projects/semanticscuttle
 */
require_once 'Auth.php';
require_once 'SemanticScuttle/Service/User.php';

/**
 * SemanticScuttle extendet user management service utilizing
 * the PEAR Auth package to enable authentication against
 * different services, i.e. LDAP or other databases.
 *
 * Requires the Log packages for debugging purposes.
 *
 * @category Bookmarking
 * @package  SemanticScuttle
 * @author   Christian Weiske <cweiske@cweiske.de>
 * @license  GPL http://www.gnu.org/licenses/gpl.html
 * @link     http://sourceforge.net/projects/semanticscuttle
 */
class SemanticScuttle_Service_JoomlaUser extends SemanticScuttle_Service_User {

    /**
     * PEAR Auth instance
     *
     * @var Auth
     */
    protected $auth = null;

    /**
     * If we want to debug authentication process
     *
     * @var boolean
     */
    protected $authdebug = false;

    /**
     * Authentication type (i.e. LDAP)
     *
     * @var string
     *
     * @link http://pear.php.net/manual/en/package.authentication.auth.intro-storage.php
     */
    var $authtype = null;

    /**
     * Authentication options
     *
     * @var array
     *
     * @link http://pear.php.net/manual/en/package.authentication.auth.intro.php
     */
    var $authoptions = null;
    protected $juser_tbname = "jos_users";

    /**
     * Returns the single service instance
     *
     * @param sql_db $db Database object
     *
     * @return SemanticScuttle_Service_AuthUser
     */
    public static function getInstance($db) {
        static $instance;
        if (!isset($instance)) {
            $instance = new self($db);
        }
        return $instance;
    }

    /**
     * Create new instance
     *
     * @var sql_db $db Database object
     */
    protected function __construct($db) {
        parent::__construct($db);

        $this->juser_tbname = $GLOBALS['authOptions']['table'];

//        $this->authtype    = $GLOBALS['authType'];
//        $this->authoptions = $GLOBALS['authOptions'];
//        $this->authdebug   = $GLOBALS['authDebug'];
//
//        //FIXME: throw error when no authtype set?
//        if (!$this->authtype) {
//            return;
//        }
//        require_once 'Auth.php';
//        $this->auth = new Auth($this->authtype, $this->authoptions);
//        //FIXME: check if it worked (i.e. db connection)
//        if ($this->authdebug) {
//            require_once 'Log.php';
//            $this->auth->logger = Log::singleton(
//                'display', '', '', array(), PEAR_LOG_DEBUG
//            );
//            $this->auth->enableLogging = true;
//        }
//        $this->auth->setShowLogin(false);
    }

    /**
     * Return current user id based on session or cookie
     *
     * @return mixed Integer user id or boolean false when user
     *               could not be found or is not logged on.
     */
    public function getCurrentUserId() {
//        return 1;
//        if (!$this->auth) {
        return parent::getCurrentUserId();
//        }
//
//        //FIXME: caching?
//        $name = $this->auth->getUsername();
//        if (!$name) {
//            return parent::getCurrentUserId();
//        }
//        return $this->getIdFromUser($name);
    }

    /**
     * Try to authenticate and login a user with
     * username and password.
     *
     * @param string  $username Name of user
     * @param string  $password Password
     * @param boolean $remember If a long-time cookie shall be set
     *
     * @return boolean True if the user could be authenticated,
     *                 false if not.
     */
    public function login($username, $password, $remember = false) {
//        var_dump(__FUNCTION__);
//        var_dump($username);
//        var_dump($password);
//        var_dump($remember);
//        die("so xxx");
//        return true;
//        if (!$this->auth) {
//            return parent::login($username, $password, $remember);
//        }

        /**
         * TODO: thay the buoc nay
         */
        $ok = $this->loginAuth($username, $password);
        if (!$ok) {
            return false;
        }

        //utilize real login method to get longtime cookie support etc.
        $ok = parent::login($username, $password, $remember);
        if ($ok) {
            return $ok;
        }

        //user must have changed password in external auth.
        //we need to update the local database.
        $user = $this->getUserByUsername($username);
        $this->_updateuser(
                $user['uId'], $this->getFieldName('password'), $this->sanitisePassword($password)
        );

        return parent::login($username, $password, $remember);
    }

    /**
     * Uses PEAR's Auth class to authenticate the user against a container.
     * This allows us to use LDAP, a different database or some other
     * external system.
     *
     * @param string $username Username to check
     * @param string $password Password to check
     *
     * @return boolean If the user has been successfully authenticated or not
     */
    public function loginAuth($username, $password) {
        // TODO: thay o day
//        var_dump(__FUNCTION__ . ' ' . $username . ' ' . $password);
//        die();
//        $this->auth->post = array(
//            'username' => $username,
//            'password' => $password,
//        );
//        $this->auth->start();
//
//        if (!$this->auth->checkAuth()) {
//            return false;
//        }

        if (!$this->validateJoomlaAuth($username, $password)) {
            return false;
        }
        //put user in database
        if (!$this->getUserByUsername($username)) {
            $this->addUser(
                    $username, $password, $username . $GLOBALS['authEmailSuffix']
            );
        }

        return true;
    }

    /**
     * Logs the current user out of the system.
     *
     * @return void
     */
    public function logout() {
        parent::logout();

//        if ($this->auth) {
//            $this->auth->logout();
//            $this->auth = null;
//        }
    }

    //!copy from JUserHelper
    /**
     * Returns a salt for the appropriate kind of password encryption.
     * Optionally takes a seed and a plaintext password, to extract the seed
     * of an existing password, or for encryption types that use the plaintext
     * in the generation of the salt.
     *
     * @access public
     * @param string $encryption  The kind of pasword encryption to use.
     * 							Defaults to md5-hex.
     * @param string $seed		The seed to get the salt from (probably a
     * 							previously generated password). Defaults to
     * 							generating a new seed.
     * @param string $plaintext	The plaintext password that we're generating
     * 							a salt for. Defaults to none.
     *
     * @return string  The generated or extracted salt.
     */
    private function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '') {
        // Encrypt the password.
        switch ($encryption) {
            case 'crypt' :
            case 'crypt-des' :
                if ($seed) {
                    return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
                } else {
                    return substr(md5(mt_rand()), 0, 2);
                }
                break;

            case 'crypt-md5' :
                if ($seed) {
                    return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
                } else {
                    return '$1$' . substr(md5(mt_rand()), 0, 8) . '$';
                }
                break;

            case 'crypt-blowfish' :
                if ($seed) {
                    return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
                } else {
                    return '$2$' . substr(md5(mt_rand()), 0, 12) . '$';
                }
                break;

            case 'ssha' :
                if ($seed) {
                    return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
                } else {
                    return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
                }
                break;

            case 'smd5' :
                if ($seed) {
                    return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
                } else {
                    return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
                }
                break;

            case 'aprmd5' :
                /* 64 characters that are valid for APRMD5 passwords. */
                $APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

                if ($seed) {
                    return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
                } else {
                    $salt = '';
                    for ($i = 0; $i < 8; $i++) {
                        $salt .= $APRMD5 {
                                rand(0, 63)
                                };
                    }
                    return $salt;
                }
                break;

            default :
                $salt = '';
                if ($seed) {
                    $salt = $seed;
                }
                return $salt;
                break;
        }
    }

    /**
     * Formats a password using the current encryption.
     *
     * @access	public
     * @param	string	$plaintext	The plaintext password to encrypt.
     * @param	string	$salt		The salt to use to encrypt the password. []
     * 								If not present, a new salt will be
     * 								generated.
     * @param	string	$encryption	The kind of pasword encryption to use.
     * 								Defaults to md5-hex.
     * @param	boolean	$show_encrypt  Some password systems prepend the kind of
     * 								encryption to the crypted password ({SHA},
     * 								etc). Defaults to false.
     *
     * @return string  The encrypted password.
     */
    private function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false) {
        // Get the salt to use.
        $salt = $this->getSalt($encryption, $salt, $plaintext);

        // Encrypt the password.
        switch ($encryption) {
            case 'plain' :
                return $plaintext;

            case 'sha' :
                $encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext));
                return ($show_encrypt) ? '{SHA}' . $encrypted : $encrypted;

            case 'crypt' :
            case 'crypt-des' :
            case 'crypt-md5' :
            case 'crypt-blowfish' :
                return ($show_encrypt ? '{crypt}' : '') . crypt($plaintext, $salt);

            case 'md5-base64' :
                $encrypted = base64_encode(mhash(MHASH_MD5, $plaintext));
                return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;

            case 'ssha' :
                $encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext . $salt) . $salt);
                return ($show_encrypt) ? '{SSHA}' . $encrypted : $encrypted;

            case 'smd5' :
                $encrypted = base64_encode(mhash(MHASH_MD5, $plaintext . $salt) . $salt);
                return ($show_encrypt) ? '{SMD5}' . $encrypted : $encrypted;

            case 'aprmd5' :
                $length = strlen($plaintext);
                $context = $plaintext . '$apr1$' . $salt;
                $binary = $this->_bin(md5($plaintext . $salt . $plaintext));

                for ($i = $length; $i > 0; $i -= 16) {
                    $context .= substr($binary, 0, ($i > 16 ? 16 : $i));
                }
                for ($i = $length; $i > 0; $i >>= 1) {
                    $context .= ($i & 1) ? chr(0) : $plaintext[0];
                }

                $binary = $this->_bin(md5($context));

                for ($i = 0; $i < 1000; $i++) {
                    $new = ($i & 1) ? $plaintext : substr($binary, 0, 16);
                    if ($i % 3) {
                        $new .= $salt;
                    }
                    if ($i % 7) {
                        $new .= $plaintext;
                    }
                    $new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
                    $binary = $this->_bin(md5($new));
                }

                $p = array();
                for ($i = 0; $i < 5; $i++) {
                    $k = $i + 6;
                    $j = $i + 12;
                    if ($j == 16) {
                        $j = 5;
                    }
                    $p[] = $this->_toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
                }

                return '$apr1$' . $salt . '$' . implode('', $p) . $this->_toAPRMD5(ord($binary[11]), 3);

            case 'md5-hex' :
            default :
                $encrypted = ($salt) ? md5($plaintext . $salt) : md5($plaintext);
                return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;
        }
    }

    private function validateJoomlaAuth($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }

        $query = 'SELECT id, password FROM ' . $this->juser_tbname
                . ' WHERE username = "' . $this->db->sql_escape($username) . '"';

        if (!($dbresult = $this->db->sql_query($query))) {
            message_die(
                    GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db
            );
            return false;
        }

        $row = $this->db->sql_fetchrow($dbresult);
        $this->db->sql_freeresult($dbresult);
        if ($row) {
            $jpassword = $row['password'];
            $parts = explode(':', $jpassword);
            $crypt = $parts[0];
            $salt = @$parts[1];
            $testcrypt = $this->getCryptedPassword($password, $salt);
            return ($crypt == $testcrypt);
        }
        return false;
    }

    /**
     * Converts hexadecimal string to binary data.
     *
     * @access private
     * @param string $hex  Hex data.
     * @return string  Binary data.
     * @since 1.5
     */
    private function _bin($hex) {
        $bin = '';
        $length = strlen($hex);
        for ($i = 0; $i < $length; $i += 2) {
            $tmp = sscanf(substr($hex, $i, 2), '%x');
            $bin .= chr(array_shift($tmp));
        }
        return $bin;
    }

    /**
     * Converts to allowed 64 characters for APRMD5 passwords.
     *
     * @access private
     * @param string  $value
     * @param integer $count
     * @return string  $value converted to the 64 MD5 characters.
     * @since 1.5
     */
    private static function _toAPRMD5($value, $count) {
        /* 64 characters that are valid for APRMD5 passwords. */
        $APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $aprmd5 = '';
        $count = abs($count);
        while (--$count) {
            $aprmd5 .= $APRMD5[$value & 0x3f];
            $value >>= 6;
        }
        return $aprmd5;
    }

}

?>