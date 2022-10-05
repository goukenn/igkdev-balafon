<?php
// @author: C.A.D. BONDJE DOUE
// @filename: bgctrl.class.php
// @date: 20220803 13:48:58
// @desc: 



//
//BACKGROUND IMAGE ADORATOR
//
// final class IGKBackgroundImgCtrl extends ConfigControllerBase
// {
		// private $m_imglist;
		// private $m_configTargetNode;
		// private $m_currentIndex;
		// private $m_allowRandom;
		// private $m_lastChanged;
		// const PROP_CHANGED = "BackgroundAdoratorChanged";
		// public function __construct()
		// {
			// parent::__construct();
			// $this->m_currentIndex = null;
			// $this->m_imglist = array();
		// }
		// private function _getadoratordir()
		// {
			// return  igk_io_syspath("R/Img/".(($this->App->Configs->adorator_fade_directory)?$this->App->Configs->adorator_fade_directory:"Adorator"));
		// }
		// ///
		// public function IsFunctionExposed($function){//explose all public function
			// return true;
		// }


		// public function SettingChanged($ctrl)
		// {
			// if ($ctrl->isChanged(self::PROP_CHANGED, $this->m_lastChanged))
			// {
				// $this->View();
			// }
		// }
	

		// public function getConfigPage()
		// {
			// return "backgroundsetting";
		// }
		// private function __viewConfig()
		// {
				// $this->m_configTargetNode->clearChilds();
				// $this->ConfigNode->clearChilds();
			 	// $d = $this->m_configTargetNode;
				// igk_html_add_title($d, "title.BackgroundAdoratorSetting");
				// $d->addHSep();
				// $frm = $d->addForm();
				// $frm["action"] = $this->getUri("updatevalue");
				// $ul = $frm->add("ul");

				// $ul->li()->addSLabelCheckbox("clEnabled",  $this->App->Configs->adorator_fade_enabled);
				// $ul->li()->addSLabelInput("clFadeinterval","text", igk_gettv($this->App->Configs->adorator_fade_interval , 500));
				// $ul->li()->addSLabelInput("clRotationInterval", "text", igk_gettv($this->App->Configs->adorator_rotation_interval, 10000));
				// $ul->li()->addSLabelCheckbox("clRandom", $this->App->Configs->adorator_random_enabled);
				// $frm->addHSep();
				// $ul = $frm->add("ul");
				// $ul->li()->addSLabelInput("clDir", "text", $this->App->Configs->adorator_fade_directory?$this->App->Configs->adorator_fade_directory:"Adorator" );
				// $frm->addBtn("btn_update", R::ngets("btn.update"));
		// }
	// public function showConfig(){
		// parent::showConfig();
		// if ($this->App->CurrentPageFolder == IGK_CONFIG_MODE)
		// {
			// $this->__viewConfig();
		// }
		// else {
			// igk_html_rm($this->m_configTargetNode);
		// }
	// }
		// public function updatevalue()
		// {
			// $this->App->Configs->adorator_fade_directory =   igk_getr("clDir","Adorator");
			// $this->App->Configs->adorator_fade_enabled = igk_getr("clEnabled",false);
			// $this->App->Configs->adorator_fade_interval = igk_getr("clFadeinterval");
			// $this->App->Configs->adorator_rotation_interval = igk_getr("clRotationInterval");
			// $this->App->Configs->adorator_random_enabled = igk_getr('clRandom');
			// igk_getctrl(IGK_CHANGE_MAN_CTRL)->registerChange(self::PROP_CHANGED, $this->m_lastChanged);
			// igk_save_config();
			// $this->showConfig();
			// igk_notifyctrl()->addMsgr("msg.backgroundimageadorator.updated");
			// $this->View();
			// igk_navtocurrent();

		// }
		// public function getIsVisible()
		// {
			// return $this->App->Configs->adorator_fade_enabled;
		// }
		// public function getfilelist_ajx()
		// {
				// $s = $this->_getadoratordir();
				// if (is_dir($s))
				// {
				// $tab = igk_io_getfiles($s);
				// $node =  igk_create_node("images");
				// foreach($tab as $k){
					// $node->add("image", array("src"=>igk_io_baseUri(igk_io_basePath($k))));
				// }
				// igk_wl($node->render());
				// }
		// }
		// public function View()
		// {
			// $c = $this->TargetNode;
			// $c->clearChilds();
			// if (!$this->getIsVisible())
			// {

				// igk_html_rm($c);
				// return;
			// }

			// $c["class"]="web_background_img posab zback loc_t loc_l fith fitw";
 
			// //default picture
			// $c->add("div", 	array("class"=>"front posab loc_t loc_l fitw fith"));//->Content = "nbsp;";

	// $out = IGK_STR_EMPTY;
	// $s = $this->_getadoratordir();

	// $tab = igk_io_getfiles($s);

	// if ($tab && count($tab>0)){
			// $script = $c->addScript();

	// $afi = $this->App->Configs->adorator_fade_interval;
	// $ari = $this->App->Configs->adorator_rotation_interval;
	// $rnd = igk_parsebool( $this->App->Configs->adorator_random_enabled? true: false);
	// $defindex = -1;
	// if ($this->m_currentIndex)
// {
	// $defindex = $this->m_currentIndex;
// }
			// $p =<<<EOF
// igk.animation.InitBgAdorator(igk.getParentScriptByTagName('div'), 20, $ari, $afi,$rnd,  '{$this->getUri('getfilelist_ajx')}',{$defindex}, '{$this->getUri('updateindex_ajx&index=')}');
// EOF;
				// $script->Content = $p;

		// }
		// }


	// public function updateindex_ajx()
	// {
		// $this->m_currentIndex = igk_getr("index");
		// $this->View();
		// igk_exit();
	// }

// } 