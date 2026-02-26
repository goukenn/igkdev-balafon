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
    * auto generate doc.
    * @var mixed
    */
    var $command = "--js:dist";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = "get core minified js";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "js";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options = [
        '--js-debug'=>'flag: js debug on generation'
    ];

    /**
    * auto generate doc.
    * @param mixed $command
    */
    public function exec($command)
    {   
        $src = igk_sys_balafon_js(null, property_exists($command->options, '--js-debug'), true, false);
        Logger::print($src); 
    }   
}