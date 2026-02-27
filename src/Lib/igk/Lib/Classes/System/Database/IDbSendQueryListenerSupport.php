<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbSendQueryListenerSupport.php
// @date: 20231220 12:33:55
namespace IGK\System\Database;

/**
* auto generate doc.
* @package IGK\System\Database
*/
interface IDbSendQueryListenerSupport{

    /**
    * Sets Send Db Query Listener.
    * @param null|IDbSendQueryListener $listener
    * @return ?IDbSendQueryListener
    */
    function setSendDbQueryListener(?IDbSendQueryListener $listener);
    function getSendDbQueryListener(): ?IDbSendQueryListener;
}