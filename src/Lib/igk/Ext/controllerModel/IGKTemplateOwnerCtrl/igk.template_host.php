<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk.template_host.php
// @date: 20220803 13:48:59
// @desc: 



///<summary> use to host template as a default page controller</summary>

use IGK\Controllers\DefaultPageController;

abstract class IGKTemplateHostCtrl extends DefaultPageController

implements IIGKUriActionRegistrableController
{
	public function LoadTemplate(){
		throw new IGKException(__METHOD__. " Not Implement");
	}
}