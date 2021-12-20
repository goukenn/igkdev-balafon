<?php
// @file: IGKdbColumnDataType.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Database;


final class DbColumnDataType{
    const DATE_TIME="Datetime";
    const DOUBLE_SINGLE="Double";
    const INT32="Int";
    const SINGLE="Float";
    const TEXT="Text";
    const VARCHAR="VarChar";
    ///<summary></summary>
    public static function GetDbTypes(){
        static $t;
        if($t === null)
            $t=array(
            self::VARCHAR=>self::VARCHAR,
            self::INT32=>self::INT32,
            self::TEXT=>self::TEXT,
            self::SINGLE=>self::SINGLE,
            self::DOUBLE_SINGLE=>self::DOUBLE_SINGLE,
            self::DATE_TIME=>self::DATE_TIME
        );
        return $t;
    }
}
