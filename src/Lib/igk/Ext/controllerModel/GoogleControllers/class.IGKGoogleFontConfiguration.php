<?php
// @file: class.IGKGoogleFontConfiguration.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Resources\R;
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
        return igk_dir($this->getDataDir()."/google.".IGK_CTRL_CONF_FILE);
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
        if (!$this->getIsVisible()){
            return;
        }
        $lang = [];
        $lang["fr"]["code"]="Utilisez <code class=\"dispib\"> igk_google_addfont(\$doc, \$name)</code> pour inscrire la police de google dans le project";
        $lang["fr"]['moreinfo']="pour plus d'information visité le site <a href='https://fonts.google.com/' title='google font'>https://fonts.google.com/</a>";
        $lang["en"]["code"]="use <code class=\"dispib\"> igk_google_addfont(\$doc, \$name)</code> to add google's inline font";
        $lang["en"]['moreinfo']="For more information please visit <a href='https://fonts.google.com/' title='google font'>https://fonts.google.com/</a>";

        $lkey = R::GetCurrentLang();
        if (!in_array($lkey, array_keys($lang))){
            $lkey = igk_configs()->get('default_lang', 'en');
        }

        // $this->_selectConfigView($this);
        $t = $this->getTargetNode();
        $box = $this->getConfigNode()->panelbox();
        $box->add($t);
        $t->h2()->Content = __("Google's Font Setting"); 
        $t->div()->Content = $lang[$lkey]['code'];
        $t->div()->Content = igk_getv($lang[$lkey], 'moreinfo');
        igk_css_reg_global_tempfile(dirname(__FILE__)."/Styles/google.font.css");
        if ($ftlist = $this->getfontlist()){
            $t->div()->setClass('googel-install-ft')->article($this, $this->getDeclaredDir()."/Articles/fontsettings.template", ['fontlist'=>$ftlist]);
            // $t->div()->Content = $this->getDeclaredDir("fontsettings.template");

        }
    }
	public function resave(){
		igk_google_store_setting();
	}
}
