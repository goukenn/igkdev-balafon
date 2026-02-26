<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbResolveLinkListener.php
// @date: 20221125 09:45:23
namespace IGK\System\Database;
/**
* 
* @package IGK\System\Database
*/
interface IDbResolveLinkListener{

    /**
    * auto generate doc.
    * @param string $linkType
    * @return bool
    */
    function resolve(string $linkType):bool;
}