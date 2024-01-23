<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbSendQueryListenerSupport.php
// @date: 20231220 12:33:55
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
interface IDbSendQueryListenerSupport{
    function setSendDbQueryListener(?IDbSendQueryListener $listener);

    function getSendDbQueryListener(): ?IDbSendQueryListener;
}