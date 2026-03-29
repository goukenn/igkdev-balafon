<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IInitUserProfile.php
// @date: 20221113 10:16:58
// @desc: 
namespace IGK\System\Database;
/**
* auto generate doc.
* @package IGK\System\Database
*/
interface IInitUserProfile{
    /**
    * Sets User Info.
    * @param mixed $userInfo
    */
    function setUserInfo($userInfo);
    function getUserInfo();
}