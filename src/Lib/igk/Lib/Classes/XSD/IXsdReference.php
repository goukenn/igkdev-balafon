<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IXsdReference.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;

/**
* Interface for xsd reference.
* @package IGK\XSD
*/
interface IXsdReference{

    /**
    * Returns Ref Type.
    */
    function getRefType();
    /** @return mixed  */
    function getRef();
}