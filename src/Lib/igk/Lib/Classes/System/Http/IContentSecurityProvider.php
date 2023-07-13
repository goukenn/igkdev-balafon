<?php
// @author: C.A.D. BONDJE DOUE
// @file: IContentSecurityProvider.php
// @date: 20230708 09:37:32
namespace IGK\System\Http;

use IGK\System\Security\Web\MapContentValidatorBase;

///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
interface IContentSecurityProvider{
    function getContentSecurity(string $name): ?MapContentValidatorBase;
}