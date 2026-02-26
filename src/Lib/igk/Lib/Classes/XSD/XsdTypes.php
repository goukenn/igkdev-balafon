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
    * auto generate doc.
    * @var mixed
    */
    const TSTRING = "xs:string";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TFLOAT = "xs:float";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TINTEGER = "xs:integer";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TDECIMAL = "xs:decimal";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TDATE = "xs:date";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TTIME = "xs:time";
}