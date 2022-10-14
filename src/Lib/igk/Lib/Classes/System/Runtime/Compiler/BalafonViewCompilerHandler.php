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
    /**
     * activate exmpression list 
     * @var string[]
     */
    protected $activates = [
        "@PHP_VERSION"=>'renderPhpVersion',
        "@BALAFON_VERSION" => 'renderBlfVersion',
        "@MainLayout"=>'setViewAsMainLayout',
        "@Import"=>'importFile',
    ];
    public function __construct(BalafonViewCompiler $compiler)
    {
        $this->compiler = $compiler;
    }
    public function evaluate($data){
        $name = $data;
        $args = [];
        if (strpos($data, "@")=== 0){
            $offset = 1;
            $name = '@'.StringUtility::ReadIdentifier($data, $offset);
            if ($offset<strlen($data)){
                $g = ltrim(substr($data, $offset));
                $ch = $g[0];
                if ($ch=="="){
                    $g = substr($g, 1);
                    $args = self::ReadLayoutArgs($g);
                } else {
                    $args = self::ReadLayoutArgs($g);
                }
            } 
        }
        if ($fc = igk_getv($this->activates, $name)){
            if (is_string($fc)){
                if (method_exists($this, $fc)){
                    return call_user_func_array([$this, $fc], $args);
                }
            }
            if (is_callable($fc)){
                return $fc($this);
            }
        }
    }
    public static function ReadLayoutArgs(string $data){
        return StringUtility::ReadArgs($data, ",");
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
        // igk_wln($this->compiler->options->layout);
        if (!$this->compiler->options->layout->{'@MainLayout'})
            igk_die("import in -- @MainLayout required");
        $dir = $this->compiler->options->layout->viewDir;
        if (file_exists($v_cfile = $dir."/".$file)){
            return "include '{$v_cfile}';\n";
        }
    }
    public function renderBlfVersion(){
        return 'echo "'.IGK_VERSION.'";';
    }
}