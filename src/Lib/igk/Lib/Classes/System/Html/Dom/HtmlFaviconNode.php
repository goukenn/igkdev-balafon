<?php
// @file: IGKHtmlFavicon.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

final class HtmlFaviconNode extends HtmlNode{
    static $sm_instance;
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function __AcceptRender($options=null){
        if($options && isset($options->Document)){
            $g=$options->Document->getFavicon();
            $this["href"]=$g; 
            return $g != null;
        }
        return false;
    }
    ///<summary></summary>
    private function __construct(){
        parent::__construct("link");
        $this["rel"]="shortcut icon";
        $this["type"]="image/x-icon";
        $this["href"]=null;
    }
    ///<summary></summary>
    public function __sleep(){
        return array();
    }
    ///<summary></summary>
    public function __wakeup(){    }
    ///<summary></summary>
    public static function getItem(){
        if(self::$sm_instance === null){
            self::$sm_instance=new HtmlFaviconNode();
        }
        return self::$sm_instance;
    }
}
