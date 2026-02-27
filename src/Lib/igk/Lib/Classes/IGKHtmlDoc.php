<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKHtmlDoc.php
// @date: 20220803 13:48:54
// @desc: 

use IGK\Constants;
use IGK\Css\CssThemeOptions;
use IGK\Helper\Activator;
use IGK\IGKHtmlDocFlagOption;
use IGK\Resources\R;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Css\CssSession;
use IGK\System\Html\Dom\GlobalScriptManagerHostNode;
use IGK\System\Html\Dom\HtmlCssLinkNode;
use IGK\System\Html\Dom\HtmlDocCoreStyle;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlDocumentNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlSingleNodeViewerNode;
use IGK\System\Html\HtmlMetaManager;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\IHtmlDocumentHost;
use IGK\System\Html\Metadatas\Traits\HtmlDocMetadataTrait;
use IGK\System\Http\CookieManager;
use IGK\System\Http\IHeaderResponse;
/**
 * create core document
 * @package IGK
 */
class IGKHtmlDoc extends HtmlDocumentNode implements IHeaderResponse, IHtmlDocumentHost
{

    /**
    * Property: privatetheme.
    * @var mixed
    */
    private $m_privatetheme;

    /**
    * Property: theme.
    * @var mixed
    */
    private $m_theme;

    /**
    * Property: baseuri.
    * @var mixed
    */
    private $m_baseuri;

    /**
    * Cache: no cache.
    * @var mixed
    */
    private $m_noCache;

    /**
    * Property: no core script.
    * @var mixed
    */
    private $m_noCoreScript;

    /**
    * Flag: can add.
    * @var mixed
    */
    private $m_can_add;

    /**
    * Property: no powered.
    * @var mixed
    */
    private $m_noPowered;

    /**
    * Property: no core css.
    * @var mixed
    */
    private $m_noCoreCss;

    /**
    * Property: no font install.
    * @var mixed
    */
    private $m_noFontInstall;

    /**
    * Property: no iframe.
    * @var mixed
    */
    private $m_noIFrame;
    use HtmlDocMetadataTrait;
    /**
     * set flag option 
     * @param mixed|IGKHtmlDocFlagOption $o 
     * @return static 
     * @throws IGKException 
     * @throws Exception 
     */

    public function setFlagOption($o)
    {
        if (($r = Activator::CreateNewInstance(IGKHtmlDocFlagOption::class, $o))
            instanceof IGKHtmlDocFlagOption
        ) {
            foreach ($r as $k => $v) {
                $this->{$k} = $v;
            }
        }
        return $this;
    }

    /**
    * Getno iframe.
    */
    public function getnoIframe()
    {
        return $this->m_noIFrame;
    }

    /**
    * Sets No Iframe.
    * @param bool $value
    */
    public function setNoIframe(bool $value)
    {
        $this->m_noIFrame = $value;
        return $this;
    }
    /**
     * set website icons 
     * @var string $href reference
     * @var string expected type 
     * */

    public function setWebSiteIcon(string $href, $type='png'){
        if (is_null($this->m_site_icon)){
            $ln = $this->head()->link();
            $ln['rel'] = 'website icon';
            $ln['type'] = $type;
        }
        $ln['href'] = $href;
        return $this;
    }

    /**
    * Getno font install.
    */
    public function getnoFontInstall()
    {
        return $this->m_noFontInstall;
    }

    /**
    * Setno font install.
    * @param null|bool $value
    */
    public function setnoFontInstall(?bool $value)
    {
        $this->m_noFontInstall = $value;
        return $this;
    }

    /**
    * Sets Is Template.
    * @param null|bool $value
    */
    public function setIsTemplate(?bool $value)
    {
        $this->isTemplate = $value;
        return $this;
    }

    /**
    * Returns Is Template.
    */
    public function getIsTemplate()
    {
        if (property_exists($this, "isTemplate"))
            return $this->isTemplate;
        return null;
    }

    /**
    * Getno core script.
    */
    public function getnoCoreScript()
    {
        return $this->m_noCoreScript;
    }

    /**
    * Setno core script.
    * @param null|bool $n
    */
    public function setnoCoreScript(?bool $n)
    {
        $this->m_noCoreScript = $n;
        return $this;
    }

