<?php

// @author: C.A.D. BONDJE DOUE
// @filename: DeniedUser.php
// @date: 20220820 09:41:18
// @desc: denied user used to set auth to false 
namespace IGK\System\Security;

/**
 * denied user : use in 
 * @package IGK\System\Security
 */
class DeniedUser{
    /**
     */
    public function auth($role):bool{
        return false;
    }
}
