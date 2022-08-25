<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCacheViewCompiler.php
// @date: 20220428 14:48:50
// @desc: 

namespace IGK\System\Compilers;

use Exception;
use IGK\Controllers\BaseController;
use IGKException;

/**
 * 
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
        // $cache = igk_cache()::view();
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
        return $output . $src . $extra;
    }
}