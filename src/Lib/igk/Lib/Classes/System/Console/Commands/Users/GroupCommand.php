<?php
// @author: C.A.D. BONDJE DOUE
// @file: GroupCommand.php
// @date: 20230802 13:27:05
namespace IGK\System\Console\Commands\Users;

use IGK\Helper\JSon;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Colorize;
use IGK\System\Console\Logger;

/**
 * auto generate doc.
 * @package IGK\System\Console\Commands\Users
 */
class GroupCommand extends AppExecCommand
{
    /**
     * Property: command.
     * @var mixed
     */
    var $command = '--users:group';
    /**
     * Property: desc.
     * @var mixed
     */
    var $desc = 'view user\'s group';
	/* var $options=[]; */
    /**
     * Property: category.
     * @var mixed
     */
    var $category = self::USER_CAT;
    /**
     * Property: usage.
     * @var mixed
     */
    var $usage = 'login [option]';
    /**
     * Exec.
     * @param mixed $command
     * @param null|string $login
     */
    public function exec($command, ?string $login = null)
    {
        is_null($login) && igk_die('login required');
        if ($user = igk_get_user_bylogin($login)) {

            $groups = $user->groups();
            Logger::info("group result : ");
            Logger::SetColorizer(new Colorize);
            if ($groups) {
                Logger::print(
                    JSon::Encode($groups, null, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );
            } else {
                Logger::print('no group found');
            }
            echo PHP_EOL;
        }
    }
}
