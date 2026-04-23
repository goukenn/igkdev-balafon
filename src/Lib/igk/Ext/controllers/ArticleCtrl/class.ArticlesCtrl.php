<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.ArticlesCtrl.php
// @date: 20220803 13:48:58
// @desc:

/**
* Igkarticle controller.
*/
class IGKArticleController extends IGKAtriclesCtrlBase
{ 
	/**
	 * Constructor.
	 */
	public function __construct(){
		parent::__construct();
	}
	/**
	 * Returns the article for the terms and conditions section.
	 *
	 * @return mixed
	 */
	public function getInfoCondition(){
		return $this->getArticle("condition");
	}
	/**
	 * Returns the article for the cookies warning section.
	 *
	 * @return mixed
	 */
	public function getCookiesWarning(){
		return $this->getArticle("cookieswarning");
	}
	/**
	 * Returns the article for the confidentiality section.
	 *
	 * @return mixed
	 */
	public function getConfidentiality(){
		return $this->getArticle("confidentiality");
	}
} 