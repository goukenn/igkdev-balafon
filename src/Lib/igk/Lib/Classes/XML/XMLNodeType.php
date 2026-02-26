<?php
// @file: IGKXMLNodeType.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\XML;

/**
* auto generate doc.
* @package IGK\XML
*/
abstract class XMLNodeType{

    /**
    * auto generate doc.
    * @var mixed
    */
    const CDATA=5;

    /**
    * auto generate doc.
    * @var mixed
    */
    const COMMENT=3;

    /**
    * auto generate doc.
    * @var mixed
    */
    const ELEMENT=1;

    /**
    * auto generate doc.
    * @var mixed
    */
    const ENDELEMENT=4;

    /**
    * auto generate doc.
    * @var mixed
    */
    const NONE=-1;

    /**
    * auto generate doc.
    * @var mixed
    */
    const PROCESSOR=2;

    /**
    * auto generate doc.
    * @var mixed
    */
    const TEXT=6;

    /**
    * auto generate doc.
    * @var mixed
    */
    const INNER_TEXT = 7;

    /**
    * auto generate doc.
    * @var mixed
    */
    const DOCTYPE=8;
    /**
     * Return the string name of an XML node type constant.
     *
     * @param int $i The node type constant value.
     * @return string
     */

    public static function GetString($i){
        switch($i){
            case self::NONE:
            return "NONE";
            case self::ELEMENT:
            return "ELEMENT";
            case self::PROCESSOR:
            return "PROCESSOR";
            case self::COMMENT:
            return "COMMENT";
            case self::ENDELEMENT:
            return "ENDELEMENT";
            case self::CDATA:
            return "CDATA";
            case self::TEXT:
            return "TEXT";
            case self::DOCTYPE:
            return "DOCTYPE";
            case self::INNER_TEXT:
            return 'INNER_TEXT';
        }
        return "UNKNOWN";
    }
}