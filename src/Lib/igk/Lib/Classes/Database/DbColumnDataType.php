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
* auto generate doc.
* @package IGK\Database
*/
final class DbColumnDataType{

    /**
    * auto generate doc.
    * @var mixed
    */
    const DATE_TIME="Datetime";

    /**
    * auto generate doc.
    * @var mixed
    */
    const DOUBLE_SINGLE="Double";

    /**
    * auto generate doc.
    * @var mixed
    */
    const INT32="Int";

    /**
    * auto generate doc.
    * @var mixed
    */
    const SINGLE="Float";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TEXT="Text";

    /**
    * auto generate doc.
    * @var mixed
    */
    const VARCHAR="VarChar";

    /**
    * auto generate doc.
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