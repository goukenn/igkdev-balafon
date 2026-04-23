<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonCLIService.php
// @date: 20231016 09:53:31
namespace IGK\System\Console;
use IGK\Controllers\BaseController;
use IGK\System\Console\Commands\BalafonCLICommand;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGKException;

/**
* auto generate doc.
* @package IGK\System\Console
*/
class BalafonCLIService{
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        // + | get balafon command to call 
        throw new IGKException(sprintf('cli %s command not found', $name));
    }
    /**
    * Creates Project Class.
    * @param BaseController $ctrl
    * @param mixed $class_name
    * @param null|mixed $options
    */
    public function makeProjectClass(BaseController $ctrl, $class_name, $options=null){
        $file = Path::Combine($ctrl::classdir(), igk_uri($class_name));
        $author = IGK_AUTHOR;
        $desc = '';
        $defs = '';
        $extends = [];
        $implements = [];
        $uses = [];
        if ($options){
            extract((array)igk_get_robj('type|extends|implements|defs|uses', 1, $options));
        }
        $pns =  igk_dirname(igk_uri($class_name)); 
        $type = 'class';
        $ns = igk_str_ns( Path::Combine(igk_uri($ctrl->getEntryNamespace()), $pns));
        $name = basename($file);
        $file.= '.php';
        $builder = new PHPScriptBuilder();
        $builder->type($type)
            ->namespace($ns)
            ->author($author)
            ->file(basename($file))
            ->extends($extends)
            ->implements($implements)
            ->uses($uses)
            ->name($name)
            ->desc($desc)
            ->defs($defs);
        igk_is_debug() && Logger::info('generate: '.$file);
        return igk_io_w2file($file, $builder->render());
    }
    /**
    * Called when an object is used as a function.
    */
    public function __invoke()
    {
    }
}