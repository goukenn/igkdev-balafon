<?php
// @author: C.A.D. BONDJE DOUE
// @file: IJSonEncodeArrayDefinition.php
// @date: 20231006 22:28:43
namespace IGK\Helper;
/**
* auto generate doc.
* @package IGK\Helper
*/
interface IJSonEncodeArrayDefinition{
    /**
    * Returns true if Empty.
    * @return bool
    */
    function isEmpty():bool;
    /**
    * Returns true if Required.
    * @return bool
    */
    function isRequired():bool;
}