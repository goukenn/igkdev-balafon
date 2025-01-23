<?php
// @file: Number.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System;

use Exception;

use function igk_resources_gets as __;

final class Number
{
    static $sm_sizeFormat = array(
        "Tb" => 1099511627776,
        "Gb" => 1073741824,
        "Mb" => 1048576,
        "Kb" => 1024,
        "B" => 1
    );
    ///<summary></summary>
    ///<param name="d"></param>
    private static function __GetValue($d)
    {
        if (is_int($d) || preg_match("/[0-9]/i", $d)) {
            return $d;
        } else
            return 10 + (ord($d) - ord('A'));
    }
    ///<summary></summary>
    ///<param name="d"></param>
    ///<param name="base"></param>
    public static function FromBase($d, $base)
    {
        $o = 0;
        $v = strtoupper(IGK_STR_EMPTY . $d);
        $ln = strlen($v);
        for ($i = 0; $i < $ln; $i++) {
            $h = self::__GetValue($v[$ln - $i - 1]);
            $o += pow($base, $i) * $h;
        }
        return $o;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    ///<param name="round" default="4"></param>
    public static function GetMemorySize($value, $round = 4)
    {
        if ($value == 0)
            return "0 byte";
        foreach (self::$sm_sizeFormat as $k => $v) {
            if ($value > $v) {
                return round(($value / $v), $round) . " " . __("enum.sizeUnit." . $k);
            }
        }
        return "0 byte";
    }
    ///<summary></summary>
    ///<param name="r"></param>
    private static function HexP($r)
    {
        $g = ($r >= 10) ? chr(ord("A") + ($r - 10)) : $r;
        return $g;
    }
    ///<summary></summary>
    ///<param name="d"></param>
    ///<param name="base"></param>
    ///<param name="length" default="-1"></param>
    public static function ToBase($d, $base, $length = -1)
    {
        if (is_numeric($d) == false)
            return "0";
        $o = IGK_STR_EMPTY;
        if ($base > 0) {
            if (is_string($d)) {
                for ($i = 0; $i < strlen($d); $i++) {
                    $th = ord($d[$i]);
                    $p = (int)($th / $base);
                    $r = ($th % $base);
                    if ($p < $base) {
                        if ($p != 0)
                            $o = self::HexP($p) . self::HexP($r);
                        else
                            $o = self::HexP($r);
                    } else {
                        $o = self::HexP($r) . $o;
                        $o = self::ToBase($p, $base) . $o;
                    }
                }
            } else {
                $p = intval($d / $base);
                $r = intval($d) % $base;
                if ($p < $base) {
                    if ($p != 0)
                        $o = self::HexP($p) . self::HexP($r);
                    else
                        $o = self::HexP($r);
                } else {
                    $o = self::HexP($r) . $o;
                    $o = self::ToBase($p, $base) . $o;
                }
            }
        }
        if ($length != -1) {
            for ($i = strlen($o); $i < $length; $i++) {
                $o = "0" . $o;
            }
        }
        return $o;
    }

    /**
     * 
     * @param string $value 
     * @return int|float|void 
     * @throws Exception 
     */
    public static function MemoryToBytes(string $value)
    {
        if (is_numeric($value)) {
            return intval($value);
        }
        if (false !== preg_match("/([0-9]+)(?P<u>(M|K|T|G)(o)?)/i", $value, $tab)) {
            $r = intval($tab[1]);
            $f = strtolower($tab['u']);
            $v_t = igk_getv([
                'k' => 'Kb',
                'ko' => 'Kb',
                't' => 'Tb',
                'to' => 'Tb',
                'g' => 'Gb',
                'go' => 'Gb',
                'm' => 'Mb',
                'mo' => 'Mb'
            ], $f);
            $v_t = igk_getv(self::$sm_sizeFormat, $v_t);


            return $r * $v_t;
        }
    }
    /**
     * string or inde
     * @param string|int $q 
     * @return void 
     */
    public static function IsZeroIndexNumber($q)
    {
        return (is_string($q) && (strlen($q) > 0) && ($q === '0')) ||
            (is_numeric($q) && $q === 0);
    }
}
