<?php
// @author: C.A.D. BONDJE DOUE
// @file: IJSonEncodeArrayDefinition.php
// @date: 20231006 22:28:43
namespace IGK\Helper;
/**
* 
* @package IGK\Helper
*/
interface IJSonEncodeArrayDefinition{

    /**
    * auto generate doc.
    * @return bool
    */
    function isEmpty():bool;

    /**
    * auto generate doc.
    * @return bool
    */
    function isRequired():bool;
}