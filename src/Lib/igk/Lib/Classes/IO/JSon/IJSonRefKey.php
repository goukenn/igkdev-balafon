<?php
// @author: C.A.D. BONDJE DOUE
// @file: IJSonRefKey.php
// @date: 20240419 16:03:14
namespace IGK\IO\JSon;
/**
* 
* @package IGK\IO\JSon
* @author C.A.D. BONDJE DOUE
*/
interface IJSonRefKey{

    /**
    * auto generate doc.
    * @param mixed $id
    * @return string
    */
    function json_refkey($id):string;
}