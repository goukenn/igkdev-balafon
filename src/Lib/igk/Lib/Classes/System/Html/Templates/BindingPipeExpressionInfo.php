<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BindingPipeExpressionInfo.php
// @date: 20220819 15:55:25
// @desc: 
namespace IGK\System\Html\Templates;
use function igk_resources_gets as __;
use IGK\Helper\IO;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;

class BindingPipeExpressionInfo{
    /**
     * create default pipe definition
     * @return array 
     */
    public static function CreateNewDefinition():array{
        return [
            "mailto" => function ($v) {
                $s = "mailto:" . $v;
                return $s;
            }, "capitalize" => function ($v) {
                return igk_str_capitalize($v);
            }, "dateformat" => function ($v, $format = null) {
                $t = strtotime($v);
                $base_fmt = igk_configs()->getConfig("dataformat", "d/m/Y");
                if ($format === null) {
                    $format = $base_fmt;
                } else {
                    list($format)
                        = [igk_getv($format, "fmt", $base_fmt)];
                }
                return date($format, $t);
            }, "trim" => function ($v) {
                return trim($v);
            }, "uppercase" => function ($v) {
                return strtoupper($v);
            }, "lowercase" => function ($v) {
                return strtolower($v);
            }, "utf8" => function ($v) {
                return utf8_decode($v);
            }, "lang" => function ($v) {
                return __($v);
            }, "json" => function ($v) {
                return json_encode($v);
            }, "date" => function ($v, $options = null) {
                $v = strtotime($v);
                if ($options) {
                    $fmt = igk_getv($options, "fmt");
                    $v = date($fmt, $v);
                }
                return $v;
            }, "mysqldate" => function ($v) {
                $v = strtotime($v);
                if ($v) {
                    return date(IGK_MYSQL_DATETIME_FORMAT, $v);
                }
                return "";
            }, "size" => function ($v) {
                if (is_numeric($v)) {
                    $v = IO::GetFileSize($v);
                }
                return $v;
            }, "currency" => function ($v, $options = null) {
                return sprintf('%.2f', $v) . " EUR";
            }, 'urlencode' => function ($v){
                return urlencode($v);
            }
        ];
    }
    /**
     * read info and detect litteral value
     * @param string $tv 
     * @return string[] 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function ReadInfo(string $tv){
        $info = [
            "type"=>"litteral",
            "value" =>$tv
        ];
        $ln = strlen($tv);
        $pos = 0;
            if (is_string($tv) && !empty($tv) && !is_numeric($tv)){
            // detect php expression
            while($pos < $ln){
                $ch = $tv[$pos];
                switch($ch){
                    case "'":
                    case '"':
                    case '$':
                    case '(':
                        $info["type"] = "php";
                        break 2;
                }
                $pos++;
            }
        }      
        return $info;
    }
}
