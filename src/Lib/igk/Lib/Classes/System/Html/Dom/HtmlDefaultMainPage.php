<?php
// @file: IGKDefaultMainPage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IGK\Resources\R;

final class HtmlDefaultMainPage extends HtmlNode{
    static $sm_instance;
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function __AcceptRender($options=null){
        if(!$this->IsVisible){
            return 0;
        }
        igk_set_env("sys://nopowered", 1);
        $this->clearChilds();
        $n=$this->addContainer()->addCol('fitw')->addRow();
        $n->setClass("default-home-page")->addObData(function(){
            if($f=igk_env_file(IGK_LIB_DIR."/Articles/startapp/default.homepage.".R::GetCurrentLang(), ".phtml")){
                include($f);
            }
        }
        , null);
        $this->author_community();
        $doc=$options->Document;
        if($doc){
            igk_google_addfont($doc, "Roboto");
            $doc->Title=igk_sys_getconfig("website_title");
            $doc->Theme->addTempFile(IGK_LIB_DIR."/".IGK_STYLE_FOLDER."/default.homepage.pcss");
            $doc->body["class"]="+google-Roboto";
            $doc->body->getAppendContent()->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->targetNode->addIGKCopyRight()->setClass("google-Roboto")->setStyle("position:absolute; bottom:0px;padding:10px; z-index:10;");
        }
        return 1;
    }
    ///<summary></summary>
    private function __construct(){
        parent::__construct("div");
        $this["class"]="igk-project-start google-Roboto igk-parent-scroll";
    }
    ///<summary></summary>

    /**
     * 
     * @return HtmlDefaultMainPage current application instance 
     */
    public static function getInstance(){
        if(self::$sm_instance == null){
            self::$sm_instance=new self();
        }
        return self::$sm_instance;
    }
    ///<summary></summary>
    public function getIsVisible(){
        if(igk_get_env("sys://defaultpage/off") == 1){
            return 0;
        }
        $v =  defined("IGK_DESIGN_MAINPAGE") || ((igk_app()->CurrentPageFolder == IGK_HOME) && (igk_get_defaultwebpagectrl() === null));
        
        // igk_wln_e("v ", $v,
        // igk_get_defaultwebpagectrl(), parent::getIsVisible());
        return $v;
    }
}
