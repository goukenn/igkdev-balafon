<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JSCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use SQLQueryUtils;
/**
 * create a core js distribution 
 * @package IGK\System\Console\Commands
 */
class JSCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--js:dist";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "get core minified js";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "js";
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        '--js-debug'=>'flag: js debug on generation'
    ];
    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command)
    {   
        $src = igk_sys_balafon_js(null, property_exists($command->options, '--js-debug'), true, false);
        Logger::print($src); 
    }   
}