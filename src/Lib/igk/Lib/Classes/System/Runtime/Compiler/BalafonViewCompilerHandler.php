<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompilerHandler.php
// @date: 20220909 16:50:16
namespace IGK\System\Runtime\Compiler;

use IGK\Helper\StringUtility;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class BalafonViewCompilerHandler{
    protected $compiler;
    protected $activates = [
        "@PHP_VERSION"=>'renderPhpVersion',
        "@MainLayout"=>'setViewAsMainLayout',
        "@Import"=>'importFile',
    ];
    public function __construct(BalafonViewCompiler $compiler)
    {
        $this->compiler = $compiler;
    }
    public function evaluate($data){
        $name = $data;
        if (strpos($data, "@")=== 0){
            $offset = 1;
            $name = '@'.StringUtility::ReadIdentifier($data, $offset);
        }
        if ($fc = igk_getv($this->activates, $name)){
            if (is_string($fc)){
                if (method_exists($this, $fc)){
                    return call_user_func_array([$this, $fc], []);
                }
            }
            if (is_callable($fc)){
                return $fc($this);
            }
        }
    }
    public function renderPhpVersion(){
        return "echo PHP_VERSION;\n";
    }
    public function setViewAsMainLayout(){
        $this->compiler->options->layout->{'@MainLayout'} = 1;
    }
    /**
     * import file in layout
     */
    public function importFile(string $file){
        if (!$this->compiler->options->layout->{'@MainLayout'})
            die("import in -- @MainLayout required");
        igk_wln_e("file : ", $file);
    }
}