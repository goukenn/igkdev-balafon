<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IInitUserProfile.php
// @date: 20221113 10:16:58
// @desc: 

namespace IGK\System\Database;

/**
 * 
 * @package IGK\System\Database
 */
interface IInitUserProfile{
    function setUserInfo($userInfo);
    function getUserInfo();
}