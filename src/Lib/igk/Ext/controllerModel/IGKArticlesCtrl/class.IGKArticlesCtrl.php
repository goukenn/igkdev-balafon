<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKArticlesCtrl.php
// @date: 20220803 13:48:59
// @desc: 

///<summary>represent used to store common primary articles and templates</summary>

use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;

abstract class IGKAtriclesCtrlBase extends \IGK\Controllers\ControllerTypeBase
{
	use NoDbActiveControllerTrait;
	public function __construct(){
		parent::__construct();
	}
	///<summary>bind primary articles</summary>
	public function bindArticle($ctrl, $name, $target, $row){
		$f = $this->getArticle($name);
		if (file_exists($f)){
			igk_html_binddata($ctrl, $target, $f, $row);
		}
	}
} 