<?php

namespace IGK\System\Html\Dom;

use IGK\System\Polyfill\ArrayAccessSelfTrait;
use ArrayAccess;
use IGKCssDefaultStyle; 
use IGK\System\Html\Dom\HtmlDocThemeMediaType;
use IGKMedia;
use IGKOb;
use IGKObjectGetProperties; 
use IGKHtmlDoc;
use IGK\Css\ICssStyleContainer;
///<summary>represent a document themes</summary>
/**
* represent a document themes
*/
final class HtmlDocTheme extends IGKObjectGetProperties implements ArrayAccess , ICssStyleContainer{
    const REGKEY="HtmlDocTheme";
    private $m_document;
    private $m_def;
    private $m_id;
    private $m_medias;
    private $m_type;
    private $m_istemp;
    private static $MEDIA;
    private static $SM_MEDIAKEY;

    use  ArrayAccessSelfTrait; 

    /**
     * parent of this theme
     * @var mixed
     */
    var $parent;
    
    ///<summary></summary>
    ///<param name="document"></param>
    ///<param name="id"></param>
    ///<param name="type" default="global"></param>
    /**
    * 
    * @param mixed $document
    * @param mixed $id
    * @param mixed $type the default value is "global"
    */
    public function __construct($document, $id, $type="global"){
        $this->m_id=$id;
        $this->m_document=$document;
        $this->m_type=$type;
        $this->m_istemp = false;
        $this->_initialize();
    }
	public static function CreateTemporaryTheme($id){
        $c = new HtmlDocTheme(null, $id);
        $c->m_istemp = true;
		return $c;
    }
    public function getIsTemp(){
        return $this->m_istemp;
    }
    public function setColors($color){
        if (is_string($color)){
            $color = explode('|', $color);
        }
        $cl = & $this->getCl(); 
        $cl = array_unique(array_filter(array_merge($cl, $color)));
    }
    public function resetColors(){
        $cl = & $this->getCl(); 
        $cl = []; 
    }
    ///<summary>display value</summary>
    /**
    * display value
    */
    public function __toString(){
        return "HtmlDocTheme : [id:".$this->m_id.", type: {$this->m_type} ]";
    }
    public function bindFile($file){
        igk_css_bind_file(null, $file, $this); 
    }
    ///<summary>convert data to array</summary>
    /**
     * convert data to array
     * @return mixed 
     */
    public function to_array(){
        $out = $this->m_def->to_array();  
        $medias =  [];
        foreach($this->m_medias as $id=>$m){
            $def = $m->to_array();
            if (count($def)==0)
                continue;
            //if ($id = $m->getId()){
                $medias[$id] = $def;
            // }
            // else 
            //     $medias[] = $def;
        }
        if (0 != count($medias)){
            $out["medias"] = $medias;
        }   
        return $out;
    }

