<?php
// ========================================================================= //
// SINEVIA PUBLIC                                        http://sinevia.com  //
// ------------------------------------------------------------------------- //
// COPYRIGHT (c) 2008-2016 Sinevia Ltd                   All rights reserved //
// ------------------------------------------------------------------------- //
// LICENCE: All information contained herein is, and remains, property of    //
// Sinevia Ltd at all times.  Any intellectual and technical concepts        //
// are proprietary to Sinevia Ltd and may be covered by existing patents,    //
// patents in process, and are protected by trade secret or copyright law.   //
// Dissemination or reproduction of this information is strictly forbidden   //
// unless prior written permission is obtained from Sinevia Ltd per domain.  //
//===========================================================================//

namespace Sinevia;

class Authentication {

    /**
     * @var Authentication
     */
    private static $instance = null;
    
    // Used to strenghten session passport, good idea to change from time to time
    public $password = '%Dgg#OHQq!wrgvp#Haxu$hxPv7(XVu7fAKqZL$NLeh_)AnI97jG7G*EF3aXfZgk1jZKLlDF';

    private function __construct() {
    }

    /**
     * @return Authentication
     */
    public static function getInstance() {
        if (self::$instance === null) {
            if (!isset($_SESSION)) {
                session_start();
            } // Session is needed
            self::$instance = new Authentication;
        }
        return self::$instance;
    }

    /**
     * Sets the namespace and identity per global space
     * <code>
     *     Authentication::setIdentity('UserId', '342', 'Website.Com');
     * </code>
     * @param string $namespace
     * @param string $identity
     * @param string $globalspace
     */
    public function setIdentity($namespace, $identity, $globalspace = '') {
        $namespace = $globalspace.''.$namespace;
        $identity = array("identity" => $identity, "time" => time());
        $encoded_identity = $this->identityEncode($identity);
        $_SESSION[$namespace . "_sinevia_auth_identity"] = $encoded_identity;
        $_SESSION[$namespace . "_sinevia_auth_identity_hash"] = md5($encoded_identity);
    }

    /**
     * Gets the identity per global space
     * <code>
     *     $userId = Authentication::getIdentity('UserId', 'Website.Com');
     * </code>
     * @param string $namespace
     * @param string $globalspace
     * @return string
     */
    public function getIdentity($namespace, $globalspace = '') {
        $namespace = $globalspace.''.$namespace;
        if ($this->identityCheck($namespace) == true) {
            $identity = $this->identityDecode($_SESSION[$namespace . "_sinevia_auth_identity"]);
            return $identity['identity'];
        }
        return null;
    }

    public function emptyIdentity($namespace,$globalspace = '') {
        $namespace = $globalspace.''.$namespace;
        unset($_SESSION[$namespace . "_sinevia_auth_identity"]);
        unset($_SESSION[$namespace . "_sinevia_auth_identity_hash"]);
    }

    private function identityCheck($namespace,$globalspace = '') {
        $namespace = $globalspace.''.$namespace;
        // Is Identity and Hash set?
        if (isset($_SESSION[$namespace . "_sinevia_auth_identity"]) && isset($_SESSION[$namespace . "_sinevia_auth_identity_hash"])) {
            // Do Identity and Hash match?
            if (md5($_SESSION[$namespace . "_sinevia_auth_identity"]) == $_SESSION[$namespace . "_sinevia_auth_identity_hash"]) {
                return true;
            }
        }
        // There is either no passport, or is not valid
        return false;
    }

    /**
     * Validates the issued passport.
     * Checks, the integrity of the passport and the date of validity.
     * todo time check // 5 min.
     * @return void
     */
    private static function passport_validate() {
        // Do LOGIN PASSPORT and HASH match?
        if (md5(self::passport_encode($_SESSION["LOGIN_PASSPORT"])) == $_SESSION["LOGIN_PASSPORT_HASH"]) {
            self::$login_passport = self::passport_decode($_SESSION["LOGIN_PASSPORT"]);
            // TO DO: Is time fine?
            return true;
        }
        return false;
    }

    public function identityEncode($identity, $salt = "") {
        return $this->xorEncode(serialize($identity), $salt . $this->password);
    }

    public function identityDecode($identity, $salt = "") {
        return unserialize($this->xorDecode($identity, $salt . $this->password));
    }

    /**
     * XOR Encodes a String
     * Encodes a String with another key String using the
     * XOR encryption.
     * @param String the String to encode
     * @param String the key String
     * @return String the XOR encoded String
     */
    private function xorEncode($string, $key) {
        for ($i = 0, $j = 0; $i < strlen($string); $i++, $j++) {
            if ($j == strlen($key)) {
                $j = 0;
            }
            $string[$i] = $string[$i] ^ $key[$j];
        }
        return base64_encode($string);
    }

    /**
     * XOR Decodes a String
     *
     * Decodes a XOR encrypted String using the same key String.
     * @param String the String to decode
     * @param String the key String
     * @return String the decoded String
     */
    private function xorDecode($encstring, $key) {
        $string = base64_decode($encstring);
        for ($i = 0, $j = 0; $i < strlen($string); $i++, $j++) {
            if ($j == strlen($key)) {
                $j = 0;
            }
            $string[$i] = $key[$j] ^ $string[$i];
        }
        return $string;
    }

}
