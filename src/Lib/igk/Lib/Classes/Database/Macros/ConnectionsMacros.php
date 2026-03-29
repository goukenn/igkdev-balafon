<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connections.phtml
// @desc: macros for model Connections
// @date: 20260102 10:13:01
namespace IGK\Database\Macros;
use IGK\Models\Connections;
use IGK\Models\Users;
/**
* 
* @package IGK\Database\Macros
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Database\Macros
*/
abstract class ConnectionsMacros{
    /**
    * auto generate doc.
    * @param null|Users $user
    * @return bool
    */
    public static function clear(Connections $connections, ?Users $user=null){
        $guid = igk_getv($user, 'clGuid'); 
        $cond = [];
        if ($guid)
            $cond[$connections::FD_CL_USER_GUID] = $guid;
        if (empty($cond)){
            igk_die('missing conditions');
        }
        return $connections::delete($cond);
    }
}