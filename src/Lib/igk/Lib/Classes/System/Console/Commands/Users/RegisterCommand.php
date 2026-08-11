<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterCommand.php
// @date: 20230703 13:28:58
namespace IGK\System\Console\Commands\Users;

use IGK\Controllers\SysDbController;
use IGK\Helper\Authorization;
use IGK\Helper\JSon;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Colorize;
use IGK\System\Console\Logger;

/**
 * auto generate doc.
 * @package IGK\System\Console\Commands\Users
 */
class RegisterCommand extends AppExecCommand
{
  /**
   * Property: command.
   * @var mixed
   */
  var $command = '--users:register';
  /**
   * Property: desc.
   * @var mixed
   */
  var $desc = 'register command user';
  /**
   * Property: options.
   * @var mixed
   */
  var $options = [
    '--activate' => 'flag: active the user',
    '--password:password' => 'set password',
    '--groups:[group[]]' => 'set list of group attached',
    '--controller:[controller]' => 'set base controller and group(profile) authorization host'
  ];
  /**
   * Property: category.
   * @var mixed
   */
  var $category = self::USER_CAT;
  /**
   * Property: usage.
   * @var mixed
   */
  var $usage = 'login [firstname] [lastname] [options]';
  /**
   * Exec.
   * @param mixed $command
   * @param null|string $login
   * @param null|string $firtname
   * @param null|string $lastname
   */
  public function exec($command, ?string $login = null, ?string $firtname = null, ?string $lastname = null)
  {
    !$login && igk_die('login is an empty string');
    $activate = property_exists($command->options, '--activate');
    $controller = igk_getv($command->options, 'controller');
    $ctrl = $controller ? self::GetController($controller) : null;
    if ($groups = igk_getv($command->options, '--groups')) {
      if (!is_array($groups)) {
        $groups = [$groups];
      }
    }
    $r = false;
    try {
      $data = ['clLogin' => $login, 'clFirstName' => $firtname, 'clLastName' => $lastname];
      if ($activate) {
        $data[Users::FD_CL_STATUS] = 1;
      }
      if ($pwd = igk_getv($command->options, '--password')) {
        $data[Users::FD_CL_PWD] = $pwd;
      }
      if ($ctrl) {
        $data[Users::FD_CL_CLASS_NAME] = get_class($ctrl);
      }
      if ($r = Users::Register($data)){

        if ($groups) {
          $ctrl = $ctrl ?? SysDbController::ctrl(true);
          foreach ($groups as $g)
            Authorization::BindUserToGroup($ctrl, $r, $g);
        }
  
        Logger::SetColorizer(new Colorize); 
        Logger::print(json_encode($r, JSON_PRETTY_PRINT));
        
      } else {
        Logger::danger('failed to register');
      }


    } catch (\Exception $ex) {
      Logger::danger($ex->getMessage());
      return -1;
    }
    Logger::success('done');
  }
}
