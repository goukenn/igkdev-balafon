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
    const TSTRING = "xs:string";
    const TFLOAT = "xs:float";
    const TINTEGER = "xs:integer";
    const TDECIMAL = "xs:decimal";
    const TDATE = "xs:date";
    const TTIME = "xs:time";
}