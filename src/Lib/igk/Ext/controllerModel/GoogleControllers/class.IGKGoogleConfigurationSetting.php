<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKGoogleConfigurationSetting.php
// @date: 20220803 13:48:59
// @desc: 


use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\Controllers\Traits\ControllerLocationTrait;
use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;
use IGK\System\WinUI\Menus\MenuItem;

use function igk_resources_gets as __;

final class IGKGoogleConfigurationSetting extends ConfigControllerBase{
	use NoDbActiveControllerTrait;
	use ControllerLocationTrait;
	const API_KEY = "google.ApiKey";

	public function getConfigPage(){return "google.sdk";}
	public function getConfigGroup(){return "google";}
	 
	public function initConfigMenu(){
		return array(
			(new MenuItem($this->ConfigPage, $this->ConfigPage, $this->getUri("showConfig")))->setGroup($this->ConfigGroup),
		);
	}
	protected function getConfigFile()
	{
		return igk_dir(IGK_DATA_FOLDER."/google.".IGK_CTRL_CONF_FILE);
	}
	public function showConfig(){
		parent::showConfig();
		$cnf = $this->ConfigNode;
		$box = $cnf->addPanelBox();
		$box->div()->setClass("igk-title-4")->setStyle("line-height:1; margin-bottom:1em")->Content = __("Google Settings");

		$frm = $box->div()->addForm();
		$frm["action"] = $this->getUri("storeApiKey");
		$frm->add("label")->Content = __("API KEY");
		$frm->addInput("clApiKey", "text", igk_google_apikey())
		->setClass('igk-form-control igk-form-input-pwd')
		->setAttribute("placeholder", __("google api key"));
		$frm->addActionBar()->addInput("btn.valid", "submit", __("Update"))
		->setClass("igk-btn-primary");
	}
	public function storeApiKey(){
		if (!igk_is_conf_connected()){
			return;
		}
		$key = igk_getr("clApiKey");		
		igk_configs()->{self::API_KEY} = $key;
		igk_configs()->saveData();
	} 
}
