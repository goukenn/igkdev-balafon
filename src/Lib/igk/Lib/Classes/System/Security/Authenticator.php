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
    private $m_user;
    public function __construct(IUserProfile $user)
    {
        $this->m_user = $user;
    }
    public function auth($param):bool{
        return $this->m_user->auth($param);
    }
}