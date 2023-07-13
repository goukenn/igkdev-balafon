<?php
// @file: HtmlDefaultMainPage.php
// @author: C.A.D. BONDJE DOUE
// @description: default home page
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;
  

use IGK\Resources\R;
use IGKException;

/**
 * defaut home page
 * @package IGK\System\Html\Dom
 */
final class HtmlDefaultMainPage extends HtmlNode
{
    static $sm_instance;
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    protected function _acceptRender($options = null):bool
    {
        if (!$this->getIsVisible()) {
            return false;
        }
        igk_set_env("sys://nopowered", 1);
        $this->clearChilds();
        $n = $this->container()->addCol('fitw')->addRow();
        $n->setClass("default-home-page")->addObData(
            function () {
                if ($f = igk_env_file(IGK_LIB_DIR . "/Articles/startapp/default.homepage." . R::GetCurrentLang(), IGK_VIEW_FILE_EXT)) {
                    include($f);
                }
            },
            null
        );
        // attach author community - node 
        $this->author_community();
        $doc = $options->Document;
        if ($doc) {
            if (function_exists('igk_google_addfont'))
                igk_google_addfont($doc, "Roboto");
            $doc->Title = igk_sys_getconfig("website_title");
            $doc->Theme->addTempFile(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/default.homepage.pcss");
            $doc->body["class"] = "+google-Roboto";
            $t = $doc->body->getAppendContent()->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->targetNode;
            $t->container()->row()->col()->addIGKCopyRight()->setClass("google-Roboto no-wrap")->setStyle("position:absolute; bottom:0px;padding:10px; z-index:10;");
        }
        return 1;
    }
    ///<summary>.ctr</summary>
    /**
     * .ctr
     * @return void 
     */
    private function __construct()
    {
        parent::__construct("div");
        $this["class"] = "igk-project-start google-Roboto igk-parent-scroll";
    }
    ///<summary></summary>

    /**
     * 
     * @return HtmlDefaultMainPage current application instance 
     */
    public static function getInstance()
    {
        if (self::$sm_instance == null) {
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    ///<summary></summary>
    /**
     * 
     * @return int|bool 
     * @throws IGKException 
     */
    public function getIsVisible()
    {
        if (igk_get_env("sys://defaultpage/off") == 1) {
            return false;
        }
        return parent::getIsVisible() && (defined("IGK_DESIGN_MAINPAGE") || ((igk_app()->CurrentPageFolder == IGK_HOME) && (igk_get_defaultwebpagectrl() === null)));
    }
}
