<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKHtmlDoc.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\System\Html\Dom\GlobalScriptManagerHostNode;
use IGK\System\Html\Dom\HtmlCssLinkNode;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDocumentNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlSingleNodeViewerNode;
use IGK\System\Html\HtmlMetaManager;
use IGK\System\Html\HtmlUtils;
use IGK\System\Http\IHeaderResponse;

/**
 * create core document
 * @package IGK
 * @property bool NoCache disable document caching
 */
class IGKHtmlDoc extends HtmlDocumentNode implements IHeaderResponse{
     
    private $m_privatetheme;
    private $m_theme;
    private $m_baseuri;
    private $m_noCache;
    /**
     * get if current document is system document
     * @return bool 
     * @throws IGKException 
     */
    public function getIsSysDoc(){
        return $this === igk_app()->getDoc();
    }
    public function getNoCache(){
        return $this->m_noCache;
    }
    public function setNoCache(bool $value){
        $this->m_noCache = $value;
        return $this;
    }
    /**
     * array to add extra reponse on rendering document
     * @var array
     */
    private $m_responseHeader;
    /**
     * the page theme name
     * @var string
     */
    private $m_page_theme;
    /**
     * document direction
     * @var string
     */
    private $m_dir;
    /**
     * store global themes
     * @var mixed
     */
    private static $sm_theme;
    private static $sm_scriptManager;

    protected $namespaces = [
        "xmlns"=>"http://www.w3.org/1999/xhtml",
        "xmlns:igk"=> HtmlNode::HTML_NAMESPACE
    ];

    const IGK_DOC_FAVICON_FLAG=self::IGK_DOC_TYPE_FLAG + 3;
    const IGK_DOC_LINKMANAGER_FLAG=self::IGK_DOC_TYPE_FLAG + 2;
    const IGK_DOC_LOADED_SCRIPT_FLAG=self::IGK_DOC_TYPE_FLAG + 4;
    const IGK_DOC_METAMANAGER_FLAG=self::IGK_DOC_TYPE_FLAG + 5;
    const IGK_DOC_SCRIPTMANAGER_FLAG=self::IGK_DOC_TYPE_FLAG + 6;
    const IGK_DOC_TITLE_FLAG=self::IGK_DOC_TYPE_FLAG + 1;
    const IGK_DOC_TYPE_FLAG=0xB01;

