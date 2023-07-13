<?php

// @author: C.A.D. BONDJE DOUE
// @filename: IUserProfile.php
// @date: 20220601 08:24:39
// @desc: use profile 

namespace IGK\System\Database;

use IGK\Controllers\BaseController;
use IGK\Models\ModelBase;
use IGK\Models\Users;

/**
 * represent user's application profile 
 * @package 
 * @property bool $remembeme 
 */
interface IUserProfile{

    function getController(): ?BaseController;
    /**
     * check authorization for user
     * @param mixed $type 
     * @return bool 
     */
    function auth($type):bool; 

    /**
     * get system's user model
     * @return Users 
     */
    function model(): \IGK\Models\Users;

    /**
     * get project's user model
     * @return ModelBase 
     */
    function user(): \IGK\Models\ModelBase;
}
