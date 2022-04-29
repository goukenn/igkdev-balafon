<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCacheViewCompiler.php
// @date: 20220428 14:48:50
// @desc: 

namespace IGK\System\Compilers;

use IGK\Controllers\BaseController;


/**
 * 
 * @package IGK\System\Compilers
 */
class BalafonCacheViewCompiler{

    public static function Compile(BaseController $controller, string $file, $args = null ){
        $cache = igk_cache()::view();
        $option = igk_create_view_builder_option();
        $node = igk_create_notagnode();
        igk_html_article($controller, $file, $node, $args, null, false, true, false);
        ob_start();
        $output = $node->render($option);
        $src = ob_get_clean();        
        $extra = igk_view_builder_extra($file, $option);
        $extra = empty($output) ?  trim($extra) : $extra; 
        return $output . $src . $extra;
    }
}