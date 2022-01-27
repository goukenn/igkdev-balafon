<?php
// @file: IGKHtmlHookNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;
use function igk_resources_gets as __;

use IGKApp;
use IGKAppConfig;

class HtmlPoweredByNode extends HtmlNode{
    protected $tagname = "div";
    public static function getItem(){
        static $_instance;
        if ($_instance==null){
            $_instance = new self();
        }
        return $_instance;
    }
    public function getIsVisible()
    {
        return !IGKApp::GetConfig("hide_powered");
    }
    private function __construct()
    {
        parent::__construct();
        $this["class"] = "igk-powered no-selection no-contextmenu google-Roboto";
        $this["igk-no-contextmenu"]="1";
    }
    public function getContent()
    {
        $uri = IGKApp::GetConfig('powered_uri');
        $msg = IGKApp::GetConfig('powered_message'); 
        if ($uri && $msg){
            $data = "<a href=\"{$uri}\" title=\"powered target\">".$msg."</a>";
            return __("Powered by {0}", $data);
        } 
    }
}