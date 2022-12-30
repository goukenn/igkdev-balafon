<?php
// @author: C.A.D. BONDJE DOUE
// @file: DocumentParser.php
// @date: 20221129 10:45:00
namespace IGK\DocumentParser;

use Exception;
use IGK\Controllers\BaseController;
use IGK\DocumentParser\Controllers\DocumentParserExportViewController;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\SvgListNode;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\XML\XmlNode;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGK\System\Regex\Replacement;
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
    private $ignoreResources = ['map'];
    private $m_bodyScripts = [];
    /**
     * will store the current container
     * @var mixed
     */
    private $m_container;

    /**
     * preserve inlines style : node 
     * @var array
     */
    private $m_inline_styles = [];
    private $m_title;
    private $m_refer_domains = [];
    private $m_failed = [];

    const REF_TAG = 'head|body|link|title|a|script|meta|object|img|video|audio|source|base|style';
    const PARAM_CONTAINER = 'doc_parser_container';
    const ENGINE_NAME = 'document_parser';
    /**
     * target domain 
     * @var string
     */
    var $uri;
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
     * download resource
     * @var true
     */
    var $downloadResource = true;

    /**
     * manifest js - in next project
     * @var ?string 
     */
    var $manifest;
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
    public function getImages()
    {
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
    /**
     * parse document
     * @param string $content content
     * @return bool 
     * @throws IGKException 
     */
    public function parse(string $content): bool
    {
        if (is_null($this->asset_dir) && $this->controller) {
            $gs =  $this->controller->getAssetsDir();
            $gs = igk_io_relativepath($this->controller->getDeclaredDir(), $gs);
            $this->asset_dir = igk_str_rm_start($gs, '../');
        }
        if (is_null($this->m_node)) {
            $this->m_node = igk_create_notagnode();
        }
        $this->m_node->load($content);

        $this->m_node->getElementsByTagName(function ($n) {
            $tn = $n->getTagName();
            if ($tn && preg_match("/^(" . self::REF_TAG . ")$/i", $tn)) {
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
    public function render($options = null)
    {
        return $this->m_node ? $this->m_node->render($options) : null;
    }
    /**
     * clear setting
     * @return void 
     */
    public function clear()
    {
        if ($this->m_node) {
            $this->m_node->clear();
        }
        $this->m_errors = [];
    }
    protected function visit_base($b)
    {
        $b['href'] = "./";
        $b->setIsVisible(false);
    }

    protected function visit_style($style)
    {
        // detect uris 
        if (!($src =  $style->getContent())) {
            return;
        }
        $this->m_inline_styles[] = (object)[
            'node' => $style,
            'src' => $src
        ];
    }
    protected function visit_head($t)
    {
        $this->m_container = 'head';
    }
    protected function visit_body($t)
    {
        $this->m_container = 'body';
    }
    protected function visit_title($title)
    {
        if (($p = $title->getParentNode()) && ($p->getTagName() == 'head')) {
            $g = $title->getContent();
            $this->m_title = $g;
        }
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
        return $this->uri . "/" . ltrim($uri);
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
            $g = str_replace($this->uri . "/img/", $this->asset_dir . '/img/', $src);
            $img['srcset'] = $g;
        }
    }
    private function _isInSameDomain($domain, $v)
    {
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
        if ($this->uri) {
            // + check if is in the same domain
            if ($this->_isInSameDomain($this->uri, $v)) {
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
                    $href = Path::Combine($this->uri, $href);
                }
                $type = $link['rel'];
                $this->m_stylesheets[] = (object)[
                    'type' => $type,
                    'href' => $href,
                    'source' => $link
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
        $link->setParam(self::PARAM_CONTAINER, $this->m_container);
        if (!empty($src = $link['src'])) {
            $v_is_uri = IGKValidator::IsUri($src);
            if ($this->controller) {
                if (!$v_is_uri || $this->_isInSameDomain($this->uri, $src)) {
                    $q = parse_url($src);
                    $ref_src = igk_getv($q, 'path');

                    $link['src'] = new ReferenceAssetHandle($ref_src, $this->controller);
                } else if ($v_is_uri) {
                    // output uri non in domain 
                    $src = ["source" => $src, "script" => $link];
                }
            }
            $this->m_scripts[] = $src;
            if (is_string($src) && ($this->m_container == 'body')) {
                $this->m_bodyScripts[] = $src;
            }
        } else {
            $v_d = $link;
            if (!$link->getHasAttributes()) {
                $v_d = $link->getContent();
            }
            $this->m_inline_scripts[] = $v_d;
        }
        $this->_integrity_check($link);
    }
    /**
     * disable integrety check for offline request
     * @param mixed $link 
     * @return void 
     */
    private function _integrity_check($link)
    {
        if ($i = $link['integrity']) {
            $link['integrity'] = new ReferenceAttributeHandle($i);
        }
        if ($i = $link['crossorigin']) {
            $link['crossorigin'] = new ReferenceAttributeHandle($i);
        }
    }
    /**
     * get global assets dir
     * @param mixed $outdir 
     * @param null|string $file 
     * @param null|BaseController $controller 
     * @return string|void|null 
     */
    private function _getGlobalAsset($outdir, ?string $file = null, ?BaseController $controller = null)
    {
        $gc = null;
        $controller = $controller ?? $this->controller;
        if ($controller) {
            $gc = $this->asset_dir ?? $controller->uri("/assets");
            if (($gc == './') && ($file))
                $gc = $this->_getRelativePathFromFile($file, $outdir);
        }
        return $gc;
    }
    /**
     * 
     * @param string $uri 
     * @param string $outdir 
     * @return string 
     */
    private function _replaceUriContent(string $uri, string $outdir)
    {
        $v_uri = new Uri($uri);
        $gc = $this->_getGlobalAsset($outdir);
        $uri = str_replace($v_uri->getSiteUri(), Path::Combine($gc, sha1($v_uri->getDomain())), $uri);
        return $uri;
    }

    protected function visit_meta($meta)
    {
        // $content = $link['content'];
        $property = $meta['property'];
        $name = $meta['name'];
        $content = $meta['content'];
        if ($content && IGKValidator::IsUri($content) && $this->_isInSameDomain($this->uri, $content)) {
            if ($this->controller) {
                $uri = new Uri($content);
                $l = str_replace($uri->getSiteUri(), $this->controller->uri(''), $content);
                $meta['content'] = $l;
            } else {
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
    /**
     * get only uri path
     * @param mixed $uri 
     * @param null|string $prefix 
     * @return mixed 
     */
    private function _getPath($uri, ?string $prefix = null)
    {
        $q = parse_url($uri);
        $path = $q['path'];
        if ($prefix)
            $path = igk_str_rm_start($path, $prefix);
        return $path;
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
            $m = is_string($lc) ? $lc : igk_getv($lc, 'source');
            $script  = is_array($lc) ? igk_getv($lc, 'script') : null;
            if (is_null($m)) {
                igk_wln_e("bad source def", $lc);
            }
            if (isset($v_download[$m])) {
                continue;
            }
            // if ($m == 'https://www.freshtropical.it/wp-includes/js/jquery/jquery.min.js?ver=3.6.1') {
            //     Logger::warn('jquery migrate ');
            // }
            $q = parse_url($m);
            $path = $q['path'];
            $local = false;
            $v_base_path = null;
            $v_output = $outdir . $dir;
            if (!isset($q['host']) || $this->_isInSameDomain($v_base_host, $q['host'])) {
                $m = $this->uri . "/" . ltrim($path, '/');
                // $m =   "./" . ltrim($path, '/');
                $path = '/' . ltrim($path, '/');
                $this->m_scripts[$k] = [
                    'refload' => 1,
                    'href' => "." . $path,
                    'in_loop' => 1,
                ];
                $local = true;
            } else {
                // not local           
                $v_output = self::_getOutdir($outdir, $v_base_host, dirname($m)) . $dir;
                $v_base_path = dirname($path);
                if ($script) {
                    $v_nuri = $this->_replaceUriContent($m, $outdir);
                    // check that the path contains js extension 
                    $v_qnuri = parse_url($v_nuri);
                    if (igk_io_path_ext($v_qnuri['path']) != 'js') {
                        $v_qnuri['path'] .= '.js';
                        $g = Uri::FromParseUrl($v_qnuri);

                        $v_nuri = $g->getFullUri(); //  build_url($v_qnuri);
                    }
                    $script['src'] = new DocumentParserResolvedUriAttribute($v_nuri);

                    if ($script->getParam(self::PARAM_CONTAINER) == 'body') {
                        $this->m_bodyScripts[] = "./" . ltrim(str_replace($this->asset_dir, "", $v_nuri), "/");
                    }
                }
            }
            $file = null;
            $this->_downloadResource($m, rtrim($v_output, "/") . "/", $v_download, $v_base_path, "/js/", "js", $file, self::class . "::MappingURL");
            if (!$local && $file) {
                if ($this->asset_dir) {
                    $trf = $file = Path::FlattenPath($file);
                    // $rf = str_replace(
                    //     $this->asset_dir,
                    //     "",
                    //     "/" . igk_str_rm_start(IGKResourceUriResolver::getInstance()->resolveOnly($file), '../')
                    // );
                    if ($this->controller) {
                        $trf = igk_str_rm_start($file, $this->controller->getAssetsDir());
                    }
                    $this->m_scripts[$k] = $trf != $file ? './' . ltrim($trf, '/') : igk_str_rm_start(IGKResourceUriResolver::getInstance()->resolveOnly($file), '../');
                    // igk_wln_e($trf, $rf, $file , $this->controller->asset('9c08b8ee9c186c84b548f400e734295d49c44e36/vue@3.2.37/dist/vue.global.prod.js'));
                }
            }
        }
    }
    public static function MappingURL($parser, string $content, $uri, $outdir, $basepath, &$file, &$v_download, $type = "/js/")
    {
        $uri_rgx = "(?<path>[^? ]+)";
        if ($type == "/js/") {
            $rgx = "/\/\/# sourceMappingURL\s*=\s*" . $uri_rgx . "/i";
        } else {
            $rgx = "/\/\*# sourceMappingURL\s*=\s*" . $uri_rgx . "\s*\*\//i";
        }
        if (preg_match_all($rgx, $content, $tab)) {
            $path = trim($tab['path'][0]);
            $v_baseuri = dirname($uri);
            $g = Path::Combine($v_baseuri, $path);
            $parser->_downloadResource($g, $outdir, $v_download, $basepath, $type, 'map', $file, null);
        }
    }
    public static function MappingCssURL(self $parser, string $content, $uri, $outdir, $basepath, &$file, &$v_download)
    {
        return self::MappingURL($parser, $content, $uri, $outdir, $basepath, $file, $v_download, '/css/');
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
        $v_file_dir = null;
        $v_uri_dir = null;
        Logger::print("inline stylesheets...");
        foreach ($this->m_inline_styles as $inline) {
            $src = $inline->src;
            if ($uris = $v_detector->cssUrl($src, $this->uri)) {
                $ctrl = $this->controller;
                $v_uri_dir = "./";
                if ($ctrl instanceof DocumentParserExportViewController) {
                    $ref_uri = igk_io_relativepath($ctrl->getDeclaredDir(), $ctrl->getAssetsDir()) . '/';
                } else {
                    $ref_uri = $ctrl->uri('/');
                }
                $v_file_dir = $outdir;
                $this->_treatStyleUris($uris, $v_download, $outdir, $v_base_host, $v_uri_dir, $v_file_dir);
                // replace src content 
                $v_rplist = [];
                while (count($uris)) {
                    $q = array_shift($uris);
                    $tq = parse_url($q->path);
                    if ($host = igk_getv($tq, 'host')) {
                        if (isset($v_rplist[$host])) {
                            continue;
                        }
                        $v_rplist[$host] = 1;
                        $g = new Uri($q->path);
                        $rep = $q->getFlattenReplacement();
                        $rep = str_replace($g->getSiteUri(), rtrim($ref_uri, "/"), $rep);
                        $src = str_replace($q->uri, $rep, $src);
                    }
                }

                $src = igk_css_minify($src);
                $inline->node->setContent($src);
            }
        }

        Logger::print("download stylesheets...");
        foreach ($this->m_stylesheets as $tm) {
            $m = $tm->href;
            if (isset($v_download[$m])) {
                continue;
            }
            if (strpos($m, "//") === 0) {
                $m = "https:" . $m;
            }

            if (($c = igk_curl_post_uri($m)) && ((igk_curl_status() == 200))) {
                Logger::success("download : " . $m);
                $q = parse_url($m);
                $path = $q['path'];
                $path = ltrim(igk_str_rm_start($path, "/css/"), '/');
                if ($tm->type == 'stylesheet') {
                    $uri_host = igk_getv($q, 'host');
                    $v_basepath  = null;
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
                    if ($tm->source) {
                        // update asset referencence
                        $this->_update_asset_ref($tm->source, 'href', substr($file, strlen($outdir)));
                    }
                    $v_basepath  = '/' . ltrim(dirname($path), '/');

                    self::MappingCssURL($this, $c, $m, dirname($file), $v_basepath, $css_file, $v_download);

                    // + | --------------------------------------------------------------------
                    // + | some style sheet contains external resource
                    // + |
                    if ($uris = $v_detector->cssUrl($c)) {
                        $v_file_dir = dirname($file);
                        $v_uri_dir = dirname($m);
                        $this->_treatStyleUris($uris, $v_download, $outdir, $v_base_host, $v_uri_dir, $v_file_dir);
                        $this->_replaceCssContent($file, $c, $uris, $v_base_host, $v_file_dir);
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
                    if ($tm->source) {
                        // update asset referencence
                        $this->_update_asset_ref($tm->source, 'href', igk_str_rm_start($file, $outdir));
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
    /**
     * treat style uri
     * @param array $uris 
     * @param string $outdir 
     * @param string $v_base_host 
     * @param string $uri_dir 
     * @param null|string $file_dir 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private function _treatStyleUris(array $uris, array &$v_download,  string $outdir, string $v_base_host, string $uri_dir, ?string $file_dir = null)
    {
        $dir = $this->routedef ? "" : "/css/";
        $v_detector = new UriDetector;
        while (count($uris) > 0) {
            $uri_m = array_shift($uris);
            // foreach ($uris as $uri_m) {
            $v_base_path = null;
            $v_uri_base_uri = $uri_m->getBaseUri();
            $baseuri =  $v_uri_base_uri ?? $uri_dir;
            // relove uri
            if ((strpos($uri_m->path, '../') === 0) || (strpos($uri_m->path, './') === 0)) {
                // relative to dirname 
                $mp = $this->_getOutdir($outdir, $v_base_host, $v_uri_base_uri) ?? $file_dir;
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

                $v_uri = $uri_m->getFullPath();
                $g = parse_url($uri_m->path);
                $uri_host = igk_getv($g, 'host');
                $v_output = $file_dir . "/";
                $v_prefix = null;
                if ($uri_host && ($uri_host != $v_base_host)) {
                    $v_goutdir = self::_getOutdir($outdir, $v_base_host, $uri_m->path);
                    if (is_null($v_goutdir)) {
                        igk_die("dir is null : " . $v_goutdir);
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
                            $uri_m->path = $this->uri . "/" . ltrim($uri_m->path, '/');
                        } else {
                            $v_tburi = new Uri($baseuri);
                            $baseuri = $v_tburi->getSiteUri();
                            $uri_m->path = $baseuri . "/" . ltrim($uri_m->path, '/');
                            $v_base_path = igk_getv($v_rpath, "path");
                            $v_output = $outdir . $dir;
                            $v_prefix = "/css/";
                        }
                    }
                }
                if ($this->_downloadResource($v_uri, rtrim($v_output, '/') . "/", $v_download, $v_base_path, $v_prefix, null, $file)) {
                    $content_type = igk_getv($this->m_last_info, CURLINFO_CONTENT_TYPE);
                    if ($content_type && (strpos($content_type, "text/css") !== false)) {
                        // + | --------------------------------------------------------------------
                        // + | binding css file definition 
                        // + |

                        $hash = '';
                        if (!empty($uri_m->extra)) {
                            $hash = '-' . sha1($uri_m->extra);
                        }
                        $nfile = $file;
                        if (igk_io_path_ext($file) != 'css') {
                            $nfile = $file . $hash . '.css';
                        } else if ($hash) {
                            $nfile = sprintf('%s/%s%s.%s', dirname($file), igk_io_basenamewithoutext($file), $hash, 'css');
                        }
                        if ($nfile != $file) {
                            rename($file, $nfile);
                            $file = $nfile;
                            $v_download[$file] = 1;
                            $v_prefix = $file_dir ? '../' : '';

                            if ($cf =  $v_prefix . $this->_getRelativePathFromFile($file_dir ?? $file, $outdir)) {
                                $cf .= ltrim(igk_str_rm_start($file, $outdir), '/');
                                $uri_m->setReplacementFile($cf);
                            }
                        }
                        // + | --------------------------------------------------------------------------------------
                        // + | get other link to download 
                        // + | $v_dom = new Uri($uri_m->path);
                        $v_last_content = file_get_contents($file);
                        if ($v_last_content  && ($v_tcs_uris = $v_detector->cssUrl($v_last_content, $uri_m->path))) {
                            array_unshift($uris, ...$v_tcs_uris);
                            // + | replace expected content with local service 
                            $this->_replaceCssContent($file, $v_last_content, $v_tcs_uris, $v_base_host, $outdir);
                        }
                    } else {
                        Logger::warn("content : " . $content_type);
                    }
                }
            }
        }
    }
    protected function _update_asset_ref($node, $attribute, string $path)
    {
        $node[$attribute] = rtrim($this->asset_dir, '/') . "/" . ltrim($path, "/");
    }
    /**
     * replace css content uri with local base asset uri
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
        $v_bgc = "";
        if ($this->controller) {
            $v_bgc = $this->_getRelativePathFromFile($file, $this->controller->getAssetsDir());
        } else {
            $v_bgc = $this->asset_dir ?? $this->controller->uri("/assets");
            if ($v_bgc == './')
                $v_bgc = $this->_getRelativePathFromFile($file, $outdir);
        }

        while (count($v_tcs_uris) > 0) {
            $q = array_shift($v_tcs_uris);
            if ($q->domain) {
                $uri = new Uri($q->path);
                $gc = $v_bgc;
                $site_uri = $uri->getSiteUri();
                // if ($this->controller) {                 
                //     $gc = $this->_getRelativePathFromFile($file, $this->controller->getAssetsDir());
                // } else {
                //     $gc = $this->asset_dir ?? $this->controller->uri("/assets");
                //     if ($gc == './')
                //         $gc = $this->_getRelativePathFromFile($file, $outdir);                    
                // }
                if (empty($gc)) {
                    $gc = './';
                }
                $replace_uri = $q->getFlattenReplacement();
                if ($v_base_host != $q->domain) {
                    $rp = Path::Combine($gc, sha1($q->domain));
                } else {
                    // enforce end '/'
                    $rp =  rtrim($gc, "/") . "/";
                    $site_uri = rtrim($site_uri, "/") . "/";
                }
                $replace_uri = str_replace($site_uri, $rp, $replace_uri);
                // $v_last_content = str_replace($site_uri, $rp, $v_last_content);
                $v_last_content = str_replace($q->uri, $replace_uri,  $v_last_content);
            } else {
                $uri = new Uri($q->path);
                if ($site_uri = $uri->getSiteUri()) {

                    $rc = $v_bgc . ltrim($uri->getPath(), '/');
                    $rc = 'url(' . $rc . ')';
                    $v_last_content = str_replace($q->uri, $rc,  $v_last_content);

                    Logger::warn("replace local " . $q->uri . "==>" .  $rc);
                }
                // $v_last_content = str_replace($q->uri, "/* ----- */",  $v_last_content);
            }
        }


        igk_io_w2file($file, $v_last_content);
    }
    private function _getRelativePathFromFile($file, $outdir)
    {
        if (strstr($file, $outdir)) {
            $p = dirname($file);
            $q = "";
            while ($p != $outdir) {
                $q .= "../";
                $p = dirname($p);
                if ($p == '/') {
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
        if ($this->asset_dir) {
            $outdir = Path::FlattenPath(Path::Combine($outdir, $this->asset_dir));
        }

        $v_base_host = igk_getv(parse_url($this->uri), 'host');
        $v_download = [];
        $this->_exportImages($outdir, $v_download, $v_base_host);
        Logger::success("-done");

        $this->_exportStyleSheets($outdir, $v_download, $v_base_host);
        Logger::success("-done");

        $this->_exportScripts($outdir, $v_download, $v_base_host);
        Logger::success("-done");
    }
    private function _exportImages($outdir, &$v_download)
    {
        Logger::info("get images...");
        if ($this->m_imgs) {
            foreach ($this->m_imgs as $m) {
                if (isset($v_download[$m])) {
                    continue;
                }
                $mp = $outdir . "/img/";
                $this->_downloadResource($m, $mp, $v_download, null, '/img/');
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
    private function _downloadResource(
        $uri,
        $outdir,
        &$v_download,
        $base_path,
        ?string $prefix = null,
        ?string $ext = null,
        &$file = null,
        ?callable $callback = null
    ) {
        if (!$this->downloadResource) {
            return;
        }
        if (!IGKValidator::IsUri($uri)) {
            $uri = $this->uri . "/" . ltrim($uri, '/');
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

            $file = Path::Combine($outdir, $path);
            if (file_exists($file)) {
                $v_download[$uri] = 1;
                $this->_loadResource($file, $ext);

                return;
            }

            if ($content = igk_curl_post_uri($uri)) {
                if (igk_curl_status() == 200) {
                    Logger::success("download : " . $uri);
                    igk_io_w2file($file, $content);

                    $this->m_res_downloaded[$uri] = 1;
                    $this->_loadResource($file, $ext);

                    $v_download[$uri] = 1;
                    $info = igk_curl_get_info();
                    $this->m_last_info = $info;
                    if ($callback) {
                        $nfile = null;
                        $callback($this, $content, $uri, $outdir, $base_path, $nfile, $v_download);
                    }
                    return true;
                } else {
                    $this->m_errors[] = 'fail: ' . $uri;
                    $this->m_failed[$uri] = 1;
                    Logger::danger("fail: " . $uri);
                }
            } else {
                $this->m_errors[] = igk_curl_status() . ': failed to get ' . $uri;
            }
        }
    }
    private function _loadResource(string $file, ?string $type = null)
    {
        if (is_null($type) || !in_array($type, $this->ignoreResources)) {
            $this->m_resources[] = $file;
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
    private function _downloadManifestResources(string $output, $path='_next')
    {
        $content = file_get_contents($this->manifest);
        if (preg_match_all('/\"(?P<file>[^ \"]+\.js)\"/', $content, $tb)) {
            $rt = [];
            $uri = (new Uri($this->uri))->getSiteUri();
            foreach ($tb[1] as $file) {
                if ($g = igk_curl_post_uri("{$uri}/{$path}/" . $file)) {
                    if (igk_curl_status() == 200) {
                        igk_io_w2file($output."/{$path}/". $file, $g);
                        $rt[] = "/{$path}/" . $file;                        
                    }
                }
            }
            $rt = array_unique($rt);
            $this->m_resources = array_merge($this->m_resources, $rt);
            
        }
    }

    private function _downloadImageResources(string $output, $path='storage')
    {
        $content = file_get_contents($this->manifest);
        if (preg_match_all('/\"(?P<file>[^ \"]+\.(jpg|jpeg|gif|png|tiff)\"/', $content, $tb)) {
            $rt = [];
            $uri = (new Uri($this->uri))->getSiteUri();
            foreach ($tb[1] as $file) {
                if ($g = igk_curl_post_uri("{$uri}/{$path}/" . $file)) {
                    if (igk_curl_status() == 200) {
                        igk_io_w2file($output."/{$path}/". $file, $g);
                        $rt[] = "/{$path}/" . $file;                        
                    }
                }
            }
            $rt = array_unique($rt);
            $this->m_resources = array_merge($this->m_resources, $rt);
             
        }
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
    public function buildView(string $view, ?BaseController $controller = null, bool $export = true)
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
            $sb->appendLine('* @var IGKHtmlDoc $doc');
            $sb->appendLine('*/');
            $sb->appendLine('ViewHelper::ForceDirEntry($ctrl, $fname);');
            $sb->appendLine('$doc->title = "' . $this->m_title . '";');

            if ($this->manifest) {
                // download manifiest
            }


            // + | 1. generate asset 
            $assets = [];
            $this->m_resources = array_unique(array_filter($this->m_resources));
            sort($this->m_resources);
            foreach ($this->m_resources as $k) {
                // IGKResourceUriResolver::getInstance()->resolve($k);
                $g = igk_str_rm_start($k, $outdir);
                $assets[] = Path::FlattenPath($g);
                // $sb->appendLine('IGKResourceUriResolver::getInstance()->resolve($ctrl->getAssetsDir("'. $g.'"));');
            }
            if ($assets) {
                $sb->appendLine('// 1. resolv assets ');
                $sb->appendLine('$ctrl->resolveAssets(' .
                    "[" . igk_map_array_to_str($assets, false) . "]"
                    . ');'); //->resolve($ctrl->getAssetsDir("'. $g.'"));');
            }


            // + |
            // + | 2. add entry uri .... 
            // + |
            $sb->appendLine('// 2. load tempory styles  ');
            foreach ($this->m_stylesheets as $k) {
                if (igk_getv($k, 'failed')) {
                    continue;
                }
                if ($k->type == 'stylesheet') {
                    $path = $this->_getAssetPath($controller, $k->href, $outdir);
                    // $path = ltrim($this->_getPath($k->href, "/css/"), '/');
                    $sb->appendLine('$doc->addTempStyle($ctrl->getAssetsDir("' . $path . '"));');
                } else {
                    $path = ltrim($this->_getPath($k->href), '/');
                    $sb->appendLine('$doc->getHead()->link()->setAttributes(["rel"=>"' . $k->type
                        . '", "href"=>$ctrl::asset("' . $path . '")]'
                        . ');');
                }
            }

            // +| bind script
            foreach ($this->m_scripts as $src) {
                if (is_string($src)) {
                    if (in_array($src, $this->m_bodyScripts)) {
                        continue;
                    }
                    if (IGKValidator::IsUri($src)) {
                        $sb->appendLine('$doc->addTempScript("' . $src . '")->activate("defer");');
                    } else {
                        $sb->appendLine('$doc->addTempScript($ctrl->getAssetsDir("' . $src . '"))->activate("defer");');
                    }
                } else {
                    $href = igk_getv($src, 'href');
                    $sb->appendLine('$doc->addTempScript($ctrl->getAssetsDir("' . $href . '"))->activate("defer");');
                }
            }

            // + | build inline script
            foreach ($this->m_inline_scripts as $src) {
                if (!$src) {
                    continue;
                }
                if (is_string($src)) {
                    $sb->appendLine('$doc->getHead()->script()->Content = (<<<\'JS\'');
                    $sb->appendLine(trim($src));
                    $sb->appendLine('JS);');
                } else {
                    $container = $src->getParam(self::PARAM_CONTAINER);
                    if ($container == 'body') {
                        continue;
                        // $sb->append('$doc->getBody()');
                    } else
                        $sb->append('$doc->getBody()');
                    $sb->appendLine('->script()->setAttributes([' .
                        igk_map_array_to_str($src->getAttributes()->to_array()) .
                        '])->Content =  (<<<\'JS\'');
                    $sb->appendLine(trim($src->getContent()));
                    $sb->appendLine('JS);');

                    // $sb->appendLine($src->render());
                    /// igk_dev_wln_e("not a string content ", $src);
                }
            }
            if ($body = $this->getBody()) {
                $v_body = $body[0];
                $views = $v_body->getChilds()->to_array();
                $node = igk_create_notagnode();

                ///clone copy for the views so now must render in default context 
                HtmlUtils::CopyNode($node, $views, function ($n, &$skip = false) {
                    $rep = null;
                    if ($n == 'svg-list') {
                        // copynode 
                        $rep = new SvgListNode(); // XmlNode($n);
                        $skip = true;
                    } else {
                        $rep = igk_create_node($n);
                    }
                    return  $rep;
                });
                // $sb->appendLine('$t->load(<<<\'HTML\'');
                $sb->append('$doc->getBody()');
                if ($arr = $v_body->getAttributes()->to_array()) {
                    $sb->append('->setAttributes([' .
                        igk_map_array_to_str($arr)
                        . '])');
                }
                $sb->appendLine('->setClass("+overflow")->getBodyBox()->setClass("-overflow-y-a");');
                $sb->appendLine('$t->obdata(function()use($ctrl){ $__IGK_ASSETS__ = $ctrl::asset(\'\'); ?>');
                $src = $node->render((object)[
                    // "NoComment" => true,
                    'engine' => self::ENGINE_NAME,
                    'method' => __FUNCTION__,
                    'documentParser' => $this,
                    'controller' => $controller,
                    'asset_dir' => $this->asset_dir,
                ]);



                // + | remove domain
                $src = str_replace($this->uri . '/', "./", $src);
                $src = self::TreatViewData($this, $controller, $src);
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
            igk_io_w2file($file, $src = $builder->render());
            igk_io_w2file('/Volumes/Data/Dev/PHP/balafon_site_dev/src/application/Projects/bantubeat/Views/home.phtml', $src);
        }
    }

    private function _getAssetPath($controller, $uri, $outdir)
    {
        $q = parse_url($uri);
        if (!$this->_isInSameDomain($this->uri, $q['host'])) {
            $uri = $this->_replaceUriContent($uri, $outdir, $controller);
            $uri = igk_str_rm_start($uri, $this->asset_dir);
        } else {
            $uri = $this->_getPath($uri);
        }
        return $uri;
    }

    public static function TreatViewData(DocumentParser $g, BaseController $controller, string $src)
    {
        $replace = new Replacement;
        $ref_uri = igk_io_relativepath($g->controller->getDeclaredDir(), $controller->getAssetsDir());

        if ($v_domainname = igk_get_domain_name($g->uri)) {
            if ($controller instanceof DocumentParserExportViewController) {
                $ref_uri = igk_io_relativepath($g->controller->getDeclaredDir(), $controller->getAssetsDir()) . '$4';
            } else {
                $ref_uri = $controller->uri('/$4');
            }
            // + | remove mail 
            $replace->add('/@' . $v_domainname . '(\/)?/i', '@__MAIL__');
            // + | match url
            $replace->add(
                '/(\/\/|[^"\';:\( ]+:\/\/((www|[0-9a-z]+)\.)?)?' . $v_domainname . '(\/)?/i',
                '<?= $__IGK_ASSETS__ ?>$4'
                // $ref_uri
            );
            // + | restaure mail
            $replace->add('/@__MAIL__/', '@' . $v_domainname);
        } else {
            $replace->add('#' . addslashes($ref_uri) . '(\/)?#i', './assets/');
        }

        return $replace->replace($src);
    }
}
