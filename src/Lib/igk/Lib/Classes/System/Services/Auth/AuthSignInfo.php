<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AuthSignInfo.php
// @date: 20220607 16:11:33
// @desc: auth provided

namespace IGK\System\Services\Auth;

/**
 * auth information
 * @package IGK\System\Services\Auth
 */
class AuthSignInfo{
    var $login;
    var $verified;
    var $email;
    var $gender;
    var $name;
    var $firstname;
    var $lastname;
    /**
     * provile picture
     * @var mixed
     */
    var $picture;
    /**
     * provider name
     * @var mixed
     */
    var $provider;
    /**
     * birth date
     * @var mixed
     */
    var $birthday;
    /**
     * id in provider
     * @var mixed
     */
    var $id;
}

