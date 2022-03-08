<?php
// @file: IGKUserAgent.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

class IGKUserAgent{
    const REGEX_ANDROID="android";
    const REGEX_ANDROID_BUILDNUMBER="android\s+(?P<version>[0-9\.]+);\s*(?P<model>[\w0-9\.]+)\s+build\/(?P<buildnumber>[a-z0-9\.]+)";
    const REGEX_ANDROID_MODELNUMBER="android\s+(?P<version>[0-9\.]+);\s*(?P<model>[\w0-9\.]+)\s*";
    const REGEX_ANDROID_VERSION="android\s+(?P<version>[0-9\.]+);";
    ///<summary></summary>
    public static function Agent(){
        return igk_server()->HTTP_USER_AGENT;
    }
    ///<summary></summary>
    ///<param name="callback"></param>
    public static function CheckSafariVersion($callback){
        $v=self::GetSafariVersion();
        if($v){
            $t=explode(".", $v);
            return $callback($t);
        }
        return -1;
    }
    ///<summary></summary>
    public static function GetAndroidBuildNumber(){
        if(self::IsAndroid()){
            $regex="/".self::REGEX_ANDROID_BUILDNUMBER."/i";
            $tab=array();
            preg_match_all($regex, self::Agent(), $tab);
            return $tab["buildnumber"][0];
        }
        return null;
    }
    ///<summary></summary>
    public static function GetAndroidModel(){
        if(self::IsAndroid()){
            $regex="/".self::REGEX_ANDROID_MODELNUMBER."/i";
            $tab=array();
            preg_match_all($regex, self::Agent(), $tab);
            return $tab["model"][0];
        }
        return null;
    }
    ///<summary></summary>
    public static function GetAndroidVersion(){
        if(self::IsAndroid()){
            $regex="/".self::REGEX_ANDROID_VERSION."/i";
            $tab=array();
            preg_match_all($regex, self::Agent(), $tab);
            return $tab["version"][0];
        }
        return null;
    }
    ///<summary></summary>
    public static function GetChromeVersion(){
        if(self::IsChrome()){
            $v_r="/Chrome\/\s*(?P<version>[0-9\.]+)\s/i";
            $tab=array();
            preg_match_all($v_r, self::Agent(), $tab);
            return $tab["version"][0];
        }
        return null;
    }
    ///<summary></summary>
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
    ///<summary></summary>
    public static function GetSafariVersion(){
        if(self::IsSafari()){
            $v_r="/Safari\/\s*(?P<version>[0-9\.]+)(\s*)/i";
            $tab=array();
            preg_match_all($v_r, self::Agent(), $tab);
            return $tab["version"][0];
        }
        return null;
    }
    ///<summary></summary>
    public static function IsAndroid(){
        $regex="/".self::REGEX_ANDROID."/i";
        return preg_match($regex, self::Agent());
    }
    ///<summary></summary>
    public static function IsChrome(){
        if( ($a = self::Agent()) && strstr($a, "Chrome"))
            return true;
        return false;
    }
    ///<summary></summary>
    public static function IsIE(){
        return preg_match("#(MSIE|Trident/|Edge/)#i", self::Agent());
    }
    ///<summary></summary>
    public static function IsIOS(){
        return false;
    }
    ///<summary></summary>
    public static function IsMobileDevice(){
        return self::IsAndroid();
    }
    ///<summary>Is Firefox modzilla</summary>
    public static function IsMod(){
        if(strstr(self::Agent(), "Firefox"))
            return true;
        return false;
    }
    ///<summary>Old safari version</summary>
    public static function IsOldSafariAgent(){
        $v=IGKUserAgent::CheckSafariVersion(function($t){
            return $t[0] < 600;
        });
        if($v === -1)
            return false;
        return $v;
    }
    ///<summary></summary>
    public static function IsSafari(){
        return !self::IsChrome() && !self::IsMod() && strstr(self::Agent(), "Safari");
    }
    ///<summary></summary>
    public static function IsXBox(){
        $regex="/xbox/i";
        return preg_match($regex, self::Agent());
    }
    ///<summary></summary>
    public static function IsXBoxOne(){
        $regex="/xbox one/i";
        return preg_match($regex, self::Agent());
    }
}
