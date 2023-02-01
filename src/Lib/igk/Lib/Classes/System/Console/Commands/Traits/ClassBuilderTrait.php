<?php
// @author: C.A.D. BONDJE DOUE
// @file: ClassBuilderTrait.php
// @date: 20230103 22:54:19
namespace IGK\System\Console\Commands\Traits;

use IGK\System\IO\File\PHPScriptBuilder;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Traits
*/
trait ClassBuilderTrait{
    public static function GetAllowedTypes(){
        return ["class", "interface", "trait", "struct", "enum"];
    }
    /**
     * create a class file 
     * @param mixed $command 
     * @param string $dir 
     * @param string $classPath 
     * @param string $type 
     * @param (null|string)|null $ns 
     * @param mixed $extends 
     * @param mixed $desc 
     * @param bool $force 
     * @return string|false 
     * @throws IGKException 
     */
    public function makeClass($command, string $dir, string $classPath, 
        string $type,
        ?string $ns=null,
        $extends = null,
        $desc = null,
        bool $force=false
        ){
            $g = igk_dir($classPath);
            if (strpos($g, $gs = igk_dir($ns) . "/") === 0) {
                $g = ltrim(substr($g, strlen($gs)), "/");
            }
            //if ($ctrl){
            if (($_ir = dirname($g)) != '.') {
                $ns .= "/" . $_ir;
            }
            $ns = ltrim(str_replace("/", "\\", $ns), "\\");
            $fname = igk_dir($g);
            if (!preg_match('/\.php$/', $fname)) {
                $fname .= ".php";
            }
            $file = $dir . "/" . $fname;
        if (!file_exists($file) || $force) {
            $name = igk_str_ns(igk_io_basenamewithoutext($file));
            $author = $this->getAuthor($command);
            $builder = new PHPScriptBuilder();
            $builder->type($type)
                ->namespace($ns)
                ->author($author)
                ->file(basename($file))
                ->extends($extends)
                ->name($name)
                ->desc($desc);
            igk_io_w2file($file, $builder->render());
           return $file;
        } 
        return false;
    }
}