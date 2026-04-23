<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerCommand.php
// @date: 20221024 16:36:06
namespace IGK\System\Console\Commands;
use ArrayAccess;
use ArrayIterator;
use Closure;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Helper\ViewHelper;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Runtime\Compiler\CompilerConstants;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompiler;
use IGK\System\ViewDataArgs;
use IGK\System\ViewEnvironmentArgs;
use IGK\System\WinUI\PageLayout;
use IteratorAggregate;
use Traversable;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ViewCompilerCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--compile-view";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "compile view file";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "compilation";
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        "--cache" => "for cache."
    ];
    /**
    * Shows Usage.
    */
    public function showUsage(){
        Logger::print(sprintf('%s file [controller] [options]', $this->command));
    }
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $path
    * @param null|mixed $ctrl
    */
    public function exec($command, ?string $path = null, $ctrl = null)
    {
        if (is_null($path)) {
            igk_die("view file required");
        }
        if (!is_file($path)) {
            igk_die("path not found");
        }
        $cache = property_exists($command->options, "--cache");
        $ctrl = (is_null($ctrl) ? null : self::GetController($ctrl, false)) ?? SysDbController::ctrl();
        $ctrl::register_autoload();
        $compiler = new ViewCompiler;
        $compiler->forCache = $cache;
        $compiler->variables = [];
        $compiler->options = ViewEnvironmentArgs::CreateContextViewArgument(
            $ctrl,
            $path,
            "command_line"
        );
        $compiler->options->layout = new PageLayout();
        $compiler->options->data = [
            (object)["user" => "1", "name" => "charles"],
            (object)["user" => "2", "name" => "bondje"],
        ];
        if ($src = $compiler->compile([$path], [])) {
            fwrite(STDERR, "Compilation result : \n");
            echo $src . "\n";
        }
            // + | --------------------------------------------------------------------
            // + | EVALUATE WITH DUMMY DATA
            // + |
    }
    /**
    * Creates Eval Code.
    * @param BaseController $controller
    */
    public static function CreateEvalCode(BaseController $controller){
        return Closure::fromCallable(function($compiler, $src){
            ob_start();
            extract((array)$compiler->options);
            ${CompilerConstants::BINDING_DATA_CONTEXT_VAR} = new NoDataProvided();
            $data = new ViewDataArgs($data);
            $rdata = eval("?>" . $src);
            $c = ob_get_contents();
            ob_end_clean();
            return implode("\n", array_filter([$rdata, $c]));
        })->bindTo($controller);
    }
}
/**
* No data provided.
* @package IGK\System\Console\Commands
*/
class NoDataProvided implements ArrayAccess
{
    use ArrayAccessSelfTrait;
    /**
    * Access offset get.
    * @param mixed $index
    */
    public function _access_OffsetGet($index)
    {
        return [];
    }
}