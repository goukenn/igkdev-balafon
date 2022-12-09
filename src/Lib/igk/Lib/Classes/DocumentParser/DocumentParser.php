<?php
// @author: C.A.D. BONDJE DOUE
// @file: DocumentParser.php
// @date: 20221129 10:45:00
namespace IGK\DocumentParser;

use Exception;
use IGK\Controllers\BaseController;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\XML\XmlNode;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGK\System\Uri;
use IGKException;
use IGKResourceUriResolver;
use IGKValidator;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\DocumentParser
 */
class DocumentParser
{
    private $m_node;
    private $m_errors = [];
    private $m_stylesheets = [];
    private $m_scripts = [];
    private $m_metas = [];
    private $m_imgs = [];
    private $m_inline_scripts = [];
    private $m_resources = [];
    private $m_res_downloaded = [];
    private $m_title;
    private $m_refer_domains = [];
    private $m_failed = [];

    const REF_TAG = 'link|title|a|script|meta|object|img|video|audio|source|base';
 
    /**
     * target domain 
     * @var string
     */
    var $domain;
    /**
     * referrer controller
     * @var BaseController
     */
    var $controller;
    /**
     * asset dir
     * @var ?string
     */
    var $asset_dir;
    /**
     * enable or not root definition
     * @var false
     */
    var $rootdef = false;

    /**
     * set document stant alone 
     * @var true
     */
    var $standalone = true;
    /**
     * get loaded style sheet
     * @return array 
     */
    public function getStyleSheets()
    {
        return $this->m_stylesheets;
    }
    /**
     * get the list of external refer domain
     * @return array 
     */
    public function getReferDomains()
    {
        return $this->m_refer_domains;
    }
    /**
     * get parsing errors
     * @return array 
     */
    public function getErrors()
    {
        return $this->m_errors;
    }
    /**
     * get loaded scripts
     * @return array 
     */
    public function getScripts()
    {
        return $this->m_scripts;
    }
    /**
     * get resolved images
     * @return array 
     */
    public function getImages(){
        return $this->m_imgs;
    }
    /**
     * get document meta
     * @return array 
     */
    public function getMetas()
    {
        return $this->m_metas;
    }
    /**
     * get the document title
     * @return ?string 
     */
    public function getTitle()
    {
        return $this->m_title;
    }