    ///<summary>load userialize data to to thme</summary>
    /**
     * load userialize data to to thme
     * @param array $data 
     * @return false 
     */
    public function load_data(array $data){
       
        $this->m_def->load_data($data);
        if ($medias = igk_getv($data, "medias")){
            //$this->m_medias = $medias;
            foreach($medias as $id=>$m){
                $v_m = igk_getv($this->m_medias, $id);
                if ($v_m){
                    $v_m->load_data($m);
                }
            }
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="indent" default="true"></param>
    ///<param name="themeexport" default="false"></param>
    ///<param name="doc" default="null"></param>
    /**
    * 
    * @param mixed $indent the default value is true
    * @param mixed $themeexport the default value is false
    * @param mixed $doc the default value is null
    */
    private function _get_css_def($indent=true, $themeexport=false, $systheme=null){
        $lineseparator=$indent ? IGK_LF : IGK_STR_EMPTY;
        $out=IGK_STR_EMPTY;
        $def=$this->def;
        $colors=$this->cl;
        $fonts=$def->getFont();
        $res=$this->res;
        $ft_def="";
        $tv=0;
        $s=""; 
        if($systheme===null){
            $systheme = igk_app()->getDoc()->getSysTheme();
        }
        $s=$def->getSymbols();
        if(is_array($s)){
            $v_cacherequire=igk_sys_cache_require();
            $tb=array();
            foreach($s as $k=>$v){
                if(file_exists($k)){
                    $rk=igk_realpath($k);
                    if($v_cacherequire){
                        $tb[]="./".igk_html_uri(igk_io_basepath($k));
                    }
                    else
                        $tb[]=igk_io_fullpath2fulluri($k);
                }
            }
            $ks=igk_str_join_tab($tb, ',', false);
            $out .= ".igk-svg-symbol-lists:before{content:'$ks'} ".$lineseparator;
        }
        if(igk_css_design_mode()){
            $v_var_def="";
            foreach($colors as $k=>$v){
                if(empty($v)){
                    $v="initial";
                }
                else if(preg_match("/\{(?P<name>(.)+)\}/i", $v, $tab)){
                    $v="var(--igk-cl-".$tab["name"].")";
                }
                $v_var_def .= "--igk-cl-".$k.":".$v.";".$lineseparator;
            }
            $tc=$this->properties;
            foreach($tc as $k=>$v){
                if(empty($v)){
                    $v="initial";
                }
                $v_var_def .= "--igk-prop-".$k.":".$v.";".$lineseparator;
            }
            if(!empty($v_var_def))
                $out .= ":root{".$v_var_def."}";
        }
        if($fonts){
            $ft_def="";
            foreach($fonts as $k=>$v){
                if(!$v)
                    continue;
                $tv=1;
                $s .= igk_css_get_fontdef($k, $v, $lineseparator);
                $v_def=null;
                if(isset($v->Def)){
                    $v_def=", ".$v->Def;
                }
                $ft_def .= ".ft-".$k." { font-family: \"$k\"{$v_def}; }".$lineseparator;
            }
            if($tv)
                $out .= "/* <!-- Fonts --> */".$lineseparator.$s.$ft_def;
        }
        if($def->getHasRules()){
            $out .= "/* <!-- Rules --> */".$lineseparator;
            $out .= $def->getRulesString($lineseparator, $themeexport, $systheme).$lineseparator;
            $out .= "/* <!-- end:Rules --> */\n";
        }
        
        $s="";
        $tv=0;
        if($attr=$def->getAttributes()){
            foreach($attr as $k=>$v){
                if (empty($v))
                    continue;
                $kv=trim(igk_css_treat($this, $v, $systheme));               
                if(!empty($kv)){
                    $s .= $k."{".$kv."}".$lineseparator;
                    $tv=1;
                }
            }
        }
        if($tv){
            $out .= "/* <!-- Attributes --> */".$lineseparator;
            $out .= rtrim($s).$lineseparator;;
            $out .= "/* <!-- end:Attributes --> */".$lineseparator;
        }
        $res=$this->res;
        if(!$themeexport){
            if($res && ($attr=$res->Attributes)) foreach($attr as $k=>$v){
                $out .= ".".$k."{background-image: url('../Img/".$v."');}".$lineseparator;
            }
        }
        $tab=$this->Append;
        if($tab && igk_count($tab) > 0){
            $keys=array_keys($tab);
            $out .= IGK_START_COMMENT." APPEND THEME ".IGK_END_COMMENT.IGK_LF;
            igk_usort($keys, "igk_key_sort");
            foreach($keys as $k){
                $v=$tab[$k];
                $kv=trim(igk_css_treat($this, $v, $systheme));
                if(!empty($kv)){
                    if(strpos($k, "#")===0)
                        $out .= $k."{".$kv."}".$lineseparator;
                    else
                        $out .= ".".$k."{".$kv."}".$lineseparator;
                }
            }
        }
        $ktemp=IGK_CSS_TEMP_FILES_KEY;
        $v_csstmpfiles=$def->getTempFiles();
      
        if(count($v_csstmpfiles) > 0){
            if(!igk_get_env($ktemp) && $v_csstmpfiles){
                igk_set_env($ktemp, 1);



                $vtemp = HtmlDocTheme::CreateTemporaryTheme("theme://inline/tempfiles");
                foreach($v_csstmpfiles as $k){
                    $k = igk_io_expand_path($k);
                    IGKOb::Start();
                    igk_css_bind_file(null, $k, $vtemp);
                    $m=IGKOb::Content();
                    IGKOb::Clear();
                    $h=$vtemp->get_css_def($indent, $themeexport);
                    if(igk_is_debug()){
                        $out .= "\n/TempFileLoading: *".igk_io_basepath($k)."*/\n";
                    }
                    $out .= $h;
                    if(!empty($m)){
                        $out .= $m;
                    }
                    $vtemp->resetAll();
                }
                igk_set_env($ktemp, null);
            }
        }
        return $out;
    }
    ///<summary></summary>
    /**
    * int theme
    */
    private function _initialize(){
		if ($this->m_document === null){
			$tab = [];
			$this->def = new IGKCssDefaultStyle($tab);
			$this->_initMedia($this->m_id);
			return;
		}
        ( ($cl = get_class($this->m_document)) !=IGKHtmlDoc::class)  && igk_die("class [".$cl."] not allowed\n ") ;
        $tab = null;
        $id = $this->m_document->getId();
        $app_info = igk_app()->settings->appInfo;
        $docs = null;
        $themes = null;
        if ($app_info){

            $docs = & $app_info->documents[$id];
            
            if ($docs===null){
                $docs = [];
                // igk_wln_e(__LINE__.":".__FILE__, igk_app()->settings->appInfo);
                $app_info->documents[$id] = & $docs;
                //die("no setting for id : ".$id);
            }
            if (!isset($docs["theme"])){
                
                $tab = [];
                $docs["theme"] = & $tab;
                $tab[$this->m_id] = [];
                $tab = & $tab[$this->m_id];
                $themes = & $docs["theme"];
            }else {
                $themes = & $docs["theme"];
                if (!isset($themes[$this->m_id])){
                    $themes[$this->m_id] = [];
                    
                    $docs["theme"] = & $themes;
                }
                $tab = & $themes[$this->m_id];
            }
        }
        $this->def = new IGKCssDefaultStyle($tab);
        $this->m_files=array();
        $this->m_medias=array();
        $this->m_mediasid=array();
        $this->Append=$this->add("AppendCss");
        $this->_initMedia($this->m_id);
    }
    ///<summary></summary>
    ///<param name="id"></param>
    /**
    * 
    * @param mixed $id
    */
    private function _initMedia($id){
        if(!(strpos($id, "media:")===0)){
            $this->reg_media("(max-width:".(IGK_CSS_XSM_SCREEN)."px)", HtmlDocThemeMediaType::XSM_MEDIA, "xsm");
            $this->reg_media("(min-width:".(IGK_CSS_XSM_SCREEN + 1)."px) and (max-width:".IGK_CSS_SM_SCREEN."px)", HtmlDocThemeMediaType::SM_MEDIA, "sm");
            $this->reg_media("(min-width:".(IGK_CSS_SM_SCREEN + 1)."px) and (max-width:".IGK_CSS_LG_SCREEN."px)", HtmlDocThemeMediaType::LG_MEDIA, "lg");
            $this->reg_media("(min-width:".(IGK_CSS_LG_SCREEN + 1)."px) and (max-width:".IGK_CSS_XLG_SCREEN."px)", HtmlDocThemeMediaType::XLG_MEDIA, "xlg");
            $this->reg_media("(min-width:".(IGK_CSS_XLG_SCREEN + 1)."px)", HtmlDocThemeMediaType::XXLG_MEDIA, "xxlg");
            $this->reg_media("(min-width:".(IGK_CSS_XSM_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_XSM_MEDIA);
            $this->reg_media("(min-width:".(IGK_CSS_SM_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_SM_MEDIA);
            $this->reg_media("(min-width:".(IGK_CSS_LG_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_LG_MEDIA);
            $this->reg_media("(min-width:".(IGK_CSS_XLG_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_XLG_MEDIA);
            $this->reg_media("(min-width:855px)", HtmlDocThemeMediaType::CTN_LG_MEDIA);
            $this->reg_media("(min-width:1300px)", HtmlDocThemeMediaType::CTN_XLG_MEDIA);
            $this->reg_media("(min-width:1820px)", HtmlDocThemeMediaType::CTN_XXLG_MEDIA);
            $this->reg_media();
        }
    }
    ///<summary></summary>
    ///<param name="style"></param>
    /**
    * 
    * @param mixed $style
    */
    private function add($style){
        $tc=$this->m_tc ?? array();
        if(is_string($style) && !empty($style)){
            $n=igk_create_xmlnode($style);
            array_push($tc, $n);
            return $n;
        }
        array_push($tc, $style);
        return $style;
    }
    ///<summary></summary>
    ///<param name="cl"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $cl
    * @param mixed $value
    */
    public function addColor($cl, $value){
        $changed=false;
        if(isset($this->cl[$cl])){
            if($this->cl[$cl] != $value){
                $this->cl[$cl]=$value;
                $changed=true;
            }
        }
        else{
            $this->cl[$cl]=$value;
            $changed=true;
        }
        if($changed){
            $this->save();
        }
    }
    ///<summary>Add file to document theme</summary>
    /**
    * Add file to document theme
    */
    public function addFile($host, $f, $temp=1){
        if($host === null)
            igk_die("controller host must be defined");
        igk_css_reg_global_style_file($f, $this, $host, $temp); 
    }
    ///<summary>add font package</summary>
    /**
    * add font package
    */
    public function addFont($name, $path){
        $changed=false;
        $ft=& $this->def->getFont();
        if(isset($ft[$name])){
            $changed=$ft[$name] != $path;
            $ft[$name]=$path;
        }
        else{
            $ft[$name]=$path;
            $changed=true;
        }
    }
    ///<summary>attach tempory css file to bind</summary>
    /**
    * attach tempory css file to bind
    */
    public function addTempFile($file){
        if(!file_exists($file))
            return 0;
        $v_tfiles=& $this->m_def->getTempFiles();
        if (($g = igk_io_collapse_path($file)) && !in_array($g, $v_tfiles)){
            $v_tfiles[]= $g;
        }
        return 1;
    }
    ///<summary>add style file</summary>
    /**
    * add style file
    */
    public function addTempStyle($host, $f){
        if(!file_exists($f))
            return false;
        $key="css://temp/rendering";
        $tab=$this->getParam($key);
        if($tab === null)
            $tab=array();
        $f = igk_io_collapse_path($f);
        $tab[]=(object)array('file'=>$f, 'host'=>$host);
        $this->setParam($key, $tab);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function ClearChilds(){}
    ///<summary></summary>
    /**
    * 
    */
    public function ClearFont(){
        $tab=$this->ft->Attributes;
        if(count($tab) > 0){
            foreach($tab as  $v){
                if(is_object($v)){
                    foreach($v->Fonts as  $n){
                        $f=igk_io_basedir($n->File);
                        if(file_exists($f))
                            @unlink($f);
                    }
                }
                else{
                    $f=igk_io_basedir($v);
                    if(file_exists($f))
                        @unlink($f);
                }
            }
            $this->ft->Attributes->Clear();
            $this->save();
        }
    }
    ///<summary></summary>
    ///<param name="minfile" default="false"></param>
    ///<param name="themeexport" default="false"></param>
    ///<param name="doc" default="null"></param>
    /**
    * 
    * @param mixed $minfile the default value is false
    * @param mixed $themeexport the default value is false
    * @param mixed $doc the default value is null
    */
    public function get_css_def($minfile=false, $themeexport=false, $doc=null){
        $el=$minfile ? IGK_STR_EMPTY: IGK_LF;
        $is_root=false;
        $doc = $doc ?? $this->m_document ?? igk_app()->getDoc(); 
        
        $systheme = $doc->getSysTheme();
        $is_root = $this === $systheme;
        $parent = $is_root ? null : ( ($this->parent instanceof self) && ($this->parent !== $this) ? $this->parent : $systheme);
        \IGK\System\Diagnostics\Benchmark::mark("theme-export-def");
        $out=  $this->_get_css_def(!$minfile, $themeexport, $parent); // systheme);
        \IGK\System\Diagnostics\Benchmark::expect("theme-export-def", 0.100);
 
        if($this->m_medias) {
            $g = "";
        foreach($this->m_medias as $k=>$v){
            $g=trim($v->getCssDef($this, $minfile, $themeexport, $systheme));
            if(!empty($g)){
                $out .= "@media ".self::GetMediaName($k)."{".$el;
                if($is_root){
                    $inf=self::GetMediaClassInfo($k);
                    if(!empty($inf)){
                        $out .= $inf.$el;
                    }
                }
                $out .= $g.$el;
                $out .= "}".$el;
            }
        }
    }
        return rtrim($out);
    }
    ///<summary></summary>
    ///<param name="id"></param>
    /**
    * 
    * @param mixed $id
    */
    public function get_media($id){
        $g=null;
        if(isset($this->m_medias[$id])){
            $g=& $this->m_medias[$id];
        }
        else{
            igk_ilog("Media not found {$id}");
            header("Content-Type:text/html");
            igk_trace();
            igk_wln_e("media not found");
        }
        return $g;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getAllClassExpression(){
        $out=IGK_STR_EMPTY;
        $def=$this->def;
        $tab=igk_create_node("table");
        foreach($def->Attributes as $k=>$v){
            $r=$tab->addRow();
            $r->addTd()->Content=$k;
            $r->addTd()->Content=$v;
        }
        $out .= $tab->render();
        return $out;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getAppend(){
        return "";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getAttributes(){
        igk_die(__METHOD__.". not avaiable for theme");
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * 
    * @return mixed|array colors
    */
    public function & getCl(){
        return $this->m_def->getCl();
    }
    ///<summary>return theme extra smalll media type</summary>
    public function getxsm_screen(){
        return $this->get_media( HtmlDocThemeMediaType::XSM_MEDIA);
    }
    ///<summary>return theme smal media type</summary>
    public function getsm_screen(){
        return $this->get_media( HtmlDocThemeMediaType::SM_MEDIA);
    }
    ///<summary>return theme large media type</summary>
    public function getlg_screen(){
        return $this->get_media( HtmlDocThemeMediaType::LG_MEDIA);
    }
    ///<summary>return theme extra large media type</summary>
    public function getxlg_screen(){
        return $this->get_media( HtmlDocThemeMediaType::XLG_MEDIA);
    }
    ///<summary>return theme extra extra large media type</summary>
    public function getxxlg_screen(){
        return $this->get_media( HtmlDocThemeMediaType::XXLG_MEDIA);
    }
    ///<summary></summary>
    ///<param name="key" default="null"></param>
    /**
    * 
    * @param mixed $key the default value is null
    */
    public function getDeclaration($key=null){
        /**
         * @var object $this
         */
        $out=IGK_STR_EMPTY;
        $key=$key == null ? "\$this": $key;
        foreach($this->def->Attributes as $k=>$v){
            $out .= $key."[\"$k\"]=\"".$v."\";".IGK_LF;
        }
        foreach($this->getChilds() as $k){
            $t=strtolower($k->TagName);
            $c=false;
            switch($t){
                case "default":
                case "igk:text":
                case "":
                $c=true;
                break;default:
                $c=!preg_match(IGK_ISIDENTIFIER_REGEX, $t);
                break;
            }
            if($c)
                continue;
            $out .= "\$$k->TagName = igk_getv({$key}->getElementsByTagName(\"$k->TagName\"), 0);".IGK_LF;
            $tab=$k->Attributes;
            if($tab){
                foreach($tab as $r=>$s){
                    if(is_object($s)){
                        switch($k->TagName){
                            case "Fonts":
                            $out .= "\$$k->TagName[\"$r\"]=\"".str_replace("\\", "\\", str_replace("\"", "'", igk_css_get_fontdef($s->Name, $s)))."\";".IGK_LF;
                            break;
                        }
                        continue;
                    }
                    $out .= "\$$k->TagName[\"$r\"]=\"".str_replace("\\", "\\", str_replace("\"", "'", $s))."\";".IGK_LF;
                }
            }
        }
        return $out;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * get
    * @return mixed|array|object definition
    */
    public function & getdef(){
        return $this->m_def;
    }
    ///<summary> get the parent document</summary>
    /**
    *  get the parent document
    */
    public function getDoc(){
        return $this->m_document;
    }
    ///<summary>return registrated fontn</summary>
    /**
    * return registrated fontn
    */
    public function getFont(){
        return null;
    }
    ///<summary> return the id of the data</summary>
    /**
    *  return the id of the data
    */
    public function getId(){
        return $this->m_id;
    }
    ///<summary></summary>
    ///<param name="idk"></param>
    /**
    * 
    * @param mixed $idk
    */
    public static function GetMediaClassInfo($idk){
        if(self::$SM_MEDIAKEY == null){
            self::$SM_MEDIAKEY=[HtmlDocThemeMediaType::XSM_MEDIA=>"xsm", HtmlDocThemeMediaType::SM_MEDIA=>"sm", HtmlDocThemeMediaType::LG_MEDIA=>"lg", HtmlDocThemeMediaType::XLG_MEDIA=>"xlg", HtmlDocThemeMediaType::XXLG_MEDIA=>"xxlg", ];
        }
        $s=null;
        if(isset(self::$SM_MEDIAKEY[$idk])){
            $g=self::$SM_MEDIAKEY[$idk];
            $s=IGK_CSS_MEDIA_TYPE_CLASS. "{z-index:{$idk}; content:'{$g}'}";
        }
        return $s;
    }
    ///<summary></summary>
    ///<param name="idk"></param>
    /**
    * 
    * @param mixed $idk
    */
    public static function GetMediaName($idk){
        if(!isset(self::$MEDIA))
            self::$MEDIA=[HtmlDocThemeMediaType::XSM_MEDIA=>"(max-width:".(IGK_CSS_XSM_SCREEN)."px)", HtmlDocThemeMediaType::SM_MEDIA=>"(min-width:".(IGK_CSS_XSM_SCREEN + 1)."px) and (max-width:".IGK_CSS_SM_SCREEN."px)", HtmlDocThemeMediaType::LG_MEDIA=>"(min-width:".(IGK_CSS_SM_SCREEN + 1)."px) and (max-width:".IGK_CSS_LG_SCREEN."px)", HtmlDocThemeMediaType::XLG_MEDIA=>"(min-width:".(IGK_CSS_LG_SCREEN + 1)."px) and (max-width:".IGK_CSS_XLG_SCREEN."px)", HtmlDocThemeMediaType::XXLG_MEDIA=>"(min-width:".(IGK_CSS_XLG_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_XSM_MEDIA=>"(min-width:".(IGK_CSS_XSM_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_SM_MEDIA=>"(min-width:".(IGK_CSS_SM_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_LG_MEDIA=>"(min-width:".(IGK_CSS_LG_SCREEN + 1)."px)", HtmlDocThemeMediaType::GT_XLG_MEDIA=>"(min-width:".(IGK_CSS_XLG_SCREEN + 1)."px)", HtmlDocThemeMediaType::CTN_LG_MEDIA=>"(min-width:855px)", HtmlDocThemeMediaType::CTN_XLG_MEDIA=>"(min-width:1300px)", HtmlDocThemeMediaType::CTN_XXLG_MEDIA=>"(min-width:1820px)"];
        return igk_getv(self::$MEDIA, $idk, $idk);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getMedias(){
        return $this->m_medias;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getParam($key){ 
        return $this->getProperties($key); 
    }
    public function setParam($key, $value){
        $this->setProperty($key, $value);
        return $this;
    }
    ///<summary>get print media</summary>
    /**
    * get print media
    */
    public function getPrintMedia(){
        return $this->reg_media("print", null, 'print');
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * 
    * @return mixed|array properties
    */
    public function & getProperties($k=null){
		$g = & $this->m_def->getParams();
        if ($k){
            $g = igk_getv($g, $k);
        }
		return  $g;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getRegChangedKey(){
        return self::REGKEY."_".$this->Name;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * 
    * @return mixed|array rules
    */
    public function & getrules(){
        $q=$this;
        $sd=& $this->m_def->getRules();
        return $sd;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    /**
    * 
    * @param mixed $file
    */
    public function LoadThemeFromFile($file){
        if(file_exists($file)){
            include($file);
        }
    }

    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetExists($i):bool{
        if(isset($this->m_tc))
            return ($i>=0) && ($i < count($this->m_tc));
        return !1;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    protected function _access_offsetGet($key){
        return $this->def[$key];
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    */
    protected function _access_offsetSet($key, $value){
        if($key == "file"){
            igk_die(__METHOD__." offset is file");
        }
        $this->def[$key]=$value;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetUnset($i){
        if(isset($this->m_tc))
            unset($this->m_tc[$i]);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function output(){
        header("Content-Type:text/css");
        $s=$this->get_css_def();
        igk_wl($s);
    }
    ///<summary></summary>
    ///<param name="def"></param>
    ///<param name="name"></param>
    ///<param name="expression"></param>
    /**
    * 
    * @param mixed $def
    * @param mixed $name
    * @param mixed $expression
    */
    public function reg_keyFrames($def, $name, $expression){
        $def->addRule("@-webkit-keyframes ".$name, $expression);
        $def->addRule("@-moz-keyframes ".$name, $expression);
        $def->addRule("@-ms-keyframes ".$name, $expression);
        $def->addRule("@-o-keyframes ".$name, $expression);
        $def->addRule("@keyframes ".$name, $expression);
    }
    ///<summary>register a media </summary>
    ///<param name="$name">name or condition</param>
    /**
    * register a media
    * @param mixed $name name or condition
    */
    public function reg_media($name="print", $id=null, $display=null){
        $s="";
        $n=null;
        $doc=$this->m_document;
        $is_root=strpos($this->m_id, "sys:");
        $display=($name == 'print') && ($display == null) ? 'ptrdevice': $display;
        if(!isset($this->m_medias[$name])){
            $n=new IGKMedia("media:".$name, $display);
            $idkey=$id ?? $name;
            $this->m_medias[$idkey]=$n;
        }
        else{
            $n=$this->m_medias[$name];
        }
        return $n;
    }
    ///<summary></summary>
    ///<param name="cl"></param>
    /**
    * 
    * @param mixed $cl
    */
    public function removeColor($cl){
        if(isset($this->cl[$cl])){
            $this->cl[$cl]=null;
            $this->save();
        }
    }
    ///remove a specific font
    /**
    */
    public function removeFont($name){
        $f=$this->ft[$name];
        if($f){
            if(is_object($f)){
                $this->ft[$name]=null;
                unset($this->ft[$name]);
                $this->save();
                return true;
            }
            if(is_string($f)){
                $f=igk_io_currentrelativepath($f);
                if(file_exists($f) && (unlink($f))){
                    igk_notifyctrl()->addMsg(__("msg.fontfile.removed"));
                }
                $this->ft[$name]=null;
                $this->save();
                return true;
            }
        }
        return false;
    }
    ///<summary>reset all media</summary>
    /**
    * reset all media
    */
    public function reset($save=false){
        // igk_wln(__FILE__.':'.__LINE__);
        $this->def->Clear();
        $this->cl->Clear();
        $this->res->Clear();
        $this->ft->Clear();
        $this->properties->Clear();
        $this->rules->Clear();
        foreach($this->m_medias as $v){
            $v->Clear();
        }
        if($save)
            $this->save();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function resetAll(){

        $this->def->Clear();
        $cl=$this->def->getCl();
        $this->m_medias=array();
        $this->_initMedia($this->m_id);
    }
    ///<summary></summary>
    ///<param name="file" default="null"></param>
    /**
    * 
    * @param mixed $file the default value is null
    */
    public function save($file=null){
        if(($file == null) && empty($this->Name))
            return;
        $f=($file == null) ? igk_io_syspath(IGK_RES_FOLDER."/Themes/".$this->Name.".".IGK_DEFAULT_VIEW_EXT): $file;
        $out=IGK_STR_EMPTY;
        $out .= "<?php".IGK_LF;
        $out .= <<<EOF
// Theme Media creation
// Name : {$this->Name}
\$cl = get_class(\$this);
if (\$cl != 'HtmlDocTheme')
{
	igk_die("this file can be only included in HtmlDocTheme context");
}
EOF;
        $out .= $this->getDeclaration();
        $out .= IGK_START_COMMENT."media properties ".IGK_END_COMMENT.IGK_LF;
        foreach($this->m_medias as $k=>$v){
            $out .= "\$media = igk_getv(\$this->m_medias, '$k');".IGK_LF;
            $out .= "if (\$media){ ".IGK_LF;
            $out .= $v->getDeclaration("\$media");
            $out .= "}".IGK_LF;
        }
        $result=igk_io_save_file_as_utf8($f, $out, true);
        return $result;
    }
    ///protected the access to allow parent or child call via calluser func
    /**
    */
    protected function setdef(? IGKCssDefaultStyle $v){
        if($v === null){
            igk_die("/!\\ bad ? ".($v === null), __METHOD__);
        }
        $this->m_def=$v;
    }
    ///set properties
    /**
    */
    public function setProperty($name, $value){
        $p=& $this->m_def->getParams();
        $p[$name]=$value;
    }
 
}
