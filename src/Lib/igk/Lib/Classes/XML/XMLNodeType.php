<?php
// @file: IGKXMLNodeType.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\XML;

abstract class XMLNodeType{
    const CDATA=5;
    const COMMENT=3;
    const DOCTYPE=7;
    const ELEMENT=1;
    const ENDELEMENT=4;
    const NONE=-1;
    const PROCESSOR=2;
    const TEXT=6;
    const INNER_TEXT = 7;

    ///<summary></summary>
    ///<param name="i"></param>
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
