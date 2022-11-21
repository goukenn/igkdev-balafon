<?php

// @author: C.A.D. BONDJE DOUE
// @filename: IUserProfile.php
// @date: 20220601 08:24:39
// @desc: use profile 

namespace IGK\System\Database;

use IGK\Controllers\BaseController;

/**
 * represent user's application profile 
 * @package 
 */
interface IUserProfile{

    function getController(): ?BaseController;
    /**
     * check authorization for user
     * @param mixed $type 
     * @return bool 
     */
    function auth($type):bool; 
}
