<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerConfigurationData.php
// @date: 20220803 13:48:57
// @desc: 
 


namespace IGK\System\Configuration;

use ArrayAccess;
use IGK\Controllers\RootControllerBase;  
use IGK\System\Html\HtmlContext;
use IGK\XML\XMLNodeType;
use IGKEnvironment;
use IGKException;
use IGKObject;
use function igk_resources_gets as __;
 
 
require_once IGK_LIB_CLASSES_DIR. "/System/Html/XML/XmlConfigurationNode.php";
require_once IGK_LIB_CLASSES_DIR. "/System/Configuration/SysConfigExpression.php";

///<summary>Controller configuration data</summary>
/**
* Controller configuration data
*/
class ControllerConfigurationData extends ConfigurationData implements ArrayAccess{
    use ConfigArrayAccessTrait; 
    private $ctrl;
    private $m_changed=0;   
    private $m_autosave; 
    private $m_secrets = [];

    /**
     * check if configuration setting is a secret
     * @param string $key 
     * @return bool 
     */
    public function isSecret(string $key) : bool{
        return key_exists($key, $this->m_secrets);
    }
    public function setAutoSave(bool $autosave){
        $this->m_autosave = $autosave;
        $this->m_change = 0;
    }
    public function to_array(){
        return (array)$this->m_configs;
    }
    public function to_json(){
        return json_encode($this->m_configs);
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
    * 
    * @param mixed $ctrl
    */
    public function __construct($ctrl){
        if(!$ctrl)
            igk_die(__("ctrl can't be null"));
 
        $this->ctrl=$ctrl;
        $this->m_changed=0;
        $this->m_autosave = true;
        $this->m_configs=igk_createobj();       
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function __get($n){
        return $this->get($n, null);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function __isset($n){
        return isset($this->m_configs->$n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        if (is_object($m = igk_conf_get($this->m_configs, $n)) && ($m instanceof SysConfigExpression)){
            if (($v!==null) && is_string($v)){
                if ($v != $m->expression){
                    $m->expression = $v;
                    $this->m_changed = 1;
                    return;
                }
            }
        }
        igk_conf_set($this->m_configs, $v, $n);
        $this->m_changed=1;
    }
    ///<summary>get configuration file</summary>
    /**
     * get configuration file
    * @return string 
    */
    public function getConfigFile(){
        return igk_dir($this->ctrl->getConfigFile());
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
    * 
    * @param mixed $t
    */
    public function initConfigSetting(\stdClass $t, $file=null){
        $f= $file ?? $this->getConfigFile();
        $def = null; 
        $v_filter_attr = function ($tab, $path){
            return array_filter($tab, function($v, $k)use($path){
                if ($k == 'secret'){
                    $this->m_secrets[$path] = 1;
                    return false;
                }
                return true;
            }, ARRAY_FILTER_USE_BOTH);
        };
        if(!is_null($f) && file_exists($f)){
            
            igk_environment()->task = 'load-config: '.$f;
            igk_environment()->loading_context =  HtmlContext::XML;
            $def = strtolower(IGKEnvironment::ResolvEnvironment(igk_server()->ENVIRONMENT));
            $confNode = new \IGK\System\Html\XML\XmlConfigurationNode("dummy-configs"); // igk_create_xmlnode("dummy-configs");             
            $confNode->loadFile($f, HtmlContext::XML, null);           
            $d=igk_getv($confNode->getElementsByTagName("config"), 0);
            if($d){
                foreach($d->Childs as $k){
                    if ($k->getNodeType() == XMLNodeType::COMMENT){
                        continue;
                    }  
                    $key = $k->TagName;
                    $secret = igk_bool($k['secret']) == true;
                    if($k->ChildCount<=0){
                        $t->{$key}=$k->getInnerHtml();
                    }
                    else{
                        $v_ob=igk_createobj();
                        igk_conf_load($v_ob, $k, $v_filter_attr);
                        $t->{$key}=$v_ob;
                    }
                    if ($secret){
                        $this->m_secrets[$key] = 1;
                    }
                }
            } 
            // | ----------------------------------------------------------
            // | UPDATE the configuration file to match allowed environment
            // | ---------------------------------------------------------- 
            if ($m = igk_getv($t, "env.".$def)){  
                foreach($m as $c=>$p){
                    if (strpos($c, "env.")===0){
                        igk_die("invalid xml configuration file env can't containt env");
                    }
                    $t->$c = $p;
                } 
            }  
        }
        if (!empty($fs = ltrim($this->ctrl->getName(), "."))){
            $fs = ".".$fs;
        }
        if(RootControllerBase::IsSystemController($this->ctrl)){
            $t->clRegisterName=IGK_DOMAIN.$fs;
        }
        else{
            if(!isset($t->clRegisterName)){
                $t->clRegisterName=igk_sys_getconfig("website_prefix", "igk").$fs;
            }
        } 
        $this->m_changed=0;
        $this->m_configs=$t;
        igk_environment()->loading_context = null;
        return $t;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function LoadSetting(){}
   
   
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    // #[\ReturnTypeWillChange()]
    // public function offsetUnset($n){
    //     unset($this->m_configs->$n);
    // }
    ///<summary>reload configuration setting</summary>
    /**
    * reload configuration setting
    */
    public function reloadConfiguration(){
        igk_die(__METHOD__." Not implement");
    }
    ///<summary> setup
    /**
    *  setup
    */
    private function setupCtrlConfigSettings(){
        igk_die(__METHOD__." Not implement");
    }
    ///<summary></summary>
    /**
    * 
    */
    public function storeConfig(){         
        $this->m_changed = 0;  
        if ($this->m_configs){
            $d = igk_createxml_config_data($this->m_configs);    
            $data = $d->render((object)[
                "Context"=>"XML",
                "Indent"=>true
            ]);
            // igk_wln_e(__FILE__.":".__LINE__, $data, $this->getConfigFile());        
            return igk_io_w2file($this->getConfigFile(), $data, true);
        }
    }
    /**
     * get will resolv the config
     * @param mixed $xpath 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public function get($xpath, $default= null){
        $v = igk_conf_get($this->m_configs, $xpath, $default);
        if (is_object($v) && ($v instanceof SysConfigExpression)){
            return $v->__toString();
        }
        return $v;
    }
}