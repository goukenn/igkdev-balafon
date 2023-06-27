<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserResponse.php
// @date: 20230427 16:54:31
namespace IGK\System\Http\Responses;

use IGK\Helper\Activator;
use IGK\System\Database\IUserProfile;

///<summary></summary>
/**
* 
* @package IGK\System\Http\Responses
*/
class UserResponse{
    var $user;
    var $groups;
    var $auths;
    var $token;
    var $message;

    public static function CreateResponse(IUserProfile $user){
        $user = $user->model();
        $data = ['user' => $user,
        'profile' => [],
        'groups' => array_map(function ($a) {
            return $a['clName'];
        }, $user->groups()),
        'auths' => array_map(function ($a) {
            return $a['clName'];
        }, $user->auths())];
        $g = new static;
        foreach(get_class_vars(get_class($g)) as $k=>$v){                 
            $g->{$k} = igk_getv($data, $k, $g->$k) ?? $v;
        }
        return $g;
    } 
    function __debugInfo()
    {
        return [];
    }
}