<?php


///<summary> use to host template as a default page controller</summary>

use IGK\Controllers\DefaultPageController;

abstract class IGKTemplateHostCtrl extends DefaultPageController

implements IIGKUriActionRegistrableController
{
	public function LoadTemplate(){
		throw new IGKException(__METHOD__. " Not Implement");
	}
}