    public function __construct()
    {
    }
    public function parse(string $content): bool
    {
        if (is_null($this->asset_dir) && $this->controller) {
            $gs = IGKResourceUriResolver::getInstance()->resolveOnly($this->controller->getAssetsDir());
            $this->asset_dir =  '/' . igk_str_rm_start($gs, '../');
        }
        if (is_null($this->m_node)) {
            $this->m_node = igk_create_notagnode();
        }
        $this->m_node->load($content);

        $this->m_node->getElementsByTagName(function ($n) {
            $tn = $n->getTagName();
            if ($tn && preg_match("/^(".self::REF_TAG.")$/i", $tn)) {
                if (method_exists($this, $fc = 'visit_' . $tn)) {
                    $this->$fc($n);
                }
                return true;
            }
            return false;
        });
        return true;
    }
    /**
     * render the parse options 
     * @param mixed $options 
     * @return mixed 
     */
    public function render($options =null ){
        return $this->m_node ? $this->m_node->render($options) : null;
    }
    /**
     * clear setting
     * @return void 
     */
    public function clear(){
        if ($this->m_node){
            $this->m_node->clear();
        }
        $this->m_errors = [];
    }
    protected function visit_base($b){
        $b['href'] = "./";
        $b->setIsVisible(false);
    }
    protected function visit_title($title)
    {
        $g = $title->getContent();
        $this->m_title = $g;
    }
    protected function visit_source($img)
    {
        if ($src = $img['srcset']) {
            $this->_bind_source($img, $src);
        }
    }
    private function _get_resource_uri($uri)
    {
        if (IGKValidator::IsUri($uri)) {
            return $uri;
        }
        return $this->domain . "/" . ltrim($uri);
    }
    private function _bind_source($img, $src)
    {
        $tab = explode(",", $src);
        $c = 0;
        $input = [];
        while (count($tab) > 0) {
            $q = array_shift($tab);
            $r = explode(" ", trim($q));
            $cp = $this->_get_resource_uri(trim($r[0]));
            $this->m_imgs[$cp] = $cp;
            $c++;
            $input[] = implode(' ', [$cp, igk_getv($r, 1)]);
        }
        if ($c == 1)
            $this->_update_attrib($img, 'srcset', $cp, $this->asset_dir . '/img/', '/img/');
        else {
            $src = implode(', ', $input);
            $g = str_replace($this->domain . "/img/", $this->asset_dir . '/img/', $src);
            $img['srcset'] = $g;
        }
    }
    private function _isInSameDomain($domain, $v){
        return  igk_get_domain_name($v) == igk_get_domain_name($domain);
    }
    /**
     * update href attribute
     * @param mixed $t target node 
     * @param mixed $a attribute
     * @param mixed $v value
     * @param mixed $outdir 
     * @param mixed $prefix 
     * @return void 
     */
    private function _update_attrib($t, $a, $v, $outdir, $prefix)
    {
        if ($this->domain) {
            // + check if is in the same domain
            if ($this->_isInSameDomain($this->domain, $v)){
            // $v_d = igk_get_domain_name($v);
            // $v_d = igk_get_domain_name($this->domain);

            // if (strpos($v, $this->domain) === 0) {
                $l =  $outdir . ltrim($this->_getPath($v, $prefix), '/');
                $t[$a] = $l;
            }
        }
    }
    /**
     * visit image tag
     */
    protected function visit_img($img)
    {
        if ($src = $img['src']) {
            $this->m_imgs[$src] = $src;
            $this->_update_attrib($img, 'src', $src, rtrim($this->asset_dir, '/') . '/img/', '/img/');
        }
        if ($src = $img['srcset']) {
            $this->_bind_source($img, $src);
        }
    }
    /**
     * visit link tab
     * @param mixed $link 
     * @return void 
     */
    protected function visit_link($link)
    {
        switch ($link['rel']) {
            case 'stylesheet':
            case 'icon':
            case 'shortcut icon':
            case 'apple-touch-icon':
                $href = $link['href'];
                $q = parse_url($href);
                if (!isset($q['host'])) {
                    $href = Path::Combine($this->domain , $href);
                }
                $type = $link['rel'];
                $this->m_stylesheets[] = (object)[
                    'type' => $type,
                    'href' => $href,
                    'source'=>$link
                ];
                break;
        }
        $this->_integrity_check($link);
    }
    /**
     * 
     * @param HtmlNode $link 
     * @return void 
     */
    protected function visit_script($link)
    {
        if (!empty($src = $link['src'])) {
            $v_is_uri = IGKValidator::IsUri($src);
            if ($this->controller) {
                if (!$v_is_uri || $this->_isInSameDomain($this->domain, $src)){
                    $link['src'] = new ReferenceAssetHandle($src, $this->controller);
                } else if ($v_is_uri){
                    // output uri non in domain 
                    $src = ["source"=>$src, "script"=>$link]; 
                }
            }
            $this->m_scripts[] = $src;
        } else {
            $this->m_inline_scripts[] = $link->getContent();
        }
        $this->_integrity_check($link);
     
    }
    private function _integrity_check($link){
        if ($i = $link['integrity']){
            $link['integrity'] = new ReferenceAttributeHandle($i);
        }
        if ($i = $link['crossorigin']){
            $link['crossorigin'] = new ReferenceAttributeHandle($i);
        }
    }
    private function _getGC($outdir, ?string $file=null){
        $gc = null;
        if ($this->controller){
            $gc = $this->asset_dir ?? $this->controller->uri("/assets");
            if (($gc == './')&&($file))
                $gc = $this->_getRelativePathFromFile($file, $outdir);
        }
        return $gc;
    }
    private function _replaceUriContent(string $src, string $outdir){
        $uri = new Uri($src);
        $gc = $this->_getGC($outdir);
        $src = str_replace($uri->getSiteUri(), Path::Combine($gc, sha1($uri->getDomain())), $src);
        return $src;
    }

