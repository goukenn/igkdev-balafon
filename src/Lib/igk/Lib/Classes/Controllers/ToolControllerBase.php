<?php
// @file: ToolControllerBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\Controllers\BaseController;
use IGKResourceUriResolver;
use function igk_resources_gets as __;

/**
* Tool controller base.
* @package IGK\Controllers
*/
abstract class ToolControllerBase extends BaseController{
    /**
    * Property: tools.
    * @var mixed
    */
    static $sm_tools = [];
    /**
    * Do action.
    */
    public function doAction(){    }
    /**
    * Returns Can Init Db.
    */
    public function getCanInitDb(){
        return false;
    }
    /**
    * Returns Image Uri.
    */
    public function getImageUri(){
        return IGK_STR_EMPTY;
    }
    /**
    * Returns Is Available.
    */
    public function getIsAvailable(){
        return true;
    }
    /**
    * Hides Tool.
    * @param mixed $ownernode
    */
    public function hideTool($ownernode){
        igk_html_rm($this->TargetNode);
        $t=$this->TargetNode;
        $t->clearChilds();
    }
    /**
    * Initializes Complete.
    * @param null|mixed $context
    */
    protected function initComplete($context=null){
        parent::initComplete();
        if($this->getIsAvailable()){
            self::$sm_tools[get_class($this)] = $this;
        }
    }
    /**
    * Refreshes Tool View.
    */
    public function refreshToolView(){
        igk_getctrl(self::class)->View();
    }
    /**
    * Shows Tool.
    * @param mixed $ownernode
    */
    public function showTool($ownernode){
        $t=$this->getTargetNode();
        $ownernode->add($t);
        $t["class"]="dispib alignc alignt";
        $t["style"]="min-width: 96px; min-height:72px;";
        $t->clearChilds();
        $d=$t->div();
        $a=$d->add("a", array(
            "class"=>"alignc dispib",
            "href"=>$this->getUri("doAction")
        ));
        $resolver=IGKResourceUriResolver::getInstance();
        $c=$this->getImageUri();
        $m=$a->add("img", array("style"=>"width: 48px; height:48px;display:inline-block;"));
        if($c){
            $m->setSrc($c);
        }
        $a->div()->Content=__("tool.".$this->Name);
    }
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController{ 
        return $this;
    }
}