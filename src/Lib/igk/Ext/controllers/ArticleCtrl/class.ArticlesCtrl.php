<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.ArticlesCtrl.php
// @date: 20220803 13:48:58
// @desc: 


class IGKArticleController extends IGKAtriclesCtrlBase
{ 

	public function __construct(){
		parent::__construct();
	}
	public function getInfoCondition(){
		return $this->getArticle("condition");
	}
	public function getCookiesWarning(){
		return $this->getArticle("cookieswarning");
	}
	public function getConfidentiality(){
		return $this->getArticle("confidentiality");
	} 
} 