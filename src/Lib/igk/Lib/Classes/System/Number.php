<?php
// @file: Number.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System;
use Exception;
use function igk_resources_gets as __;

/**
 * use to manager number 
 * @package IGK\System
 */
final class Number
{
    /**
    * Property: size format.
    * @var mixed
    */
    static $sm_sizeFormat = array(
        "Tb" => 1099511627776,
        "Gb" => 1073741824,
        "Mb" => 1048576,
        "Kb" => 1024,
        "B" => 1
    );
    /**
     * check if number is roman litteral 
     * @param mixed $string 
     * @return bool 
     */
    static function IsRomanNumeral($string): bool
    {
        $pattern = '/^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/'; 
        return preg_match($pattern, $string) === 1;
    }
    /**
    * auto generate doc.
    * @param mixed $d
    * @return
    */
    private static function __GetValue($d)
    {
        if (is_int($d) || preg_match("/[0-9]/i", $d)) {
            return $d;
        } else
            return 10 + (ord($d) - ord('A'));
    }
    /**
    * From base.
    * @param mixed $d
    * @param mixed $base
    */
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
    /**
    * Returns Memory Size.
    * @param mixed $value
    * @param mixed $round
    */
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
    /**
    * auto generate doc.
    * @param mixed $r
    * @return
    */
    private static function HexP($r)
    {
        $g = ($r >= 10) ? chr(ord("A") + ($r - 10)) : $r;
        return $g;
    }
    /**
    * To base.
    * @param mixed $d
    * @param mixed $base
    * @param mixed $length
    */
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
    * auto generate doc.
    * @param string $value
    * @return int|float|void
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