<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigControllerBase.php
// @date: 20220803 13:48:57
// @desc: 


///<summary>Represente class: ConfigControllerBase</summary>
namespace IGK\System\Configuration\Controllers;

use IGK\Controllers\BaseController;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\WinUI\Menus\MenuItem;

use IGKEvents;
use IIGKConfigController;

use function igk_resources_gets as __; 


require_once IGK_LIB_CLASSES_DIR . "/System/Configuration/Controllers/IConfigController.php";

/**
* Represente ConfigControllerBase class
*/
abstract class ConfigControllerBase extends BaseController implements IConfigController {
    public function getName()
    {
        return strtolower(static::class);
    }
    public function getUseDataSchema():bool{
        if (self::IsSysController(static::class)){
            return false;
        }
        return false;
    }
    public function getViewDir()
    {
        if (strstr($this->getDeclaredDir(), IGK_LIB_DIR)){
            return IGK_LIB_DIR."/".IGK_VIEW_FOLDER;
        }
        return parent::getViewDir();
    } 
    public function getArticlesDir()
    {
        if (strstr($this->getDeclaredDir(), IGK_LIB_DIR)){
            return IGK_LIB_DIR."/".IGK_ARTICLES_FOLDER;
        }
        return parent::getViewDir();
    } 
    public function getDataDir(){
        if (strstr($this->getDeclaredDir(), IGK_LIB_DIR)){
            return IGK_LIB_DIR."/".IGK_DATA_FOLDER;
        }
        return parent::getDataDir();
    }

    protected function getConfig($name, $default=null){         
        return igk_getv([
            "no_auto_cache_view"=>"1",
        ], $name, $this->getConfigs()->get($name, $default));
    }
  
    ///<summary></summary>
    ///<param name="node"></param>
    ///<param name="title"></param>
    /**
    * 
    * @param mixed $node
    * @param mixed $title
    */
    protected function addTitle($node, $title){
        $d=$node->div();
        $d["class"]="igk-cnf-title";
        $d->Content=__($title);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigCtrl(){
        return igk_getctrl(IGK_CONF_CTRL, false);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigNode(){
        return $this->getConfigCtrl()->getConfigNode();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigPage(){
        return "default";
    }
    ///<summary></summary>
    /**
    * 
    */
    protected function getGlobalHelpArticle(){
        return "./help/help.".$this->Name;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsConfigPageAvailable(){
        return igk_is_conf_connected();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsVisible(): bool{
        $app=igk_app();
        $cnf=$this->ConfigCtrl;
        // ($app->CurrentPageFolder == IGK_CONFIG_MODE) && 
        // $v=($cnf && ($cnf->getSelectedConfigCtrl() === $this) && 
        $v = $cnf->getIsConnected();
        return $v;
    }
    ///<summary></summary>
    /**
    * 
    */
    protected function initComplete($context=null){ 
        parent::initComplete($context);        
        if($c=$this->getConfigCtrl()){
            $c->registerConfig($this);
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function initConfigMenu(){
        if (!$this->getIsConfigPageAvailable()){
            return null;
        }
        $c=$this->getConfigPage();
        $error_msg="No config menu found for : ".$c. " = ".$this->Name. " : ".get_class($this). " ".$this->getDeclaredFileName();
        $conf=igk_get_configs_menu_settings();
        if(isset($conf->$c)){
            $cp=$conf->$c;
            if($cp){
                return array(
                    new MenuItem($cp->menuname,
                    $cp->pagename,
                    $this->getUri("showConfig"),
                    $cp->menuindex,
                    $cp->imagekey,
                    $cp->group)
                );
            }
            else{
                igk_ilog($error_msg, __METHOD__);
            }
        }
        else{
            return array(
                new MenuItem($c,
                $c,
                $this->getUri("showConfig"),
                $this->ConfigIndex ?? -1,
                $this->ConfigImageKey,
                $this->getConfigGroup())
            );
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="funcName"></param>
    /**
    * 
    * @param mixed $funcName
    */
    protected function IsFunctionExposed(string $function){
        if (!igk_is_conf_connected() || igk_configs()->get("noWebConfiguration")){
            return false;
        }
        return true; // parent::__callStatic('invokeMacros', [__FUNCTION__, $this, $function]);
    }
    ///<summary>base show Configuration of the controller</summary>
    /**
    * base show Configuration of the controller
    */
    public function showConfig(){
       
        $_t=$this->getTargetNode();
		$e_key  = "sys://config/selectedview";
        $this->ConfigCtrl->setSelectedConfigCtrl($this, get_class($this)."::showConfig");      
        if(!$this->getIsVisible()){ 
            $_t->remove();
            igk_set_env($e_key , null);
        }
        else{
            $this->View();
            if($_cnf_node=$this->getConfigNode()){ 
                $_cnf_node->clearChilds();
                $_cnf_node->add($_t);
                // $_cnf_node->div()->Content = "DEBUG ::::".get_class($this);
                igk_set_env($e_key, $this); 
            }
        }
    }
    ///<summary>used to initialize the config view node</summary>
    /**
    * used to initialize the config view node
    */
    protected function viewConfig($target, $titlekey, $descfile){
        return igk_html_ctrl_view_config($this, $target, $titlekey, $descfile);
    }
    protected function _selectConfigView($ctrl){
        igk_environment()->set('sys://config/selectedview', $ctrl);
    }
}