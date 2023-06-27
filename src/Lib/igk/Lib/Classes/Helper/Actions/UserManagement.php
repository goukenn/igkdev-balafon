<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserManagement.php
// @date: 20230613 16:05:31
namespace IGK\Helper\Actions;

use IGK\Actions\Dispatcher;
use IGK\Models\Users;
use ReflectionMethod;

///<summary></summary>
/**
* 
* @package IGK\Helper\Actions
*/
class UserManagement{
    public function post($name, $args = null){
        if (method_exists($this, $fc ="{$name}_post")){
            $ref = new ReflectionMethod($this, $fc);
            $args = ($args ?  Dispatcher::GetInjectArgs($ref, $args) : null) ?? [];
            return call_user_func_array([$this, $fc], $args);
        }
    }
    public function get($name, $args = null){
        if (method_exists($this, $fc ="{$name}_".__FUNCTION__)){
            $ref = new ReflectionMethod($this, $fc);
            $args = ($args ?  Dispatcher::GetInjectArgs($ref, $args) : null) ?? [];
            return call_user_func_array([$this, $fc], $args);
        }
    }
    public function block_post( Users $user){
        $user->clStatus = 0;
        $r = $user->save();
        return ['data'=>['success'=>$r, "status"=>$user->clStatus, 'id'=>$user->clId, 'guid'=>$user->clGuid]];
    }
    public function enable_post( Users $user){
        $user->clStatus = 1;
        $r = $user->save();
        return ['data'=>['success'=>$r, "status"=>$user->clStatus, 'id'=>$user->clId, 'guid'=>$user->clGuid]];
    }
}