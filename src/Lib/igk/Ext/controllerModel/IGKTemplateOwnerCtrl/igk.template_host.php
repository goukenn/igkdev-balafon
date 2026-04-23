<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk.template_host.php
// @date: 20220803 13:48:59
// @desc: 
use IGK\Controllers\DefaultPageController;
use IGK\IUriActionRegistrableController;

/**
 * exposed template host controller 
 * @package 
 */
abstract class IGKTemplateHostCtrl extends DefaultPageController implements IUriActionRegistrableController
{
    /**
    * Loads Template.
    */
    public function LoadTemplate(){
		throw new IGKException(__METHOD__. " Not Implement");
	}
}