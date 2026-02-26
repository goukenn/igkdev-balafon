<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDbSendQueryListenerSupport.php
// @date: 20231220 12:33:55
namespace IGK\System\Database;
/**
* 
* @package IGK\System\Database
*/
interface IDbSendQueryListenerSupport{

    /**
    * auto generate doc.
    * @param null|IDbSendQueryListener $listener
    * @return ?IDbSendQueryListener
    */
    function setSendDbQueryListener(?IDbSendQueryListener $listener);
    function getSendDbQueryListener(): ?IDbSendQueryListener;
}