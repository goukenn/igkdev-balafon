<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Authenticator.php
// @date: 20220902 12:53:11
// @desc: 
namespace IGK\System\Security;
use IGK\System\Database\IUserProfile;
/**
 * represent authenticator
 * @package IGK\System\Security
 */
class Authenticator{
    /**
    * Property: user.
    * @var mixed
    */
    private $m_user;
    /**
    * .ctr
    * @param IUserProfile $user
    */
    public function __construct(IUserProfile $user)
    {
        $this->m_user = $user;
    }
    /**
    * Auth.
    * @param mixed $param
    * @return bool
    */
    public function auth($param):bool{
        return $this->m_user->auth($param);
    }
}