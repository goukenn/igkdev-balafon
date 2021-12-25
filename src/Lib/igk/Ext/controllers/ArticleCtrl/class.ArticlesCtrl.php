<?php

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