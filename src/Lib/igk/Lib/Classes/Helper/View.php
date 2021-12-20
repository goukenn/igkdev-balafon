<?php

namespace IGK\Helper;

/**
 * view helper class 
 * @package
 * @method string File() get current view file 
 * @method IGKHtmlDoc Doc() get current document
 * @method HtmlNode TargetNode() get current target node
 */
class View {
    /**
     * 
     * @return string
     */
    public static function File(){
        return igk_get_viewfile();
    }
}