<?php
// @file: class.igk_templateEditor.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Controllers\BaseController;
use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;

/**
* Use to edit a template
*/
final class IGKTemplateEditor extends BaseController{
    use NoDbActiveControllerTrait;
    /**
    * auto generate doc.
    */    public function __construct(){
        parent::__construct();
    }
    /**
    * cancel edition of the controller
    */
    public function Cancel($ctrl){}
    /**
    * call this function edit a controller
    */
    public function Edit($ctrl){
        if(!$this->can_edit($ctrl)){
            return;};
    }
    /**
    * get tempory folder
    */
    public function getTempFolder(){
        return $this->getDeclaredDir()."/temp";
    }
}