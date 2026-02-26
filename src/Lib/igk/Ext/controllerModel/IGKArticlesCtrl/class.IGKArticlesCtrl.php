<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKArticlesCtrl.php
// @date: 20220803 13:48:59
// @desc: 

use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;

/**
* Igkatricles ctrl base.
*/
abstract class IGKAtriclesCtrlBase extends \IGK\Controllers\ControllerTypeBase
{
	use NoDbActiveControllerTrait;
	/**
	 * Constructor.
	 */
	public function __construct(){
		parent::__construct();
	}
	/**
	 * Binds article data to a target node if the article file exists.
	 *
	 * @param mixed  $ctrl   The controller instance.
	 * @param string $name   The article name to load.
	 * @param mixed  $target The target node to render into.
	 * @param mixed  $row    The data row to bind.
	 * @return void
	 */
	public function bindArticle($ctrl, $name, $target, $row){
		$f = $this->getArticle($name);
		if (igk_io_file_exists($f)){
			igk_html_binddata($ctrl, $target, $f, $row);
		}
	}
} 