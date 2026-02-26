<?php
// @file: IGKUserAgent.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* auto generate doc.
*/
class IGKUserAgent{
    const REGEX_ANDROID="android";
    const REGEX_ANDROID_BUILDNUMBER="android\s+(?P<version>[0-9\.]+);\s*(?P<model>[\w0-9\.]+)\s+build\/(?P<buildnumber>[a-z0-9\.]+)";
    const REGEX_ANDROID_MODELNUMBER="android\s+(?P<version>[0-9\.]+);\s*(?P<model>[\w0-9\.]+)\s*";
    const REGEX_ANDROID_VERSION="android\s+(?P<version>[0-9\.]+);";
    /**
     * Returns the current HTTP user agent string.
     * @return string The user agent string from the server environment.
     */
    public static function Agent(){
        return igk_server()->HTTP_USER_AGENT;
    }
    /**
     * Checks the Safari version by passing the parsed version parts to a callback.
     * @param callable $callback Callback receiving an array of version number parts.
     * @return mixed The callback return value, or -1 if not a Safari agent.
     */
    public static function CheckSafariVersion($callback){
        $v=self::GetSafariVersion();
        if($v){
            $t=explode(".", $v);
            return $callback($t);
        }
        return -1;
    }
    /**
     * Returns the Android build number from the user agent string.
     * @return string|null The build number, or null if not an Android agent.
     */
    public static function GetAndroidBuildNumber(){
        if(self::IsAndroid()){
            $regex="/".self::REGEX_ANDROID_BUILDNUMBER."/i";
            $tab=array();
            preg_match_all($regex, self::Agent(), $tab);
            return $tab["buildnumber"][0];
        }
        return null;
    }
    /**
     * Returns the Android device model from the user agent string.
     * @return string|null The device model, or null if not an Android agent.
     */
    public static function GetAndroidModel(){
        if(self::IsAndroid()){
            $regex="/".self::REGEX_ANDROID_MODELNUMBER."/i";
            $tab=array();
            preg_match_all($regex, self::Agent(), $tab);
            return $tab["model"][0];
        }
        return null;
    }
    /**
     * Returns the Android OS version from the user agent string.
     * @return string|null The Android version string, or null if not an Android agent.
     */
    public static function GetAndroidVersion(){
        if(self::IsAndroid()){
            $regex="/".self::REGEX_ANDROID_VERSION."/i";
            $tab=array();
            preg_match_all($regex, self::Agent(), $tab);
            return $tab["version"][0];
        }
        return null;
    }
    /**
     * Returns the Chrome browser version from the user agent string.
     * @return string|null The Chrome version string, or null if not a Chrome agent.
     */
    public static function GetChromeVersion(){
        if(self::IsChrome()){
            $v_r="/Chrome\/\s*(?P<version>[0-9\.]+)\s/i";
            $tab=array();
            preg_match_all($v_r, self::Agent(), $tab);
            return $tab["version"][0];
        }
        return null;
    }
    /**
     * Returns the default language from the HTTP Accept-Language header.
     * @return string The detected language code, or the system default language.
     */
    public static function GetDefaultLang(){
        static $deflang=null;
        if($deflang == null){
            $regex="/^(?P<name>\w+)(,*)/i";
            $tab=array();
            $r=igk_server()->HTTP_ACCEPT_LANGUAGE;
            if($r){
                preg_match_all($regex, $r, $tab);
                $deflang=$tab["name"][0];
            }
            $deflang=IGK_DEFAULT_LANG;
        }
        return $deflang;
    }
    /**
     * Returns the Safari browser version from the user agent string.
     * @return string|null The Safari version string, or null if not a Safari agent.
     */
    public static function GetSafariVersion(){
        if(self::IsSafari()){
            $v_r="/Safari\/\s*(?P<version>[0-9\.]+)(\s*)/i";
            $tab=array();
            preg_match_all($v_r, self::Agent(), $tab);
            return $tab["version"][0];
        }
        return null;
    }
    /**
     * Determines whether the current user agent is an Android device.
     * @return int|bool Non-zero if Android is detected, false otherwise.
     */
    public static function IsAndroid(){
        $regex="/".self::REGEX_ANDROID."/i";
        return preg_match($regex, self::Agent());
    }
    /**
     * Determines whether the current user agent is Google Chrome.
     * @return bool True if Chrome is detected, false otherwise.
     */
    public static function IsChrome(){
        if( ($a = self::Agent()) && strstr($a, "Chrome"))
            return true;
        return false;
    }
    /**
     * Determines whether the current user agent is Internet Explorer or Edge (legacy).
     * @return int|bool Non-zero if MSIE, Trident, or Edge is detected.
     */
    public static function IsIE(){
        return ($a = self::Agent()) && preg_match("#(MSIE|Trident/|Edge/)#i", $a);
    }
    /**
     * Determines whether the current user agent is an iOS device.
     * @return bool Always returns false (not yet implemented).
     */
    public static function IsIOS(){
        return false;
    }
    /**
     * Determines whether the current user agent is a mobile device.
     * @return int|bool Non-zero if a mobile device is detected.
     */
    public static function IsMobileDevice(){
        return self::IsAndroid();
    }
    /**
     * Determines whether the current user agent is Mozilla Firefox.
     * @return bool True if Firefox is detected, false otherwise.
     */
    public static function IsMod(){
        if(strstr(self::Agent(), "Firefox"))
            return true;
        return false;
    }
    /**
     * Determines whether the current Safari agent version is older than 600.
     * @return bool True if it is an old Safari version, false otherwise.
     */
    public static function IsOldSafariAgent(){
        $v=IGKUserAgent::CheckSafariVersion(function($t){
            return $t[0] < 600;
        });
        if($v === -1)
            return false;
        return $v;
    }
    /**
     * Determines whether the current user agent is Safari (not Chrome or Firefox).
     * @return bool|string True or non-empty string if Safari is detected.
     */
    public static function IsSafari(){
        return !self::IsChrome() && !self::IsMod() && strstr(self::Agent(), "Safari");
    }
    /**
     * Determines whether the current user agent is an Xbox console.
     * @return int|bool Non-zero if Xbox is detected, false otherwise.
     */
    public static function IsXBox(){
        $regex="/xbox/i";
        return preg_match($regex, self::Agent());
    }
    /**
     * Determines whether the current user agent is an Xbox One console.
     * @return int|bool Non-zero if Xbox One is detected, false otherwise.
     */
    public static function IsXBoxOne(){
        $regex="/xbox one/i";
        return preg_match($regex, self::Agent());
    }
}
