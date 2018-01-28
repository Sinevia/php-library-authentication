<?php
// ========================================================================= //
// SINEVIA CONFIDENTIAL                                  http://sinevia.com  //
// ------------------------------------------------------------------------- //
// COPYRIGHT (c) 2008-2018 Sinevia Ltd                   All rights resrved! //
// ------------------------------------------------------------------------- //
// LICENCE: All information contained herein is, and remains, property of    //
// Sinevia Ltd at all times.  Any intellectual and technical concepts        //
// are proprietary to Sinevia Ltd and may be covered by existing patents,    //
// patents in process, and are protected by trade secret or copyright law.   //
// Dissemination or reproduction of this information is strictly forbidden   //
// unless prior written permission is obtained from Sinevia Ltd per domain.  //
//===========================================================================//

namespace Sinevia;

/**
 * This class contains useful utility functions for authentication
 */
class AuthenticationUtils {

    private static $separator = 'b';

    public static function equals($password, $hash) {
        $parts = explode(self::$separator, $hash);
        if (isset($parts[1]) == false) {
            return false;
        }
        if (self::passwordHash($password, $parts[0]) == $parts[1]) {
            return true;
        }
        return false;
    }

    public static function hash($password) {
        $salt = md5(self::randomSalt(20));
        $salt = str_replace(self::$separator, 'd', $salt);
        $passwordHash = self::passwordHash($password, $salt);
        return $salt . self::$separator . $passwordHash;
    }

    private static function passwordHash($password, $salt) {
        $password = base64_encode($password);
        $password = md5($password) . md5($salt) . sha1($password);
        $password = md5(sha1($salt . $password));
        $password = md5(base64_encode($password));
        $password = str_replace(self::$separator, 'd', $password);
        return substr($password, 0, 7);
    }

    /**
     * Returns a random password.
     * 
     * This method returns a randomly generated password of specified length
     * and specified set of characters.
     * <code>
     * $new_password = AuthUtils::randomPassword();
     * </code>
     * @param $length int Integer specifying the desired returned length
     * @param $string String A string with the allowed characters
     * @return bool true on success, false otherwise
     */
    public static function randomPassword($length = 8, $string = "ABCDFGHIJKLMNOPQRSTVWXYZabcdefghijklmnopqrstuvwxyz1234567890") {
        return self::stringRandom($length, $string);
    }

    /**
     * Returns a random salt.
     * 
     * This method returns a randomly generated salt of specified length
     * and specified set of characters.
     * <code>
     * $new_password = AuthUtils::randomPassword();
     * </code>
     * @param $length int Integer specifying the desired returned length
     * @param $string String A string with the allowed characters
     * @return bool true on success, false otherwise
     */
    public static function randomSalt($length = 18, $string = "!@#$%^&*()-{}[]:;/?>.,<_ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890") {
        return self::stringRandom($length, $string);
    }

    /**
     * Returns a random string.
     * 
     * The returned string can be of specified length and specified 
     * allowed characters.
     * <code>
     * $string = str_random(10,"");
     * // $result is true
     * </code>
     * @param $length int Integer specifying the desired returned length
     * @param $string String A string with the allowed characters
     * @return bool true on success, false otherwise
     */
    private static function stringRandom($length = 8, $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890") {
        $chars = str_split($string);
        shuffle($chars);
        shuffle($chars);
        return implode("", array_splice($chars, 0, $length));
    }

    /**
     * XOR Encodes a String
     * 
     * Encodes a String with another key String using the
     * XOR encryption.
     * @param String the String to encode
     * @param String the key String
     * @return String the XOR encoded String
     */
    public static function xorEncode($string, $key) {
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
    public static function xorDecode($encstring, $key) {
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

//===========================================================================//
// CLASS: Auth                                                               //
//============================== END OF CLASS ===============================//
