<?php
// @author: C.A.D. BONDJE DOUE
// @file: ResetControllerIDSCommand.php
// @date: 20260924 13:04:42
namespace IGK\System\Console\Commands\Core;

use IGK\System\Console\AppExecCommand;
use IGK\System\IO\StringBuilder;

/**
 * 
 * @package IGK\System\Console\Commands\Core
 * @author C.A.D. BONDJE DOUE
 */
class ResetControllerIDSCommand extends AppExecCommand
{
	var $command = '--reset-ids';
	var $desc = 'reset controller ids';
	/* var $options=[]; */
	/* var $category = ''; */
	/* var $usage = ''; */
	public function exec($command) {
		self::ResetIDS();
	}
	public static function ResetIDS()
	{
		$sb = new StringBuilder();
		$sb->appendLine('<?php');
		$sb->appendLine('// @desc: generated ids for controller ');
		$sb->appendLine('// @date: ' . date('Ymd H:i:s'));
		$tab = [
			'IGK_FRAME_CTRL',
			'IGK_PIC_RES_CTRL',
			'IGK_USER_GROUPS_CTRL',
			'IGK_SYS_CTRL',
			'IGK_MODULE_CNF_CTRL',
			'IGK_SYSDB_CTRL',
			'IGK_MENU_CTRL',
			'IGK_CONF_CTRL',
			'IGK_NOTIFICATION_CTRL',
			'IGK_CTRL_MANAGER',
			'IGK_CTRL_TOOLS',
			'IGK_CTRL_SESSION_MANAGER',
			'IGK_CA_CTRL',
			'IGK_SYSACTION_CTRL',
			'IGK_COMPONENT_MANAGER_CTRL',
			'IGK_DEBUG_CTRL',
			'IGK_CB_REF_CTRL',
			'IGK_LANGUAGE_CTRL',
			'IGK_SESSION_CTRL',
			'IGK_TOOLS_CTRL',
			'IGK_USER_CTRL',
			'IGK_MYSQL_DB_CTRL',
			'IGK_SUBDOMAINNAME_CTRL',
			'IGK_CACHE_CTRL',
			'IGK_AUTH_CTRL',
			'IGK_MAIL_CTRL',
			'IGK_COMPOSER_CTRL',
			'IGK_USERGROUP_CTRL',
			'IGK_USER_AND_GROUP_CTRL',
			'IGK_DB_CONF_CTRL',
		];
		sort($tab);
		foreach ($tab as $p) {
			$sb->appendLine(sprintf('define(\'%s\', \'%s\');', $p, igk_create_guid()));
		}  
		igk_io_a2file(IGK_LIB_DIR . '/igk_ids.php', $sb);
	}
}
