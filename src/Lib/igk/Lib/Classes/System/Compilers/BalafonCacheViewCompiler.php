<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCacheViewCompiler.php
// @date: 20220428 14:48:50
// @desc: 
namespace IGK\System\Compilers;
use Closure;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\ViewHelper;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompiler;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewExpressionArgHelper;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewHandler;
use IGK\System\ViewEnvironmentArgs;
use IGKException;

/**
 * 
 * @package IGK\System\Compilers
 */
/**
* auto generate doc.
* @package IGK\System\Compilers
*/
class BalafonCacheViewCompiler{
    /**
     * generate cache view file. 
     * @param BaseController $controller 
     * @param string $file 
     * @param mixed $args 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function Compile(BaseController $controller, string $file, $args = null, $noExtra = false ){
        $extra = "";
        $node = igk_create_notagnode();
        igk_html_article($controller, $file, $node, $args, null, false, true, false);
        ob_start();
        $option = igk_create_view_builder_option(); 
        $output = $node->render($option);
        $src = ob_get_clean();     
        if (!$noExtra){
            $extra = igk_view_builder_extra($file, $option);
        }    
        $extra = empty($output) ?  trim($extra) : $extra; 
        $f = self::TreatOuput($output . $src . $extra, $file, $args);
        return $f;
    }
    /**
    * auto generate doc.
    * @param mixed $args
    */
    public static function TreatOuput(string $source, string $file, $args = null){
        $helper = preg_match("/\b__(FILE|DIR)__\b/", $source);
        $source = preg_replace("/__FILE__/", "ViewHelper::File()", $source);
        $source = preg_replace("/__DIR__/", "ViewHelper::DIR()", $source);
        if ($helper && !(0 < preg_match("/use\s+IGK\\\\Helper\\\\ViewHelper\b\s*;/", $source, $tab))){
            $source = implode("\n", ["<?php","use IGK\\Helper\\ViewHelper;","?>",$source]);
        }
        return $source;
    }
    /**
     * compile view helper
     * @param BaseController $controller 
     * @param string $file 
     * @param mixed $args 
     * @return void 
     */
    public static function ViewCompile(BaseController $controller, string $file, ?array $args = null){
        $compiler = new ViewCompiler;
        $compiler->forCache = true;
        $compiler->params = $args;
        $compiler->options = ViewEnvironmentArgs::CreateContextViewArgument($controller, $file, __METHOD__);
        $cout =  $compiler->compile([$file]); 
        return $cout;
    }
    /**
    * Returns Bind View Compiler Handler.
    * @param BaseController $controller
    */
    public static function GetBindViewCompilerHandler(BaseController $controller){
        $__igk_attr__ = Closure::fromCallable(function($arr){
            /**
            * auto generate doc.
            * @var ViewHandler $q
            */
            $q = $this;
            if (key_exists("class", $arr)){
                $cl = trim($arr["class"] ?? "", '"');
                $tab = explode(" ", $this->tab["class"] ?? "");
                $carr = array_map(function($a)use(& $tab){
                    if (strpos($a,'-')===0){
                        $k = substr($a, 1);
                        if ( ($index = array_search($k, $tab)) === false){
                            unset($tab[$index]);
                        }
                    }else{
                        return $a;
                    }
                }, explode(" ", $cl) );
                $arr["class"] = implode(' ', array_filter(array_merge($tab, $carr)));
            }
            $q->tab = array_merge($q->tab, $arr);
            $q->attribBind = true;
        })->bindTo(ViewHandler::getInstance());
        $binding_args = get_defined_vars();
        return Closure::fromCallable(function()use( $binding_args) {
            // + | --------------------------------------------------------------------
            // + | Handle argument views
            // + |
            ${ViewExpressionArgHelper::GETTER_VAR} = (array)func_get_arg(1);
            extract(func_get_arg(1));
            foreach($binding_args as $k=>$v){
                $$k = & $binding_args[$k];
            }  
            unset($v, $k);
            include(func_get_arg(0));
        })->bindTo($controller);
    }
}