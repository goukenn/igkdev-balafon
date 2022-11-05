<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompileProcessCommandHandler.php
// @date: 20221027 13:34:06
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use IGK\Helper\StringUtility;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewCompiler;
use IGKCaches;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewCompileProcessCommandHandler{ 
        /**
         * 
         * @var IViewCompiler
         */
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
            "@Include"=>'includeFile',
        ];
        public function __construct(IViewCompiler $compiler)
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
        public function includeFile(string $file){
            // igk_wln($this->compiler->options->layout);
             
            if (!$this->compiler->options->layout->{'@MainLayout'})
                igk_die("import in -- @MainLayout required");
            $dir = $this->compiler->options->layout->viewDir;
            if (file_exists($v_cfile = $dir."/".$file)){
                return "include '{$v_cfile}';\n";
            }
        }
        public function importFile(string $file){
            // igk_wln_e(__FILE__.":".__LINE__, __METHOD__, $file);    
            $dir = $this->compiler->options->layout->viewDir;
            if (file_exists($v_cfile = $dir."/".$file)){
                $ext = ".cphtml";
                // check for cached compiled view if not to compilation
                $cache_file = IGKCaches::view()->getCacheFilePath($v_cfile, $ext);
                $expired =  IGKCaches::view()->cacheExpired($v_cfile, $ext);
                $src = "";
                if ($expired){
                    $c = ltrim(file_get_contents($v_cfile));
                    if (!empty($c)){
                        if (strpos($c, "<?php") === 0){
                            // 
                            $src = $this->compiler->compileSource($c);
                            igk_io_w2file($cache_file, $src);
                        }
                    }
                }
                // igk_wln_e(__FILE__.":".__LINE__, "cache ", $cache_file, $v_cfile, 
                // $expired, 
                // igk_io_collapse_path($cache_file),
                // igk_io_collapse_const_path($cache_file)
            // );
                return 'include '.igk_io_collapse_const_path($cache_file).";\n";
                // return $src;
            }
        }
        public function renderBlfVersion(){
            return 'echo "'.IGK_VERSION.'";';
        }
    }