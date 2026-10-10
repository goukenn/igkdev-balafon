<?php
// @author: C.A.D. BONDJE DOUE
// @file: BaseCommandSystemCommand.php
// @date: 20260927 18:52:46
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\ConsoleConstants;

/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
abstract class BaseCommandSystemCommand extends AppExecCommand{
	var $category = ConsoleConstants::SYS_CATEGORY;
}