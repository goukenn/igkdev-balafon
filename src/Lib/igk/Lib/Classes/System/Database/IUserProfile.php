<?php

// @author: C.A.D. BONDJE DOUE
// @filename: IUserProfile.php
// @date: 20220601 08:24:39
// @desc: use profile 

namespace IGK\System\Database;

/**
 * represent profile 
 * @package 
 */
interface IUserProfile{
    /**
     * check authorization for user
     * @param mixed $type 
     * @return bool 
     */
    function auth($type):bool;
}
