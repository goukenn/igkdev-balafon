<?php
// @author: C.A.D. BONDJE DOUE
// @filename: R.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\Resources;
use IGK\Controllers\BaseController;
use IGK\Resources\IGKLangExpression;
use IGKObject;
use IGKAppContext;
use IGK\Resources\IGKLangKey;
use IGK\Resources\IGKLangResDictionary;
use IGK\System\Console\Logger;
use IGKAppType; 
use IGK\System\Html\HtmlReader;
use IGK\System\IO\FileSystem;
use IGKEvents;
use IGKException; 
use IGKUserAgent;
use function igk_resources_gets as __;
/**
* IGKResource Class. represent resource / lang / resource management
*/
final class R extends IGKObject {
    /**
    * Constant: default lang.
    * @var mixed
    */
    const DEFAULT_LANG="fr";
    /**
    * Property: lang changed date.
    * @var mixed
    */
    private $m_langChangedDate;
    /**
    * Property: lang files.
    * @var mixed
    */
    private $m_langFiles;
    /**
    * Property: langctrl.
    * @var mixed
    */
    private $m_langctrl;
    /**
    * Property: langloaded.
    * @var mixed
    */
    private $m_langloaded;
    /**
    * Property: instance.
    * @var mixed
    */
    static $sm_instance;
    /**
    * Property: key var.
    * @var mixed
    */
    static $sm_keyVAR;
    /**
    * Property: keys added.
    * @var mixed
    */
    var $KeysAdded;
    /**
    * Property: lang changed event.
    * @var mixed
    */
    var $LangChangedEvent;
    /**
    * Property: page lang changed event.
    * @var mixed
    */
    var $PageLangChangedEvent;
    /**
    * Property: static.
    * @var mixed
    */
    private $sm_static;
    /**
    * Constant: handler key.
    * @var mixed
    */
    const _HANDLER_KEY = __CLASS__.'@string_resource_handler';
    /**
    * Returns String Resource Handler.
    */
    public static function GetStringResourceHandler(){
        return igk_environment()->peek(self::_HANDLER_KEY);
    }
    /**
    * Sets String Resource Handler.
    * @param null|callable $v
    */
    public static function SetStringResourceHandler(?callable $v){
        if (is_null($v)){
            igk_environment()->pop(self::_HANDLER_KEY);
        }
        igk_environment()->push(self::_HANDLER_KEY, $v);
    }
    /**
     * language resources keys
     * @var mixed dictionary
     */
    var $langRes;
    /**
    * auto generate doc.
    */
    private function __construct(){}
    /**
    * set the langage key
    */
    public static function AddLang($key, $value){
        if(!empty($key))
            self::getInstance()->langRes[strtolower(trim($key))]=$value;
    }
    /**
     * get current lang locale string 
     * @return string  
     * @throws IGKException 
     */
    public static function GetLocale(){
        $n = self::GetCurrentLang();
        $c = $n;
        // igk_wln_e("locale : ", php-intl \Locale::getDisplayRegion("-be", "en"));
        if (strpos($n, "_")!==false){
            return $n;
        }
        return sprintf("%s_%s", strtolower($n), strtoupper($c));
    }
    /**
    * Localize.
    * @param string $text
    * @param null|string $locale
    * @param null|array $args
    */
    public static function Localize(string $text, ?string $locale=null, ?array $args=null){
        if (is_null($locale)){
            $locale = self::GetCurrentLang();
        }
        if ($locale != self::GetCurrentLang()){
        }
        return __($text, ...$args);
    }
    /**
    * auto generate doc.
    * @param string $locale
    * @return bool
    */
    public static function SupportLang(string $locale):bool{
        return preg_match(sprintf("/%s/i", self::GetSupportLangRegex()), $locale);
    }
    /**
     * array of supported lang
     * @return string[] 
     * @throws IGKException 
     */
    public static function GetSupportedLangs(){
        return array_filter(explode("|", self::GetSupportLangRegex()));
    }
    /**
    * auto generate doc.
    * @param fr
    */
    public static function ChangeLang($lang="fr"){
        $app=igk_app();
        $v=self::getInstance();
        if(igk_get_env($key="flag:".__FUNCTION__))
            return;
        igk_set_env($key, 1);
        $v_lang=$app->session->lang; 
        $tab = array_unique(self::GetSupportedLangs()); 
        $tc = array_combine(array_map('strtolower', $tab), $tab);
        $lang = igk_getv($tc, strtolower($lang)) ?? igk_configs()->default_lang;
        $r=false;
        if(($v_lang != $lang) && in_array($lang, $tab)){
            $app->session->lang=$lang;
            $v->m_langloaded=false;
            self::LoadLang();
            $v->onPageLangChangedEvent();
            $r=true;
        }
        igk_set_env($key, null);
        return $r;
    }
    /**
    * auto generate doc.
    */
    public static function ClearLang($autosave=true){
        $v=self::getInstance();
        $v->langRes->clear();
        if ($autosave){
            self::SaveLang(null);
        }
    }
    /**
    * auto generate doc.
    */
    public static function GetCurrentLang(){
        $lg=igk_app()->session->getLang() ?? self::GetSupportLang();
        if(empty($lg)){
            igk_die("language is empty :::".self::GetSupportLang());
        }
        return $lg;
    }
    /**
    *  get the current language file
    */
    public static function GetCurrentLangPath($folder=null){
        if($folder == null)
            $folder=IGK_LIB_DIR."/Default/Lang";
        $lg=self::GetCurrentLang();
        return $folder."/".IGK_LANG_FILE_PREFIX.$lg.IGK_LANG_FILE_EXTENSION;
    }
    /**
    * auto generate doc.
    */
    public static function GetDefaultLang(){
        $app=igk_app();
        if($lg=$app->Configs->default_lang){
            return $lg;
        }
        return self::DEFAULT_LANG;
    }
    /**
    * get resource image uri
    */
    public static function GetImgResUri($name, & $path=null, $check=true): ?string {
        $v=igk_getctrl(IGK_PIC_RES_CTRL);
        if($v){
            return $v->getImgUri($name, $check, $path);
        } 
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $name
    */
    public static function GetImgUri($name){
        $v=igk_getctrl(IGK_PIC_RES_CTRL);
        if($v){
            return $v->getImgUri($name);
        }
        return IGK_STR_EMPTY;
    }
    /**
    * auto generate doc.
    */
    public static function getInstance(){
        if(self::$sm_instance === null){
            $b=0;
            self::$sm_instance=new R();
            self::LoadLang();
        }
        return self::$sm_instance;
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public static function GetKeyValue($key){
        $v=self::getInstance();
        return igk_getv($v->langRes, $key);
    }
    /**
    *  get Language key
    */
    public static function GetLang($keys){
        return new IGKLangExpression($keys);
    }
    /**
    * auto generate doc.
    */
    public static function GetLangInfo(){
        return self::getInstance()->langRes;
    }
    /**
    * get string expression
    */
    public static function Gets(string $key){
        if(igk_current_context() == IGKAppContext::initializing){ 
            if (igk_environment()->get("app_type") == IGKAppType::balafon){            
                return Logger::Resources($key);
            }
            return $key;
        }
        $i=self::getInstance();    
        $t=$key;
        if(isset($i->langRes[$t])){
            $s=$i->langRes[$t];
            $match=array();
            $c=0;
            $c=preg_match_all("/\[(?P<value>[a-zA-Z0-9_\.]+)\]/", $s, $match);
            if($c > 0){
                for($i=0; $i < $c; $i++){
                    $ckey=$match["value"][$i];
                    if($ckey == $t)
                        continue;
                    $s=str_replace($match[0][$i], __($ckey), $s);
                }
            }
            $key=$s;
        }
        else{
            if(!empty($key)){
                $i->langRes->set($key, $key);
                $i->OnKeyAdded($key);
            }
        }
        if(func_num_args() > 1){
            return igk_str_format(...array_merge(array($key), array_slice(func_get_args(), 1)));
        }
        if (preg_match('/^(?P<n>\\w+)\\.[\\w\\d\-\_]+\\b$/', $key, $tab)){
            $n = $tab['n'];
            if ($fc = $i->handleLangPrefix($n)){
                return $fc($key);
            }
        }
        return $key;
    }
    /**
    * auto generate doc.
    * @param string $name
    * @return mixed
    */
    public function handleLangPrefix(string $name){
        $nkey = function($n){
            return explode('.', $n,2)[1];
        };
        return igk_getv([
            'enum'=>function($s){
                return substr($s, 5);
            },
            'lb'=>$nkey,
            'btn'=>$nkey,
        ], $name);
    }
    /**
    * prepare support lang
    */
    private static function GetSupportLang(){
        $lang=IGKUserAgent::GetDefaultLang();
        if(!self::ChangeLang($lang)){
            igk_app()->session->lang=$lang;
        }
        return $lang;
    }
    /**
    * auto generate doc.
    * @param mixed $key
    * @return
    */
    public static function Contains($key){ 
        return isset(self::getInstance()->langRes[$key]); 
    }
    /**
     * get support lang regex
    * @var string
    */
    public static function GetSupportLangRegex(){
        $cnf = igk_configs();
        $r = $cnf->get("support_lang", $cnf->default_lang);
        if (is_array($r)){
            return implode("|", $r);
        }
        return $r ?? 'en'; 
    }
    /**
    * auto generate doc.
    */
    private function langscript(){
        $f=igk_io_basepath("Lib/Scripts/lang/".R::GetCurrentLang().".xml");
        if(empty($f))
            return;
        $s=<<<EOF
var lang = igk.R.getLang();
var h = igk_getdir(igk_get_script_src());
if (h){
var dir = "{$f}";

igk.file.getcontents(dir, function(data){
	var d = igk.createNode("dum");
	var ts = d.setHtml(data).getElementsByTagName("string");
	for(var i = 0;i < ts.length; i++){
		 keys[ts[i].getAttribute("name")] = ts[i].innerHTML;
	}
}, true);

}
EOF;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="files" default="null"></param>
    /**
    * auto generate doc.
    * @param mixed $files the default value is null
    */
    public static function LoadCtrlLang($ctrl, $files=null){  
        if (!($ctrl instanceof BaseController)){
            return;
        }
        $v= $_instance = self::getInstance();
        $gdir=$ctrl->configDir()."/Lang";
        $f=null;
        $tfile=array();
        if($files === null){
            $f=igk_dir($v->GetCurrentLangPath($gdir));
            $tfile[]=$f;
        }
        else{
            foreach($files as $c){
                $sfile=igk_dir($gdir."/".$c);
                if((!igk_io_file_exists($sfile)) && !igk_io_file_exists($sfile=self::GetCurrentLang().IGK_LANG_FILE_EXTENSION)){
                    continue;
                }
                $tfile []=$sfile;
            }
        } 
        while($f=array_shift($tfile)){
            if(self::Exists($f) && !isset($_instance->m_langFiles[$f])){
                $v->langRes->load($f);
                $_instance->m_langFiles[$f]=1;
            }
            else{
                $lang=igk_dir($gdir."/".R::GetCurrentLang().".xml");
                if(self::Exists($lang)){
                    R::LoadLangFileXml($lang);
                    $_instance->m_langFiles[$f]=1;
                }
            }
        }
    }
    /**
    * Exists.
    * @param string $file
    */
    static function Exists(string $file){
        return FileSystem::Exists($file);
    }
    /**
     * reload language definition 
     * @return void 
     */
    public static function Reload(){
        self::getInstance()->m_langloaded = false;
        self::LoadLang();
    }
    ///<summary></summary>
    /**
    * auto generate doc.
    */
    public static function LoadLang(){
        $v=self::getInstance();
        if($v->m_langloaded){
            igk_ilog(" lang already loaded ", __METHOD__);
            return;
        }
        $v->langRes=new IGKLangResDictionary();
        $v->m_langloaded=false;        
        $f=$v->GetCurrentLangPath();
        if(self::Exists($f)){
            $v->langRes->load($f);     
        }
        else{
            if(!igk_sys_env_production()){
                igk_ilog("Language file not found. -- [{$f}] -- ", __METHOD__);
            }
        }
        if($v->m_langctrl){
            foreach($v->m_langctrl as $k=>$vc){
                self::LoadCtrlLang($vc);
            }
        }
        $v->m_langloaded=true;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    /**
    * auto generate doc.
    * @param mixed $file
    */
    public static function LoadLangFiles($file){
        if(igk_io_file_exists($file)){
            $v=self::getInstance();
            $l=$v->langRes;
            include($file);
            $v->langRes=$l;
        }
    }
    ///<summary></summary>
    ///<param name="file"></param>
    ///<param name="override" default="true"></param>
    /**
    * auto generate doc.
    * @param mixed $override the default value is true
    */
    public static function LoadLangFileXml($file, $override=true){
        if(!igk_io_file_exists($file))
            return;
        $t=HtmlReader::LoadFile($file);
        $h=igk_getv($t->getElementsByTagName("resources"), 0);
        if($h){
            $g=self::getInstance();
            $tab=$h->getElementsByTagName("string");
            foreach($tab as  $v){
                $n=strtolower($v["name"]);
                $r=trim($v->getInnerHtml());
                if(!empty($n) && ($override || !isset($g->langRes[$n]))){
                    $g->langRes[$n]=$r;
                }
            }
        }
    }
    ///<summary>get new key string value from controller</summary>
    /**
    * get new key string value from controller
    */
    public static function ncgets($ctrl, $key){
        if(empty($key) || ($ctrl == null))
            return null;
        return self::ngets(strtolower($ctrl->getName().".".$key));
    }
    ///<summary>get new language expression</summary>
    /**
    * get new language expression
    */
    public static function ngets($key){
        if($key == null)
            return null;
        if(!is_string($key)){
            return $key;
        }
        if(self::$sm_keyVAR == null)
            self::$sm_keyVAR=array();
        $T=strtolower($key);
        $default=self::Gets($T);
        $args=array();
        if(func_num_args() > 1){
            $t=func_get_args();
            for($i=1; $i < count($t); $i++){
                if(($i == 1) && is_array($t[$i])){
                    $args=array_merge($args, $t[$i]);
                }
                else
                    $args[]=$t[$i];
            }
            return new IGKLangKey($key, $default, $args);
        }
        else{
            if(isset(self::$sm_keyVAR[$T])){
                return self::$sm_keyVAR[$T];
            }
            else{
                self::$sm_keyVAR[$T]=new IGKLangKey($key, $default, $args);
                return self::$sm_keyVAR[$T];
            }
        }
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * auto generate doc.
    * @param mixed $key
    */
    protected function OnKeyAdded($key){
        igk_hook("LangKeyAdded", func_get_args());
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * auto generate doc.
    * @param mixed $key
    */
    protected function OnLangChangedEvent($key){        
        igk_hook(IGKEvents::HOOK_LANG_CHANGED, []);
    }
    ///<summary></summary>
    /**
    * auto generate doc.
    */
    public function onPageLangChangedEvent(){
        if($this->PageLangChangedEvent != null){
            $this->PageLangChangedEvent->Call($this, null);
        }
    }
    ///<summary>register a language controller</summary>
    /**
    * register a language controller
    */
    public static function RegLangCtrl($ctrl){
        $_instance=self::getInstance();
        if($_instance->m_langctrl == null)
            $_instance->m_langctrl=array();
        if($_instance->m_langFiles == null)
            $_instance->m_langFiles=array();
        if($ctrl && !BaseController::IsSystemController($ctrl) && !isset($_instance->sm_langctrl[ $name = $ctrl->getName()])){
            $_instance->m_langctrl[$name]=$ctrl;
            if($_instance->m_langloaded){
                self::LoadCtrlLang($ctrl); 
            }
        }
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
    * auto generate doc.
    * @param mixed $name
    */
    public static function RemoveKey($name){
        $name=strtolower($name);
        $v=self::getInstance();
        if(isset($v->langRes[$name])){
            unset($v->langRes[$name]);
            return true;
        }
        return false;
    }
    ///<summary></summary>
    /**
    * auto generate doc.
    */
    public static function ResetLang(){
        $v=self::getInstance();
        $v->langRes=array();
    }
    ///<summary></summary>
    /**
    * auto generate doc.
    */
    public static function SaveLang(){
        $instance=self::getInstance();
        $out="<?php \n//Balafon Generated language file ".IGK_LF;
        $tab=$instance->langRes;
        if(is_null($tab)){
            self::LoadLang();
            $tab=& $instance->langRes; 
            if (is_array($tab)){
                ksort($tab);
            }
        }
        $ktab= is_array($tab) ? array_keys($tab) : $tab->sortKeys();
        if($ktab){
            foreach($ktab as $k){
                $k=trim($k);
                $v=$tab[$k];
                $v=str_replace("\'", "'", str_replace("\"", "&quot;", $v));
                if(!empty($k)){
                    $out .= "\$l[\"".strtolower($k)."\"]=\"".$v."\";".IGK_LF;
                }
            }
        }
        $file=$instance->GetCurrentLangPath();
        if(igk_io_w2file($file, $out, true)){
            igk_sys_regchange("LangChanged", $instance->m_langChangedDate);
            $instance->OnLangChangedEvent(null);
            return true;
        }
        else{
            igk_notify_error("can't save lang file");
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="filename" default="null"></param>
    /**
    * auto generate doc.
    * @param mixed $filename the default value is null
    */
    public static function SaveLangXml($filename=null){
        $q=self::getInstance();
        $out=igk_create_node("resources");
        $tab=$q->langRes;
        $ktab=array_keys($tab);
        igk_usort($ktab, "igk_key_sort");
        foreach($ktab as $k){
            $v=$tab[$k];
            $v=str_replace("\'", "'", str_replace("\"", "&quot;", $v));
            $v=str_replace("<", "&lt;", $v);
            $v=str_replace(">", "&gt;", $v);
            $v=preg_replace_callback("/([&](.){0,1})/i", function($m){
                if(trim($m[0]) == "&"){
                    return "&amp;";
                }
                return $m[0];
            }
            , $v);
            if(!empty($k))
                $out->add("string")->setAttribute("name", $k)->Content=$v;
        }
        $file=$filename == null ? igk_io_basedir(IGK_RES_FOLDER."/Lang/".self::GetCurrentLang().".xml"): $filename;
        if(igk_io_save_file_as_utf8($file, $out->render(), true)){
            self::LoadLang();
            igk_sys_regchange("LangChanged", $q->m_langChangedDate);
            $q->OnLangChangedEvent(null);
            return true;
        }
        else{
            igk_notify_error("can't save lang file");
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
    * auto generate doc.
    * @param mixed $ctrl
    */
    public static function UnRegLangCtrl($ctrl){
        $_instance=self::getInstance();
        if($_instance->m_langctrl == null)
            $_instance->m_langctrl=array();
        if(isset($_instance->m_langctrl[$ctrl->getName()]))
            unset($_instance->m_langctrl[$ctrl->getName()]);
    }
    /**
     * include language regex
     * @param string $filename,
     * @param array options
     */
    public static function IncludeLang(){
        if (func_num_args()!=2){
            igk_die("expected 2 argument");
        }
        extract((array)func_get_arg(1));
        $l = & func_get_arg(1)->l;
        include(func_get_arg(0));
        return $l;
    }
}