<?php
// @author: C.A.D. BONDJE DOUE
// @file: AccountManager.php
// @date: 20260730 08:48:33
namespace IGK\System\RGPD;


/**
* 
* @package IGK\System\RGPD
* @author C.A.D. BONDJE DOUE
*/
class AccountManager{
    public const BASE_HOOK = 'sys://rgpd-hook';
    public const HOOK_REQUEST_REMOVE_ACCOUNT = self::BASE_HOOK.'/remove-account';
}