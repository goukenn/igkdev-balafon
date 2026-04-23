<?php
// @file: IGKdbColumnDataType.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;

/**
* Db column data type.
* @package IGK\Database
*/
final class DbColumnDataType{
    /**
    * Constant: date time.
    * @var mixed
    */
    const DATE_TIME="Datetime";
    /**
    * Constant: double single.
    * @var mixed
    */
    const DOUBLE_SINGLE="Double";
    /**
    * Constant: int32.
    * @var mixed
    */
    const INT32="Int";
    /**
    * Constant: single.
    * @var mixed
    */
    const SINGLE="Float";
    /**
    * Constant: text.
    * @var mixed
    */
    const TEXT="Text";
    /**
    * Constant: varchar.
    * @var mixed
    */
    const VARCHAR="VarChar";
    /**
    * Returns Db Types.
    */
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