    protected function visit_meta($meta)
    {
        // $content = $link['content'];
        $property = $meta['property'];
        $name = $meta['name'];
        $content = $meta['content'];
        if ($content && IGKValidator::IsUri($content) && $this->_isInSameDomain($this->domain, $content)){
            if ($this->controller){
                $uri = new Uri($content);
                $l = str_replace($uri->getSiteUri(), $this->controller->uri(''), $content);
                $meta['content'] = $l;
            }else{
                $this->_update_attrib($meta, 'content', $content, $this->asset_dir, null);
            }
        }
        if (!empty($name)) {
            if (!isset($this->m_metas['names'])) {
                $this->m_metas['names'] = [];
            }
            $this->m_metas['names'][$name] = $meta->getAttributes()->to_array();
        } else if (!empty($property)) {
            if (!isset($this->m_metas['properties'])) {
                $this->m_metas['properties'] = [];
            }
            $this->m_metas['properties'][$property] = $meta->getAttributes()->to_array();
        }
    }
    private function _getPath($uri, ?string $prefix = null)
    {
        $q = parse_url($uri);
        $path = $q['path'];
        if ($prefix)
            $path = igk_str_rm_start($path, $prefix);
        return $path;
    }
    /**
     * 
     * @param string $outdir 
     * @param array $v_download 
     * @param string $v_base_host 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _exportStyleSheets(string $outdir, array &$v_download, string $v_base_host)
    {
        Logger::info("get stylesheets...");
        $v_detector = new UriDetector;
        $dir = $this->routedef ? "" : "/css/";
        foreach ($this->m_stylesheets as $tm) {
            $m = $tm->href;
            if (isset($v_download[$m])) {
                continue;
            }
            if (strpos($m, "//") === 0) {
                $m = "https:" . $m;
            }

            if ($c = igk_curl_post_uri($m)) {
                $q = parse_url($m);
                $path = $q['path'];
                $path = ltrim(igk_str_rm_start($path, "/css/"), '/');
                if ($tm->type == 'stylesheet') {
                    $uri_host = igk_getv($q, 'host');
                    if ($uri_host && !$this->_isInSameDomain($uri_host, $v_base_host)) { 
                        $v_uri = new Uri($m);
                        $file = self::_getOutdir($outdir, $v_base_host, $v_uri->getSiteUri()) . "/" . $path;
                    } else {
                        $file = Path::Combine(...array_filter([$outdir, $dir, ltrim($path, '/')]));
                    }
                    // igk_dev_wln("file :".$file);
                    if (igk_io_path_ext($file) != 'css') {
                        $file .= ".css";
                        $tm->href .= '.css';
                    }
                    if ($tm->source ){
                        // update asset referencence
                        $this->_update_asset_ref($tm->source, 'href', substr($file, strlen($outdir))); 
                    }

                    // + | --------------------------------------------------------------------
                    // + | some style sheet contains external resource
                    // + |
                    if ($uris = $v_detector->cssUrl($c)){
                        $tp = dirname($file);
                        $this->_replaceCssContent($file, $c, $uris, $v_base_host, $tp);
                        $v_baseuri = dirname($m);
                        while (count($uris) > 0) {
                            $uri_m = array_shift($uris);
                            // foreach ($uris as $uri_m) {
                            $v_base_path = null;
                            $v_uri_base_uri = $uri_m->getBaseUri();
                            $baseuri =  $v_uri_base_uri ?? $v_baseuri;
                            // relove uri
                            if ((strpos($uri_m->path, '../') === 0) || (strpos($uri_m->path, './') === 0)) {
                                // relative to dirname 
                                $mp = $this->_getOutdir($outdir, $v_base_host, $v_uri_base_uri) ?? $tp;
                                $ck = $uri_m->path;
                                while (strpos($ck, '../') === 0) {
                                    $ck = substr($ck, 3);
                                    $mp = dirname($mp);
                                    $baseuri = dirname($baseuri);
                                }
                                $v_uri = $baseuri . "/" . $ck;
                                $v_base_path = igk_getv(parse_url($baseuri), "path");
                                // $gp =  igk_io_flatten($gp);
                                // $tgp =  igk_io_flatten($tgp);
                                $this->_downloadResource($v_uri, $mp . "/", $v_download, $v_base_path);
                            } else {
                                $g = parse_url($uri_m->path);
                                $uri_host = igk_getv($g, 'host');
                                $v_output = $tp . "/";
                                $v_prefix = null;
                                if ($uri_host && ($uri_host != $v_base_host)) { 
                                    $v_goutdir = self::_getOutdir($outdir, $v_base_host, $uri_m->path);
                                    if (is_null($v_goutdir)){
                                        igk_die("dir is null : ".$v_goutdir);
                                    }
                                    $v_output = dirname($v_goutdir);
                                    $v_base_path = dirname(igk_getv(parse_url($uri_m->path), "path"));
                                    // igk_wln_e("inline ...");
                                } else {
                                    //local uri 
                                    if (strpos($uri_m->path, '/') === 0) {
                                        $v_rpath = parse_url($baseuri);
                                        $uri_host = igk_getv($v_rpath, 'host');
                                        if (!$uri_host) {
                                            $v_output = $outdir;
                                            $uri_m->path = $this->domain . "/" . ltrim($uri_m->path, '/');
                                        } else {
                                            $v_tburi = new Uri($baseuri);
                                            $baseuri = $v_tburi->getSiteUri();
                                            $uri_m->path = $baseuri . "/" . ltrim($uri_m->path, '/');
                                            $v_base_path = igk_getv($v_rpath, "path");
                                            $v_output = $outdir . $dir;
                                            $v_prefix = "/css/";
                                        }
                                    } else {
                                        igk_dev_wln_e("not found....", $uri_m->path);
                                    }
                                }
                                if ($this->_downloadResource($uri_m->path, $v_output . "/", $v_download, $v_base_path, $v_prefix, null, $file)) {
                                    $content_type = igk_getv($this->m_last_info, CURLINFO_CONTENT_TYPE);
                                    if ($content_type && (strpos($content_type, "text/css") !== false)) {
                                        // + | get other link to download 
                                        // $v_dom = new Uri($uri_m->path);

                                        $v_last_content = file_get_contents($file);
                                        if ($v_last_content  && ($v_tcs_uris = $v_detector->cssUrl($v_last_content, $uri_m->path))) {
                                            array_unshift($uris, ...$v_tcs_uris);
                                            // + | replace expected content with local service 
                                            $this->_replaceCssContent($file, $v_last_content, $v_tcs_uris, $v_base_host, $outdir);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        igk_io_w2file($file, $c);
                    }
                    $this->m_resources[] = $file;
                } else {
                    $file = $outdir . "/" . ltrim($path, '/');
                    // if (igk_io_path_ext($file)!='css'){
                    //     $file .= ".css";
                    //     $tm->href .= '.css';
                    // }
                    if ($tm->source ){
                        // update asset referencence
                        $this->_update_asset_ref($tm->source, 'href', substr($file, strlen($outdir))); 
                    }
                    $this->m_resources[] = $file;
                    igk_io_w2file($file, $c);
                }
                $v_download[$m] = 1;   
            } else {
                // igk_dev_wln_e("failed ", $m);
                Logger::danger("faile " . $m);
                $tm->failed = 1;
            }
        }
    }
    protected function _update_asset_ref($node, $attribute, string $path){
        $node[$attribute] = rtrim($this->asset_dir,'/')."/".ltrim($path, "/");
    }
    /**
     * replace css content base uri - and store file
     * @param mixed $file 
     * @param mixed $v_last_content 
     * @param mixed $v_tcs_uris 
     * @param mixed $v_base_host 
     * @param mixed $outdir 
     * @return void 
     * @throws IGKException 
     */
    private function _replaceCssContent($file, $v_last_content, $v_tcs_uris, $v_base_host, string $outdir)
    {
        $rp = "";
        while (count($v_tcs_uris) > 0) {
            $q = array_shift($v_tcs_uris);
            if ($q->domain) {
                $uri = new Uri($q->path);
                $gc = null;
                $site_uri = $uri->getSiteUri();
                if ($this->controller){
                    $gc = $this->asset_dir ?? $this->controller->uri("/assets");
                    if ($gc == './')
                        $gc = $this->_getRelativePathFromFile($file, $outdir);
                }
                if (empty($gc)){
                    $gc = './';
                }
                if ($v_base_host!=$q->domain){
                    $rp = Path::Combine($gc, sha1($q->domain));                    
                } else {
                    $rp =  rtrim($gc, "/")."/";
                }
                $v_last_content = str_replace($site_uri, $rp, $v_last_content);
            }
        }
        igk_io_w2file($file, $v_last_content);        
    }
    private function _getRelativePathFromFile($file, $outdir){
        if (strstr($file, $outdir)){
            $p = dirname($file);
            $q = "";
            while($p != $outdir){
                $q.="../";
                $p = dirname($p);
                if ($p == '/'){
                    break;
                }
            }
            return $q;
        }
    }
    /**
     * export asset to 
     * @param string $outdir 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function exportTo(string $outdir)
    {
        Logger::info("get images...");
        $v_base_host = igk_getv(parse_url($this->domain), 'host');
        $v_download = [];
        foreach ($this->m_imgs as $m) {
            if (isset($v_download[$m])) {
                continue;
            }
            $mp = $outdir . "/img/";
            $this->_downloadResource($m, $mp, $v_download, null, '/img/');
        }
        Logger::success("-done");
        $this->_exportStyleSheets($outdir, $v_download, $v_base_host);
        Logger::success("-done");
        $this->_exportScripts($outdir, $v_download, $v_base_host);
        Logger::success("-done");
    }
    /**
     * export script
     * @param string $outdir 
     * @param array $v_download 
     * @param string $v_base_host 
     * @return void 
     * @throws IGKException 
     */
    private function _exportScripts(string $outdir, array &$v_download, string $v_base_host)
    {
        Logger::info("get scripts...");
        $dir = $this->routedef ? "" : "/js/";
        foreach ($this->m_scripts as $k => $lc) {
            //$link['src'] = $this->_replaceUriContent($src);
            $m = is_string($lc)? $lc : $lc['source'];
            $script  = is_array($lc) ? $lc['script'] : null;
            if (isset($v_download[$m])) {
                continue;
            }
            $q = parse_url($m);
            $path = $q['path'];
            $local = false;
            $v_base_path = null;
            $v_output = $outdir . $dir;
            if (!isset($q['host'])) {
                $m = $this->domain . "/" . ltrim($path, '/');
                $path = '/' . ltrim($path, '/');
                $this->m_scripts[$k] = (object)[
                    'refload' => 1,
                    'href' => $m
                ];
                $local = true;
            } else {
                // not local           
                $v_output = self::_getOutdir($outdir, $v_base_host, dirname($m)) . $dir;
                $v_base_path = dirname($path);
                $v_nuri = $this->_replaceUriContent($m, $outdir);
                $script['src'] = $v_nuri;
            }
            $file = null;
            $this->_downloadResource($m, rtrim($v_output, "/") . "/", $v_download, $v_base_path, "/js/", "js", $file);
            if (!$local && $file) {
                if ($this->asset_dir) {
                    $rf = str_replace(
                        $this->asset_dir,
                        "",
                        "/" . igk_str_rm_start(IGKResourceUriResolver::getInstance()->resolveOnly($file), '../')
                    );
                    $this->m_scripts[$k] = $rf;
                }
            }
        }
    }
    private function _getOutdir(string $outdir, string $host, ?string $bdir = null)
    {
        if ($bdir) {
            $q = parse_url($bdir);
            $v_bhost = igk_getv($q, "host");
            if ($v_bhost != $host) {
                $p = igk_getv($q, "path");
                $this->m_refer_domains[$v_bhost] = 1;
                return rtrim($outdir,  "/") . "/" . sha1($v_bhost) . $p;
            }
        }
        return null;
    }
    /**
     * download resource 
     * @param string $uri url to download 
     * @param string $outdir the output directory 
     * @param mixed $v_download 
     * @param mixed $base_path 
     * @param null|string $prefix 
     * @param null|string $ext 
     * @param mixed $file 
     * @return void|true 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    private function _downloadResource($uri, $outdir, &$v_download, $base_path, ?string $prefix = null, ?string $ext = null, &$file = null)
    {
        if (!IGKValidator::IsUri($uri)) {
            $uri = $this->domain . "/" . ltrim($uri, '/');
        }

        if (isset($v_download[$uri]) || isset($this->m_failed[$uri])) {
            return;
        }
        $g = parse_url($uri);
        // $ghost = parse_url($this->domain);

        if ($g && isset($g['path']) && !isset($this->m_res_downloaded[$uri])) {
            $path = $this->_getPath($uri, $prefix);
            if ($ext && (igk_io_path_ext($path) != $ext)) {
                $path .= '.' . $ext;
            }
            if ($base_path && strstr($path, $base_path)) {
                $path = substr($path, strlen($base_path));
            }

            $file = implode("/", [rtrim($outdir, "/"), ltrim($path, "/")]);
            if (file_exists($file)) {
                $v_download[$uri] = 1;
                $this->m_resources[] = $file;
                return;
            }

            if ($content = igk_curl_post_uri($uri)) {
                if (igk_curl_status() == 200) {
                    Logger::success("download : " . $uri);
                    igk_io_w2file($file, $content);
                    $this->m_res_downloaded[$uri] = 1;
                    $this->m_resources[] = $file;
                    $v_download[$uri] = 1;
                    $info = igk_curl_get_info();
                    $this->m_last_info = $info;
                    return true;
                } else {
                    $this->m_errors[] = 'fail: ' . $uri;
                    $this->m_failed[$uri] = 1;
                    Logger::danger("fail: " . $uri);
                }
            } else {
                $this->m_errors[] = igk_curl_status(). ': failed to get ' . $uri;
            }
        }
    }
    /**
     * retrieve the body node
     * @return mixed 
     */
    public function getBody()
    {
        return $this->m_node->getElementsByTagName('body', true);
    }
    /**
     * build view
     * @param string $view 
     * @return void 
     * @throws NotFoundExceptionInterface  
     * @throws ContainerExceptionInterface  
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     * @throws CssParserException 
     */
    public function buildView(string $view, ?BaseController $controller=null, bool $export = true)
    {
        $view = ltrim($view, '/');
        $controller = $controller ?? $this->controller ?? igk_die("controller not provided");
        $file = $controller->getViewDir() . "/" . $view;
        if (igk_io_path_ext($file) != IGK_VIEW_FILE_EXT) {
            $file .= IGK_VIEW_FILE_EXT;
        
        $outdir = $controller->getAssetsDir();
        $export_dir = $outdir;
        if ($this->standalone) {
            $export_dir .= "/" . $view;
        }
        if ($export)
            $this->exportTo($export_dir);

        $builder = new PHPScriptBuilder;
        $sb = new StringBuilder;

        $sb->appendLine('/**');
        $sb->appendLine('* @var HtmlNode $t');
        $sb->appendLine('*/');
        $sb->appendLine('ViewHelper::ForceDirEntry($ctrl, $fname);');
        $sb->appendLine('$doc->title = "' . $this->m_title . '";');
        // + |
        // + | $sb->appendLine('$doc->setBaseUri($entry_uri);');
        // + |
        foreach ($this->m_stylesheets as $k) {
            if (igk_getv($k, 'failed')) {
                continue;
            }
            if ($k->type == 'stylesheet') {
                $path = ltrim($this->_getPath($k->href, "/css/"), '/');
                $sb->appendLine('$doc->addTempStyle($ctrl->getAssetsDir("/css/' . $path . '"));');
            } else {
                $path = ltrim($this->_getPath($k->href), '/');
                $sb->appendLine('$doc->getHead()->link()->setAttributes(["rel"=>"' . $k->type
                    . '", "href"=>$ctrl::asset("' . $path . '")]'
                    . ');');
                // $this->m_resources[] = $path;
            }
        }

        foreach ($this->m_scripts as $src) {
            if (is_string($src)) {
                if (IGKValidator::IsUri($src)) {
                    $sb->appendLine('$doc->addTempScript("' . $src . '")->activate("defer");');
                } else {
                    $sb->appendLine('$doc->addTempScript($ctrl->getAssetsDir("' . $src . '"))->activate("defer");');
                }
            }
        }
        foreach ($this->m_inline_scripts as $src) {
            if (!$src){
                continue;
            }
            $sb->appendLine('$doc->getHead()->script()->Content = (<<<\'JS\'');
            if (is_string($src)){
                 $sb->appendLine($src);
            }else{
                igk_wln_e($src);
            }
            $sb->appendLine('JS);');
        } 
        $assets = [];
        $this->m_resources = array_unique(array_filter($this->m_resources));
        sort($this->m_resources);
        foreach ($this->m_resources as $k) {
            // IGKResourceUriResolver::getInstance()->resolve($k);
            $g = substr($k, strlen($outdir));
            $assets[] = $g;
            // $sb->appendLine('IGKResourceUriResolver::getInstance()->resolve($ctrl->getAssetsDir("'. $g.'"));');
        }
        if ($assets) {
            $sb->appendLine('$ctrl->resolveAssets(' . var_export($assets, true) . ');'); //->resolve($ctrl->getAssetsDir("'. $g.'"));');
        }

        if ($body = $this->getBody()) {
            $views = $body[0]->getChilds()->to_array();
            $node = igk_create_notagnode();
            HtmlUtils::CopyNode($node, $views, function ($n, & $skip = false) {
                $rep = null;
                if ($n=='svg'){
                    // copynode 
                    $rep = new XmlNode($n);
                    $skip = true;
                }else{
                    $rep = igk_create_node($n);
                }
                return  $rep;
            });
            // $sb->appendLine('$t->load(<<<\'HTML\'');
            $sb->appendLine('$doc->getBody()->setClass("+overflow")->getBodyBox()->setClass("-overflow-y-a");');
            $sb->appendLine('$t->obdata(function(){ ?>');
            $src = $node->render((object)[
                // "NoComment" => true
            ]);

            // + | remove domain
            $src = str_replace($this->domain . '/', "./", $src);
            $sb->appendLine($src);
            // $sb->appendLine('HTML);');
            $sb->appendLine('<?php });');
        }
        $builder->type('function');
        $builder->uses([
            //  IGKResourceUriResolver::class
            \IGK\Helper\ViewHelper::class
        ]);
        $builder->defs($sb . '');
        igk_io_w2file($file, $builder->render());
    }
}
}