    /**
    * Returns No Core Css.
    */
    public function getNoCoreCss()
    {
        return $this->m_noCoreCss;
    }

    /**
    * Sets No Core Css.
    * @param null|bool $value
    */
    public function setNoCoreCss(?bool $value = null)
    {
        $this->m_noCoreCss = $value;
        return $this;
    }

    /**
    * Returns No Powered.
    */
    public function getNoPowered()
    {
        return $this->m_noPowered;
    }

    /**
    * Sets No Powered.
    * @param null|bool $value
    */
    public function setNoPowered(?bool $value = null)
    {
        $this->m_noPowered = $value;
        return $this;
    }
    /**
     * get if current document is system document
     * @return bool 
     * @throws IGKException 
     */

    public function getIsSysDoc()
    {
        return $this === igk_app()->getDoc();
    }

    /**
    * Returns No Cache.
    */
    public function getNoCache()
    {
        return $this->m_noCache;
    }

    /**
    * Sets No Cache.
    * @param bool $value
    */
    public function setNoCache(bool $value)
    {
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
    * Property: status.
    * @var mixed
    */
    private $m_status;
    /**
     * store global themes
     * @var mixed
     */
    private static $sm_theme;

    /**
    * Property: script manager.
    * @var mixed
    */
    private static $sm_scriptManager;

    /**
    * Name of namespaces.
    * @var mixed
    */
    protected $namespaces = [
        "xmlns" => "http://www.w3.org/1999/xhtml",
        "xmlns:igk" => HtmlNode::HTML_NAMESPACE
    ];

    /**
    * Constant: igk doc favicon flag.
    * @var mixed
    */
    const IGK_DOC_FAVICON_FLAG = self::IGK_DOC_TYPE_FLAG + 3;

    /**
    * Constant: igk doc linkmanager flag.
    * @var mixed
    */
    const IGK_DOC_LINKMANAGER_FLAG = self::IGK_DOC_TYPE_FLAG + 2;

    /**
    * Constant: igk doc loaded script flag.
    * @var mixed
    */
    const IGK_DOC_LOADED_SCRIPT_FLAG = self::IGK_DOC_TYPE_FLAG + 4;

    /**
    * Constant: igk doc metamanager flag.
    * @var mixed
    */
    const IGK_DOC_METAMANAGER_FLAG = self::IGK_DOC_TYPE_FLAG + 5;

    /**
    * Constant: igk doc scriptmanager flag.
    * @var mixed
    */
    const IGK_DOC_SCRIPTMANAGER_FLAG = self::IGK_DOC_TYPE_FLAG + 6;

    /**
    * Constant: igk doc title flag.
    * @var mixed
    */
    const IGK_DOC_TITLE_FLAG = self::IGK_DOC_TYPE_FLAG + 1;

    /**
    * Constant: igk doc type flag.
    * @var mixed
    */
    const IGK_DOC_TYPE_FLAG = 0xB01;
    /**
     * set color scheme
     * @param 'dark'|'light'|null $cl 
     * @return $this 
     * @throws Exception 
     * @throws IGKException 
     */

    public function setColorScheme($cl)
    {
        $this->_storeMeta('color-scheme', [
            HtmlMetaManager::ATTR_CONTENT => $cl
        ], $cl !== null);
        return $this;
    }
    /**
     * helper store meta
     * @param mixed $id 
     * @param mixed $value 
     * @param bool $store 
     * @return $this 
     * @throws Exception 
     * @throws IGKException 
     */
    private function _storeMeta($id, $value, bool $store)
    {
        $sm = $this->getMetas();
        $meta = $sm->getMetaById($id);
        if ($store) {
            if (!$meta) {
                $meta = array_merge(['name' => $id], $value);
            } else {
                $meta = array_merge($meta, $value);
            }
            $sm->addMeta($id, $meta);
        } else {
            if ($meta) {
                $meta->remove();
                igk_wln("remove .... ");
            }
        }
        return $this;
    }
    /**
     * 
     * @param mixed $cl
     */

    public function setHeaderColor($cl)
    {
        $sm = $this->getMetas();
        $n = "theme-color";
        $meta = $sm->getMetaById($n);
        if ($meta == null) {;
            $meta = igk_create_node("meta");
            $meta["name"] = $n;
            $sm->addMeta($n, $meta);
        }
        $sm->setAttribute($n, HtmlMetaManager::ATTR_CONTENT, $cl);
        return $this;
    }
    /**
     * get header color
     */

    public function getHeaderColor()
    {
        if ($g = $this->getMetas()->getMetaById("theme-color")) {
            return $g[HtmlMetaManager::ATTR_CONTENT];
        }
        return null;
    }
    /**
     * get reponse header
     * @return null|array 
     */

    public function getResponseHeaders(): ?array
    {
        return $this->m_responseHeader;
    }
    /**
     * get reponse header
     * @param null|array $headers 
     * @return self
     */

    public function setResponseHeaders(?array $headers = null)
    {
        $this->m_responseHeader = $headers;
        return $this;
    }

    /**
    * auto generate doc.
    * @param ?string $theme dark|light
    * @return $this
    */

    public function setDefaultTheme(?string $theme)
    {
        $this->m_page_theme = $theme;
        return $this;
    }

    /**
    * Returns Default Theme.
    */
    public function getDefaultTheme()
    {
        return $this->m_page_theme;
    }
    /**
     * get the meta manager object
     * @return IGK\System\Html\HtmlMetaManager
     */

    public function getMetas()
    {
        if (!($g = $this->getTempFlag(self::IGK_DOC_METAMANAGER_FLAG))) {
            $this->setTempFlag(self::IGK_DOC_METAMANAGER_FLAG, $g = new HtmlMetaManager());
        }
        return $g;
    }
    /**
     * get system theme
     * @return ?IGK\System\Html\Dom\HtmlDocTheme 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public function getSysTheme()
    {
        if (self::$sm_theme === null) {
            self::$sm_theme = new HtmlDocTheme($this, 0, "sys://document");
            HtmlUtils::InitSystemTheme(self::$sm_theme);
        }
        return self::$sm_theme;
    }

    /**
    * auto generate doc.
    * @param mixed $reset the default value is 0
    */

    public function getTemporaryCssDef($minfile, $export, $reset = 0)
    {
        igk_set_env("sys://css_temp", 1);
        $p = $this->m_privatetheme->get_css_def($minfile, $export);
        igk_set_env("sys://css_temp", null);
        if ($reset) {
            $this->m_privatetheme->resetAll();
        }
        return $p;
    }

    /**
    * auto generate doc.
    * @return ?IGK\System\Html\Dom\HtmlDocTheme
    */

    public function getTempTheme()
    {
        return $this->m_privatetheme;
    }
    /**
     * get document responset status 
     * @return ?int 
     */

    public function getResponseStatus():?int{
        return $this->m_status;
    }
    /**
     * set document responset status
     * @param null|int $code 
     * @return void 
     */

    public function setResponseStatus(?int $code ){
        $this->m_status = $code; 
        return $this;
    }
    /**
     * get document theme. depending on environment in case ops is null
     * @return mixed|IGK\System\Html\Dom\HtmlDocTheme 
     * @throws Exception
     */

    public function getTheme(?bool $ops = null)
    {
        $r = $this->m_theme;
        $ops = is_bool($ops) ? $ops : is_null($ops) && igk_environment()->isOPS();
        if ($ops) {
            $r = $this->getInlineTheme();
        }
        if ($r = (igk_get_env("sys://css_temp") ? $this->m_privatetheme : $r)) {
            return $r;
        }
        igk_die("theme not created");
    }

    /**
    * auto generate doc.
    * @return ?IGKHtmlScriptManager
    */

    public function getScriptManager(): ?IGKHtmlScriptManager
    {
        return $this->getFlag(self::IGK_DOC_SCRIPTMANAGER_FLAG);
    }
    /**
     * add script file to document
     * @param string $file fullpath to server script or uri. if null return a script node.
     * @param string $tag : tag identifier
     * @param bool $canbeMerged merge the document 
     */

    public function addScript($file = null, $tag = null, $canbeMerged = true)
    {
        return $this->ScriptManager->addScript($file, $canbeMerged, $tag);
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @return void
    */
    private function __construct($id)
    {
        $this->m_can_add = true;
        parent::__construct();
        $this->m_can_add = false;
        $this->m_id = $id;
        $this->m_theme = new HtmlDocTheme($this, "css:public");
        $this->m_privatetheme = new HtmlDocTheme($this, "css:private");
        $this->setFlag(self::IGK_DOC_SCRIPTMANAGER_FLAG, $this->prepareScriptManager());
        $this->_addCoreCss();
        $this->getHead()->add(new GlobalScriptManagerHostNode());
        $this->setup_document();
        igk_hook(IGKEvents::HOOK_NEW_DOC_CREATED, array(igk_app(), $this));
    }

    public function getCanAddChilds()
    {
        return $this->m_can_add;
    }
    /**
     * get inline theme
     * @return HtmlDocTheme 
     */

    public function getInlineTheme()
    {
        $id = spl_object_id($this);
        $key = "doc://" . $id . "/inline_theme";
        if ($theme = igk_environment()->get($key)) {
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
    private function _addCoreCss()
    {
        $key = "sys://css";
        $t = null;
        if (empty($s = igk_io_corestyle_uri())) {
            return $t;
        }
        $n =  $this->m_head;
        $g = $n->getParam($key);
        if (!$g || !isset($g[$s])) {
            $t = new HtmlDocCoreStyle($s, true, 0);
            $this->m_head->add($t);
            // + | ---------------------------------------------------
            // + | to avoid flickering FOCUS direct css access required
            // + | 
            // + | 
            // $this->m_head->add('link')->setAttributes([
            //     "rel"=>"stylesheet", 
            //     "href"=>"/assets/demo.css"
            // ]);
            $t->cache = 0;
            $g[$s] = $t;
            $n->setParam($key, $g);
        } else {
            $t = $g[$s];
        }
        return $t;
    }
    /**
     * check the presence of the favicon
     * @param mixed $icon
     */

    public function setFavicon($icon)
    {
        if ($icon) {
            if (is_object($icon)) {
                $icon = $icon->getValue();
            } else if (is_string($icon) && !empty(igk_realpath($icon))) {
                $icon = IGKResourceUriResolver::getInstance()->resolve($icon);
            } else {
                $icon = null;
            }
            $this->setFlag(self::IGK_DOC_FAVICON_FLAG, $icon);
        } else {
            $this->setFlag(self::IGK_DOC_FAVICON_FLAG, null);
        }
    }
    /**
     * set favicon as uri
     * @param string $uri 
     * @return $this 
     * @throws IGKException 
     */

    public function setFaviconURL(string $uri)
    {
        $this->setFlag(self::IGK_DOC_FAVICON_FLAG, $uri);
        return $this;
    }
    /**
     * get favicon
     */

    public function getFavicon()
    {
        return $this->getFlag(self::IGK_DOC_FAVICON_FLAG);
    }
    /**
     * 
     */
    private function prepareScriptManager()
    {
        return new IGKHtmlScriptManager();
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @return IGKHtmlDoc
    */

    public static function CreateDocument($id, ?array $init_properties = null)
    {
        $doc = new static($id);
        if ($init_properties) {
            foreach ($init_properties as $key => $value) {
                $doc->{$key} = $value;
            }
        }
        return $doc;
    }

    /**
    * Creates Core Document.
    * @param mixed $id
    */
    public static function CreateCoreDocument($id)
    {
        $document = new static($id);
        $document->registerHook();
        return $document;
    }

    /**
    * auto generate doc.
    * @return IGKHtmlDoc
    */

    public static function LastRenderedDocument()
    {
        static $sm_lastDocument;
        if ($sm_lastDocument === null) {
            $_id = igk_app()->settings->CurrentDocumentIndex;
            if ($_id === null)
                $_id = 0;
            if ($_id > 0) {
                $sm_lastDocument = new self($_id);
            } else {
                $sm_lastDocument = igk_app()->getDoc();
            }
        }
        return $sm_lastDocument;
    }
    /**
     *  initialize the private theme
     */
    private function _initThemes()
    {
        $d = igk_app()->getDoc();
        if ($d !== $this) {
            return;
        }
        $this->m_privatetheme->Name = "igk_private_theme";
        $this->m_theme->Name = "default";
        $this->m_privatetheme->resetAll();
    }
    /**
     * setup global document
     */

    protected function setup_document()
    {
        $this->_initThemes();
        $this->m_body["class"] = "igk-body";
    }

    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options = null)
    {
        igk_app()->settings->CurrentDocumentIndex = $this->getId();
        $this->m_lang = R::GetCurrentLang();
        return parent::render($options);
    }
    /**
     * register hooks
     */

    protected function registerHook()
    {
        // $v_cevent = array($this, 'setup_document');
        $v_func = function () {
            $this->setup_document();
        };
        // igk_hook(IGK_CONF_USER_CHANGE_EVENT, $v_cevent);
        igk_reg_hook(IGKEvents::HOOK_PAGEFOLDER_CHANGED, $v_func, 0);
    }
    /**
     * load temp extra script file. must be called out of rendering context. /!\\ Before
     * @var string $file file or uri
     * @var ?array $query_args extra args
     */

    public function addTempScript(string $file, ?array $query_args = null)
    {
        if (!IGKValidator::IsUri($file))
            $file = igk_dir($file);
        $t = $this->ScriptManager->getTempScripts();
        if (!igk_getv($t, "temp")) {
            $t->temp = array();
        }
        $uris = explode("?", $file, 2);
        $kfile = $file = array_shift($uris);
        if (is_file($file)) {
            $kfile = IGKResourceUriResolver::getInstance()->resolve($file);
        }
        if (isset($t->temp[$kfile])) {
            $n = $t->temp[$kfile];
            $n->setIsTemp($t);
            $this->m_head->add($n);
            return $n;
        }
        if ($uris) {
            parse_str($uris[0], $out);
            $query_args = array_merge($out, $query_args ?? []);
        }
        if ($query_args) {
            // $file .= '?' . http_build_query($query_args);
            $file = $kfile . '?' . http_build_query($query_args);
        }
        $sc = $this->m_head->addScript($file);
        $t->temp[$kfile] = $sc;
        $sc->setIsTemp($t); 
        return $sc;
    }
    /**
     * Add tempory file to temp document. \
     * Call it out of rendering context. \
     * The file will be requested with link in the header.
     * @param string $file css file to add to document
     * @return IHtmlNode style node 
     */

    public function addTempStyle(string $file)
    {
        $v_t = igk_get_env("sys://temp/css");
        if ($v_t == null) {
            $v_t = new HtmlSingleNodeViewerNode(igk_html_node_notagnode());
        }
        if (is_link($file) || ($file == realpath($file))) {
            $file = IGKResourceUriResolver::getInstance()->resolve($file);
        }
        $ln = $this->_addStyle($v_t->targetNode, $file);
        igk_set_env("sys://temp/css", $v_t);
        $this->m_head->add($v_t);
        return $ln;
    }
    /**
     * clear component list
     */

    public function clearComponents()
    {
        $this->m_components = array();
    }
    /**
     * clear style 
     */

    public function clearStyle()
    {
        $v_childs = array();
        foreach ($this->m_head->Childs as $k => $v) {
            if (get_class($v) == HtmlCssLinkNode::class) {
                $v_childs[] = $v;
            }
        }
        foreach ($v_childs as $k) {
            $this->m_head->remove($k);
        }
    }

    /**
    * auto generate doc.
    */

    public function getHtmlOptions()
    {
        $c = $this->m_params;
        $ns = "doc://html";
        if ($c)
            return igk_array_to_obj($c, $ns);
        return $c;
    }
    /**
     * get doctype
     */

    public function getDocType()
    {
        !($d = $this->getFlag(self::IGK_DOC_TYPE_FLAG)) && ($d = IGK_DOC_TYPE);
        return "<!DOCTYPE " . $d . ">";
    }

    /**
    * auto generate doc.
    */

    public function getNameSpace()
    {
        return Constants::NAMESPACE;
    }

    /**
    * auto generate doc.
    */

    public function Dispose()
    {
        $this->clearComponents();
        parent::Dispose();
    }
    /**
     * retrieve document style list
     */

    public function getStylesList()
    {
        $v_childs = array();
        foreach ($this->m_head->Childs as  $v) {
            if (get_class($v) == HtmlCssLinkNode::class) {
                $v_childs[] = $v;
            }
        }
        return $v_childs;
    }
    private function _initializedocument()
    {
        die(__("Not implement : {0}", __METHOD__));
    }
    /**
     * file : relative path to file according to system base dir
     */

    public function addStyle(string $file, $system = false)
    {
        return $this->_addStyle($this->m_head, $file, $system);
    }
    /**
     * append style to head node
     * @param HtmlNode $n node that will receive the style tag
     * @param string $file system file path 
     * @param mixed $system the default value is false
     */

    protected function _addStyle($n, string $file, $system = false)
    {
        $g = $n->getParam("sys://css");
        if ($g == null) {
            $g = array();
        }
        if (realpath($file)) {
            $file = IGKResourceUriResolver::getInstance()->resolve($file, ["hashed" => 1]);
        }
        if (isset($g[$file])) {
            return $g[$file];
        }
        $ln = new HtmlCssLinkNode($file, $system);
        $n->add($ln);
        $g[$file] = $ln;
        $n->setParam("sys://css", $g);
        return $ln;
    }
    /**
     * set document description.
     * @param mixed $value 
     * @return $this 
     * @throws Exception 
     * @note : for CEO description must be [50, 170] character. 
     */

    public function setDescription($value)
    {
        $this->getMetas()->setDescription($value);
        return $this;
    }
    /**
     * set document keywords
     * @param mixed $value 
     * @return $this 
     * @throws Exception 
     */

    public function setKeywords($value)
    {
        $this->getMetas()->setKeywords($value);
        return $this;
    }

    /**
    * auto generate doc.
    */

    public function setCanonical(?string $uri)
    {
        $v_tlinks = $this->getHead()->getElementsByTagName('link');
        $v_can = null;
        foreach ($v_tlinks as $k) {
            if ($k['rel'] == 'canonical') {
                $v_can = $k;
                break;
            }
        }
        if (is_null($uri) && $v_can) {
            $v_can->remove();
        } else if (is_null($v_can)) {
            $v_can = $this->getHead()->add('link');
            $v_can['rel'] = 'canonical';
            $v_can['href'] = $uri;
        }
        return $this;
    }
    /**
     * get the base uri
     * @return mixed 
     */

    public function getBaseUri()
    {
        return $this->m_baseuri;
    }
    /**
     * set the base uri 
     * @param mixed $baseuri 
     * @return $this 
     */

    public function setBaseUri($baseuri)
    {
        $this->m_baseuri = $baseuri;
        return $this;
    }
    /**
     * set view port definition 
     * @param string $portdefinition 
     * @return $this 
     * @throws Exception 
     */

    public function setViewport(string $portdefinition){
        $this->getMetas()->setAttribute(HtmlMetaManager::META_VIEWPORT,'content', $portdefinition);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $name
    * @return mixed|array
    */

    public function getElementsByTagName($name, bool $stop_first = false)
    {
        $n = strtolower($name);
        $tab = array();
        if ($n == "head") {
            $tab[] = $this->m_head;
            return $tab;
        }
        if ($n == "body") {
            $tab[] = $this->m_body;
            return $tab;
        }
        $tab1 = $this->m_head->getElementsByTagName($name, $stop_first);
        $tab2 = $this->m_body->getElementsByTagName($name, $stop_first);
        $h = array_merge($tab1, $tab2);
        return $h;
    }

    /**
    * Header extra attribute.
    */
    public function headerExtraAttribute()
    {
        $attr = "";
        $theme_name = $this->m_page_theme ??
            CssSession::getInstance()->theme_name ??
            CookieManager::getInstance()->get(CssSession::APP_THEME_NAME) // came for browser             
            ?? CssThemeOptions::DEFAULT_THEME_NAME;
        if ($theme_name)
            $attr .= "data-theme=\"" . $theme_name . "\" ";
        if ($this->m_dir) {
            $attr .= "dir=\"" . $this->m_dir . "\" ";
        }
        return trim($attr);
    }
}