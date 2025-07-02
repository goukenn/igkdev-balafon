<?php
// @file: IGKHtmlUsageCondition.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html;

use IGKObject;

/**
 * usage condition helper
 * @package 
 */
final class HtmlUsageCondition extends IGKObject implements IHtmlGetValue{
    public function getValue($o=null){
        $c=igk_create_node("span");
        $tc=igk_get_regctrl("sys://articles");
        $uri="#";
        if($tc != null){
            $uri=$tc->getUri("usagecondition");
        }
        $c->Content=__("lb.span.accept.usagecondition_1");
        $c->addA($uri)->Content=__("lb.usagecondition");
        return $c->render($o);
    }
}
