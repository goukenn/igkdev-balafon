<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormatterService.php
// @date: 20250817 13:32:13
namespace IGK\System\Text\Formatters;

use IGK\Services\IAppService;

/**
* a service that provide a formatter 
* @package IGK\System\Text\Formatters
* @author C.A.D. BONDJE DOUE
* @property ?string $engineClassName engine class name 
*/
interface IFormatterService extends IAppService{
    function format(string $src):string;
}