<?php
// @author: C.A.D. BONDJE DOUE
// @file: ArmonicCommand.php
// @date: 20221023 10:18:19
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Runtime\Compiler\Armonic\ArmonicCompiler;
/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ArmonicCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--armonic";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "armonise file ";
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $path
    */
    public function exec($command, ?string $path = null)
    {
        empty($path) && igk_die("path require");
        $compiler = new ArmonicCompiler;
        if (!empty($g = $compiler->compileFile($path))) {
            echo $g;
        }
    }
}