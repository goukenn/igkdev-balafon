<?php
// @file: class.IGKGoogleFontConfiguration.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\WinUI\Menus\MenuItem;

use function igk_resources_gets as __;
///<summary>represent google's font configuration layer</summary>
/**
* represent google's font configuration layer
*/
final class IGKGoogleFontConfiguration extends ConfigControllerBase{
    ///<summary></summary>
    /**
    * 
    */
    protected function getConfigFile(){
        return igk_io_dir($this->getDataDir()."/google.".IGK_CTRL_CONF_FILE);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigGroup(){
        return "google";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigPage(){
        return "google.fonts";
    }
    ///<summary></summary>
    /**
    * 
    */
    private function getfontlist(){
        $r=igk_google_settings();
        $fonts=igk_conf_get($r, "fonts");
        $t=(array)($fonts);
        return array_keys($t);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getName(){
        return "com.igkdev.googlefont";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function initConfigMenu(){
        return array(
            (new MenuItem($this->ConfigPage,
            $this->ConfigPage,
            $this->getUri("showConfig")))->setGroup($this->ConfigGroup)
        );
    }
    ///<summary></summary>
    /**
    * 
    */
    public function install(){
        session_write_close();
        extract(igk_getrs("family", "size"));
		$k = 0;

		if(!empty($family))
        $k = igk_google_installfont($family, $size);

		if (igk_is_ajx_demand()){
			if ($k)
				igk_ajx_toast(__("font installed"), "success");
			else{
				igk_ajx_toast(__("font not installed"), "danger");
			}
		}
        $this->showConfig();
        igk_ajx_redirect();
        igk_navto_referer();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function showConfig(){
        parent::showConfig();
        $cnf = $this->getConfigNode();

        $box = $cnf->addPanelBox();
        // $box->div()->Content = "OK ".igk_env_count(__FILE__);
        // $box->div()->obdata(function(){
        //     igk_trace();
        // });
		// $box->div()->setClass("igk-title-4")->setStyle("line-height:1; margin-bottom:1em")->Content = __("Google Setss tings");

        $box= $cnf->addPanelBox();
        $box->div()->h2()->Content = "Google's Font Setting";
        // $box->panelbox()->Content = "...";
        $box->panelbox()->Content = "use <code class=\"dispib\"> igk_google_addfont(\$doc, \$name)</code> to add google's inline font";
        igk_css_reg_global_tempfile(dirname(__FILE__)."/Styles/google.font.css");

        $box->ctrlview("fontsettings", $this, ['fontlist'=>$this->getfontlist()]);


    }
	public function resave(){
		igk_google_store_setting();
	}
}