    ///<summary></summary>
    ///<param name="cl"></param>
    /**
    * 
    * @param mixed $cl
    */
    public function setHeaderColor($cl){
        $sm=$this->getMetas();
        $n="theme-color";
        $meta=$sm->getMetaById($n);
        if($meta == null){;
            $meta=igk_create_node("meta");
            $meta["name"]=$n;
            $sm->addMeta($n, $meta);
        }
        $sm->setAttribute($n, HtmlMetaManager::ATTR_CONTENT, $cl);
        return $this;
    }
    public function getHeaderColor(){
        if ($g = $this->getMetas()->getMetaById("theme-color")){
            return $g[HtmlMetaManager::ATTR_CONTENT];
        }
        return null;
    }
    /**
     * get reponse header
     * @return null|array 
     */
    public function getResponseHeaders(): ?array{
        return $this->m_responseHeader;
    }
    /**
     * get reponse header
     * @param null|array $headers 
     * @return self
     */
    public function setResponseHeaders(?array $headers = null){
        $this->m_responseHeader = $headers;
        return $this;
    }
    /**
     * 
     * @param string $theme dark|light
     * @return $this 
     */
    public function setDefaultTheme(string $theme){
        $this->m_page_theme = $theme;
        return $this;
    }
    public function getDefaultTheme(){
        return $this->m_page_theme; 
    }
    ///<summary>get the meta manager object</summary>
    /**
    * get the meta manager object
    * @return IGK\System\Html\HtmlMetaManager
    */
    public function getMetas(){
        if(!($g=$this->getTempFlag(self::IGK_DOC_METAMANAGER_FLAG))){
            $this->setTempFlag(self::IGK_DOC_METAMANAGER_FLAG, $g=new HtmlMetaManager());
        }
        return $g;
    }
    public function getSysTheme(){
        if (self::$sm_theme===null){
            self::$sm_theme = new HtmlDocTheme($this, 0, "sys://document");
            HtmlUtils::InitSystemTheme(self::$sm_theme);
        }
        return self::$sm_theme;
    }
    ///<summary></summary>
    ///<param name="minfile"></param>
    ///<param name="export"></param>
    ///<param name="reset"></param>
    /**
    * 
    * @param mixed $minfile
    * @param mixed $export
    * @param mixed $reset the default value is 0
    */
    public function getTemporaryCssDef($minfile, $export, $reset=0){
        igk_set_env("sys://css_temp", 1);
        $p=$this->m_privatetheme->get_css_def($minfile, $export);
        igk_set_env("sys://css_temp", null);
        if($reset){
            $this->m_privatetheme->resetAll();
        }
        return $p;
    }
 ///<summary></summary>
    /**
    * 
    */
    public function getTempTheme(){
        return $this->m_privatetheme;
    }
    ///<summary>get document theme</summary>
    /**
     * get document theme
    * @return mixed|IGK\System\Html\Dom\HtmlDocTheme 
    * @throws Exception
    */
    public function getTheme($ops=false){
        $r = $this->m_theme;
        if ($ops || igk_environment()->is("OPS")){
            $r = $this->getInlineTheme();
        }
        if($r=(igk_get_env("sys://css_temp") ? $this->m_privatetheme: $r)){
            return $r;
        }
        igk_die("theme not created");
    }
    ///<summary>script manager</summary>
    /**
    * @return IGKHtmlScriptManager 
    */
    public function getScriptManager(){
        return $this->getFlag(self::IGK_DOC_SCRIPTMANAGER_FLAG);
    }
    ///<summary>add script file to document</summary>
    /**
    * add script file to document
    * @param string $file fullpath to server script or uri. if null return a script node.
    * @param string $tag : tag identifier
    * @param bool $canbeMerged merge the document 
    */
    public function addScript($file=null, $tag=null, $canbeMerged=true){       
        return $this->ScriptManager->addScript($file, $canbeMerged, $tag);
    }
    private function __construct($id){
        parent::__construct();
        $this->m_id = $id; 
        $this->m_theme= new HtmlDocTheme($this, "css:public");
        $this->m_privatetheme= new HtmlDocTheme($this, "css:private");
        $this->m_page_theme = "dark";

        $this->setFlag(self::IGK_DOC_SCRIPTMANAGER_FLAG, $this->prepareScriptManager());
       
        $this->getHead()->add(new GlobalScriptManagerHostNode());
        $this->_addCoreCss();
        $this->setup_document();
        igk_hook(IGK_ENV_NEW_DOC_CREATED, array(igk_app(), $this));
    }

    /**
     * get inline theme
     * @return HtmlDocTheme 
     */
    public function getInlineTheme(){
        $id = spl_object_id($this);
        $key = "doc://".$id."/inline_theme";
        if ($theme = igk_environment()->get($key)){
            return $theme;
        }
        $theme = new  HtmlDocTheme(null, "css://inline_theme");
        igk_environment()->set($key, $theme);
        return $theme;
    }

