<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlEngineHelper.php
// @date: 20230517 13:05:25
namespace IGK\System\Html\Helpers;

use IGK\Controllers\BaseController;
use IGK\System\DataArgs;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\HtmlLoadingContextOptions;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Helpers
*/
abstract class HtmlEngineHelper{
    /**
     * 
     * @param HtmlItemBase $node 
     * @param string $content 
     * @param IGK\System\Html\Helpers\args *1413dcd 
     * @return void 
     * @throws IGKException 
     */
    public static function BindContent(HtmlItemBase $node, string $content, $args, ?BaseController $ctrl=null){
        $options = new HtmlLoadingContextOptions;
        $options->load_expression = true;
        $options->transformToEval = false;
        $options->raw = new DataArgs($args);
        $options->ctrl = $ctrl;
        $node::LoadInContext($node, $content, $options);
    }
}