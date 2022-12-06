<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbResolveLinkListener.php
// @date: 20221125 09:45:23
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
interface IDbResolveLinkListener{
    
    function resolve(string $linkType):bool;
}