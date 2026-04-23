<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdTypes.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;

/**
 * represent simple xsd type
 * @package IGK\XSD
 */
abstract class XsdTypes{
    /**
    * Constant: tstring.
    * @var mixed
    */
    const TSTRING = "xs:string";
    /**
    * Constant: tfloat.
    * @var mixed
    */
    const TFLOAT = "xs:float";
    /**
    * Constant: tinteger.
    * @var mixed
    */
    const TINTEGER = "xs:integer";
    /**
    * Constant: tdecimal.
    * @var mixed
    */
    const TDECIMAL = "xs:decimal";
    /**
    * Constant: tdate.
    * @var mixed
    */
    const TDATE = "xs:date";
    /**
    * Constant: ttime.
    * @var mixed
    */
    const TTIME = "xs:time";
}