    /**
     * add core style uri
     * @return mixed 
     * @throws IGKException 
     */
    private function _addCoreCss(){
        if (!empty($s = igk_io_corestyle_uri())){
            $t=$this->addStyle($s, true);
            $t->cache = 1; 
        }
        return $t;
    }
     ///<summary></summary>
    ///<param name="icon"></param>
    /**
    * check the presence of the favicon
    * @param mixed $icon
    */
    public function setFavicon($icon){
 
        if($icon){
            if(is_object($icon)){
                $icon=$icon->getValue();
            } else if (is_string($icon) && !empty(igk_realpath($icon))){
				$icon = IGKResourceUriResolver::getInstance()->resolve($icon);
			} else {
				$icon = null;
			}
            $this->setFlag(self::IGK_DOC_FAVICON_FLAG, $icon);
        }
        else{
            $this->setFlag(self::IGK_DOC_FAVICON_FLAG, null);
        }
    }
    /**
     * set favicon as uri
     * @param string $uri 
     * @return $this 
     * @throws IGKException 
     */
    public function setFaviconURL(string $uri){
        $this->setFlag(self::IGK_DOC_FAVICON_FLAG, $uri);
        return $this;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getFavicon(){
        return $this->getFlag(self::IGK_DOC_FAVICON_FLAG);
    }

    
    
    ///<summary></summary>
    /**
    * 
    */
    private function prepareScriptManager(){
        return new IGKHtmlScriptManager();
    }
    /**
     * 
     * @param mixed $id 
     * @return IGKHtmlDoc 
     * @throws IGKException 
     */
    public static function CreateDocument($id){       
		$doc= new static($id);
		return $doc;
    }
    public static function CreateCoreDocument($id){       
        $document = new static($id); 
        $document->registerHook();  
        return $document;
    }
    ///<summary>Get The last rendering document index</summary>
    ///<return type="IGKHtmlDoc" >last rendering document </return>
    /**
    * @return IGKHtmlDoc 
    */
    public static function LastRenderedDocument(){
		static $sm_lastDocument;
       
		if ($sm_lastDocument === null){
			$_id = igk_app()->settings->CurrentDocumentIndex;
            if ($_id ===null)
                $_id = 0;
			if ($_id > 0){
				$sm_lastDocument = new self($_id);
			}else {
				$sm_lastDocument = igk_app()->getDoc();
			}
		} 
       	return $sm_lastDocument;
    }

    ///<summary> initialize the private theme</summary>
    /**
    *  initialize the private theme
    */
    private function _initThemes(){
        $d = igk_app()->getDoc();
        if($d !== $this){
            return;
        } 
        $this->m_privatetheme->Name="igk_private_theme";
        $this->m_theme->Name="default";
        $this->m_privatetheme->resetAll(); 
    }

     ///<summary>setup global document</summary>
    /**
    * setup global document
    */
    protected function setup_document(){
        $this->_initThemes();
        $this->m_body["class"]="igk-body"; 
    }

    public function render($options = null){
        igk_app()->settings->CurrentDocumentIndex = $this->getId();
        return parent::render($options); 
    }
    /**
     * register hooks
     */
    protected function registerHook(){
        $v_cevent=array($this, 'setup_document');
        $v_func=function(){
            $this->setup_document();
        };
        // igk_hook(IGK_CONF_USER_CHANGE_EVENT, $v_cevent);
        igk_reg_hook(IGKEvents::HOOK_PAGEFOLDER_CHANGED, $v_func, 0);
    }

    ///<summary>load temp extra script file. must be called out of rendering context. /!\\ Before</summary>
    /**
    * load temp extra script file. must be called out of rendering context. /!\\ Before
    */
    public function addTempScript($file){
        if(!IGKValidator::IsUri($file))
            $file=igk_io_dir($file);
        $t=$this->ScriptManager->getTempScripts();
        if(!igk_getv($t, "temp")){
            $t->temp=array();
        }
        if(isset($t->temp[$file])){
            $n=$t->temp[$file];
            $n->setIsTemp($t);
            $this->m_head->add($n);
            return $n;
        } 
        $sc=$this->m_head->addScript($file); 
        $t->temp[$file]=$sc;
        $sc->setIsTemp($t);
        return $sc;
    }
     ///<summary>add tempory file to temp document. must be called out of rendering context.the file will be requested with link in the header.</summary>
    /**
    * add tempory file to temp document. must be called out of rendering context.the file will be requested with link in the header.
    */
    public function addTempStyle($file){
        $v_t=igk_get_env("sys://temp/css");
        if($v_t == null){
            $v_t=new HtmlSingleNodeViewerNode(igk_html_node_notagnode());
        }
        if ( is_link($file)  || ($file == realpath($file))){
            $file = IGKResourceUriResolver::getInstance()->resolve($file);            
        }
        $ln=$this->__addStyle($v_t->targetNode, $file);
        igk_set_env("sys://temp/css", $v_t);
        $this->m_head->add($v_t);
        return $ln;
    }
    ///<summary>clear component list</summary>
    /**
    * clear component list
    */
    public function ClearComponents(){
        $this->m_components=array();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function ClearStyle(){
        $v_childs=array();
        foreach($this->m_head->Childs as $k=>$v){
            if(get_class($v) == IGKHtmlCssLinkNode::class){
                $v_childs[]=$v;
            }
        }
        foreach($v_childs as $k){
            $this->m_head->remove($k);
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getHtmlOptions(){
        $c=$this->m_params;
        $ns="doc://html";
        if($c)
            return igk_array_to_obj($c, $ns);
        return $c;
    }
     ///<summary>get doctype</summary>
    /**
    * get doctype
    */
    public function getDocType(){
        !($d=$this->getFlag(self::IGK_DOC_TYPE_FLAG)) && ($d=IGK_DOC_TYPE);
        return "<!DOCTYPE ".$d.">";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getNameSpace(){
        return IGKConstants::NAMESPACE;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function Dispose(){
        $this->ClearComponents();
        parent::Dispose();
    }

    ///<summary>retrieve document style list</summary>
    /**
    * retrieve document style list
    */
    public function getStylesList(){
        $v_childs=array();
        foreach($this->m_head->Childs as  $v){
            if(get_class($v) == IGKHtmlCssLinkNode::class){
                $v_childs[]=$v;
            }
        }
        return $v_childs;
    }

    private function _initializedocument(){
        die(__("Not implement : {0}", __METHOD__) );
    }
     ///<summary>file : relative path to file according to system base dir</summary>
    /**
    * file : relative path to file according to system base dir
    */
    public function addStyle(string $file, $system=false){
        return $this->__addStyle($this->m_head, $file, $system);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="file"></param>
    ///<param name="system" default="false"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $file
    * @param mixed $system the default value is false
    */
    protected function __addStyle($n, string $file, $system=false){
        $g=$n->getParam("sys://css");
        if($g == null){
            $g=array();
        }
        if ($file == realpath($file)){
            $file = IGKResourceUriResolver::getInstance()->resolve($file);            
        }
        if(isset($g[$file])){
            return $g[$file];
        }
        $ln=new HtmlCssLinkNode($file, $system);
        $n->add($ln);
        $g[$file]=$ln;
        $n->setParam("sys://css", $g);
        return $ln;
    }

    public function setDescription($value){
        $this->getMetas()->setDescription($value);
        return $this;
    }
    public function getBaseUri(){
        return $this->m_baseuri;
    }
    public function setBaseUri($baseuri){
        $this->m_baseuri = $baseuri;
        return $this;
    }

    ///<summary></summary>
    ///<param name="name"></param>
    ///<return refout="true"></return>
    /**
    * 
    * @param mixed $name
    * @return mixed|array
    */
    public function getElementsByTagName($name){
        $n=strtolower($name);
        $tab=array();
        if($n == "head"){
            $tab []=$this->m_head;
            return $tab;
        }
        if($n == "body"){
            $tab []=$this->m_body;
            return $tab;
        }
        $tab1=$this->m_head->getElementsByTagName($name);
        $tab2=$this->m_body->getElementsByTagName($name);
        $h=array_merge($tab1, $tab2);
        return $h;
    }
    public function headerExtraAttribute(){
        $attr = "";
        if ($this->m_page_theme)
            $attr .= "data-theme=\"".$this->m_page_theme."\" ";
        if ($this->m_dir){
            $attr .= "dir=\"".$this->m_dir."\" ";
        }

        return trim($attr);
    }
}