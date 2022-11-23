<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerAsset.php
// @date: 20221122 19:55:00
namespace IGK\DocumentParser;

use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\System\Console\Logger;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlRendererOptions;
use IGK\System\Html\XML\XmlNode;

///<summary></summary>
/**
 * 
 * @package IGK\DocumentParser
 */
class ControllerAsset
{
    /**
     * base controller 
     * @var mixed
     */
    private $m_controller;
    private $m_res_downloaded;
    /**
     * uri path => target path resources base on the domain
     * @var array 
     */
    private $m_resources;
    var $primaryController;
    var $domain;
    /**
     * 
     * @var mixed
     */
    var $callback;
    public function __construct(BaseController $controller)
    {
        $this->m_controller = $controller;
        $this->primaryController = true;
    }
    /**
     * replace uri
     * @param BaseController $controller 
     * @param array $replace 
     * @param string $uri 
     * @param string $tp 
     * @return string|void 
     */
    function replace_uri(BaseController $controller, array &$replace, string $uri, string $tp)
    {
        $v_asset_dir = $controller->getAssetsDir();
        $g = parse_url($uri);
        if (!$g || !isset($g['path'])) {

            return;
        }
        if (strstr($tp, $v_asset_dir)) {
            $v_rg = substr($tp, strlen($v_asset_dir));
            if (!empty($v_rg)) {
                $v_rg = trim($v_rg, '/') . '/';
            }
            $guri = $controller::uri('assets/' . $v_rg . ltrim($g["path"], '/'));
            $replace[$uri] = $guri;
            $host = igk_getv($g, 'host');
            $replace[':base_host'][$host] = 1;

            return $guri;
        }
    }

    /**
     * 
     * @param mixed $file 
     * @param null|string $out_dir 
     * @param null|BaseController $controller 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function scriptPageFile(string $file, ?string $outdir = null)
    {
        $controller = $this->m_controller;
        $bck_controller = igk_configs()->default_controller;
        if ($this->primaryController)
            igk_configs()->default_controller = $controller;
        $this->_generateScriptPage($controller, $file, $outdir);
        igk_configs()->default_controller = $bck_controller;
    }
    protected function _generateScriptContentPage(BaseController $controller, string $content, ?string $outdir = null)
    {
    }
    protected function _generateScriptPage(BaseController $controller, string $file, ?string $outdir = null)
    {
        $content = file_get_contents($file);

        // + TESTING
//         $content = <<<'HTML'
// <div style='color:red'> my color </div>
// <div id="background-image" class="image loaded" style="background-image:url('https://www.tonerafrika.com/wp-content/uploads/2020/08/printer-933098_1920.jpg')">
//         </div>
//         <div class="background-overlay solid-color" style="background-color:#0a0a0a;opacity:0.3"></div>
// HTML;



        $v_detect = new UriDetector();
        $v_asset_dir = $controller->getAssetsDir();
        $v_hash = '';
        $out_dir =  $outdir ?? $v_asset_dir;
        $v = new XmlNode("div");
        $n = HtmlReader::Load($content, HtmlContext::Html, function ($n) {
            return new HtmlNode($n);
        });

        $replace = [];
        $binding_content = [];
        $v->add($n);

        //$links = $v->getElementsByTagName("div");
        $links = $v->getElementsByTagName("link");
        if ($links) {
            Logger::info(" writing links...");
            $content = '';

            foreach ($links as $ln) {
                $uri = $ln['href'];
                // if ($uri == 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'){

                // }
                $v_get_link = parse_url($uri);
                $v_hash = $this->_hashUri($v_get_link);

                if ($uri && $v_detect->match($uri)) {
                    Logger::info("/* $uri */");
                    if ($ln['rel'] == 'stylesheet') {
                        $tp = $out_dir . $v_hash . "/css/";
                        $s = "";
                        $this->downloadResource($uri, $tp, $s);
                        $content .= $s; //  igk_curl_post_uri($uri);
                        $tc = $this->replace_uri($controller, $replace, $uri, $tp);
                        if (strpos(basename($v_ph = $v_get_link['path']), '.') === false) {
                            rename($tp . $v_ph, $tp . $v_ph . '.css');
                            $tc .= '.css';
                        }
                        $ln['href'] = $tc;

                        $baseuri = dirname($uri);
                        $path = parse_url($uri)['path'];
                        if ($uris = $v_detect->cssUrl($s)) {
                            foreach ($uris as $m) {
                                // $tgp = $tp . trim($path, '/').'/' . $m->path; 
                                if ((strpos($m->path, '../') === 0) || (strpos($m->path, '../') === 0)) {
                                    $gp = $baseuri . $m->path;
                                    $gp =  igk_io_flatten($gp);
                                    // $tgp =  igk_io_flatten($tgp);
                                    $this->downloadResource($gp, $tp);
                                } else {
                                    $this->downloadResource($m->path, $tp);
                                }
                            }
                        }
                    } else {
                        $g = parse_url($uri);
                        $tp = $out_dir . "/img/";
                        $this->downloadResource($uri, $tp);
                        $ln['href'] = $this->replace_uri($controller, $replace, $uri, $tp);
                    }
                }
            }
            $binding_content[$out_dir . '/css/ref.css'] = $content;
        }
        // some tag tacontent 
        $links = $v->getElementsByTagName("meta");
        if ($links) {
            Logger::info(" writing links...");
            foreach ($links as $ln) {
                $uri = $ln['content'];
                if ($uri && $v_detect->match($uri)) {
                    if ($g = parse_url($uri)) {
                        $tp = $out_dir . "/img/";
                        $this->downloadResource($uri, $tp);
                        $ln['content'] = $n_uri = $this->replace_uri($controller, $replace, $uri, $tp);
                    }
                }
            }
        }
        $links = $v->getElementsByTagName("style");
        if ($links) {
            $content = '';
            $inline = '';
            foreach ($links as $ln) {
                if ($uri = $ln['src']) {
                    Logger::print("/* $uri */");
                    $content .= igk_curl_post_uri($uri);
                    if ($controller) {
                        $g = parse_url($uri);
                        $tp = $out_dir . "/css/";
                        igk_io_w2file($tp . $g["path"], $content);
                        $ln['src'] = $this->replace_uri($controller, $replace, $uri, $tp);
                    }
                } else {
                    $inline .= $ln->getContent();
                }
            }
            Logger::info(" writing style...");
            igk_io_w2file($out_dir . '/css/main-gen.css', $content);
            igk_io_w2file($out_dir . '/css/inline-js.css', $inline);
        }

        $links = $v->getElementsByTagName("script");
        if ($links) {
            $content = '';
            $inline = '';
            Logger::info(" writing script ... - ");
            foreach ($links as $ln) {
                if ($uri = $ln['src']) {
                    $g = parse_url($uri);
                    Logger::print("/* $uri */");
                    $content = igk_curl_post_uri($uri);
                    igk_io_w2file($out_dir . "/js/" . $g["path"], $content);
                    if ($controller) {
                        $tp = $out_dir . "/js/";
                        $this->downloadResource($uri, $tp);
                        $ln['src'] = $this->replace_uri($controller, $replace, $uri, $tp);
                    }
                    $content = "";
                } else {
                    $inline .= $ln->getContent();
                }
            }
            igk_wln("/* finish js */ ");
            igk_io_w2file($out_dir . '/js/main-gen.js', $content);
            igk_io_w2file($out_dir . '/js/inline-js.js', $inline);
        }


        $links = $v->getElementsByTagName("img");
        if ($links) {
            $content = '';
            $inline = '';
            foreach ($links as $ln) {
                if ($uri = $ln['src']) {
                    $g = parse_url($uri);
                    Logger::print("/* $uri */");
                    $tp = $out_dir . "/img/";
                    $this->downloadResource($uri, $tp);
                    $ln['src'] = $this->replace_uri($controller, $replace, $uri, $tp);
                } else {
                    $inline .= $ln->getContent();
                }
            }
        }

        if ($this->callback) {
            $fc = $this->callback;
            $pnode = $fc($v);
        } else {
            $pnode = $v->getElementsByTagName('html')[0];
        }

        if ($this->domain) {
            $tp = $out_dir;
            $this->downloadResource($this->domain . '/favicon.ico', $tp);
        }

        $options = new HtmlRendererOptions;
        $options->Indent = 0;
        $options->StandAlone = 0;
        $options->noComment = 1;
        $src = $pnode->render($options);

        // treat output 

        $binding_content[$out_dir . '/index.html'] = $src;

        $base_host = igk_getv($replace, ':base_host');
        unset($replace[':base_host']);
        $tp = $out_dir . "/img/";
        //$this->downloadResource('http://'.$uri[0], $tp);    
        $v_hosts = [];
        foreach ($binding_content as $file => $src) {
            foreach ($replace as $k => $v) {
                $src = str_replace($k, $v, $src);
            }
            if ($d = $v_detect->match($src)) {
                while (count($d) > 0) {
                    $rp = '';
                    $q = array_shift($d);
                    $tg = parse_url($q->uri);
                    if (!isset($base_host[igk_getv($tg, 'host')]))
                        continue;
                    if (empty($q->path)) {
                        $v_hosts[$q->uri] = 1;
                        continue;
                    }
                    if (preg_match('/\.(jp(e)?g|png|svg|tiff|gif)$/', $q->path)) {
                        $tp = $out_dir . "/img/";
                        $this->downloadResource($q->uri, $tp);
                        $rp = $this->replace_uri($controller, $replace, $q->uri, $tp);
                    } else if (preg_match('/\.(pdf|xml|txt|csv)$/', $q->path)) {
                        $tp = $out_dir . "/data/";
                        $this->downloadResource($q->uri, $tp);
                        $rp = $this->replace_uri($controller, $replace, $q->uri, $tp);
                    } else if (preg_match('/\.(js|ts|vue)$/', $q->path)) {
                        $tp = $out_dir . "/js/";
                        $this->downloadResource($q->uri, $tp);
                        $rp = $this->replace_uri($controller, $replace, $q->uri, $tp);
                    } else {
                        if (!($rp = $this->handleRes($q->uri)))
                            $rp = igk_io_baseuri();
                    }
                    $src = str_replace($q->uri, $rp, $src);
                }
            }
            // + | remove host 
            foreach (array_keys($v_hosts) as $t)
                $src = str_replace($t, '/', $src);
            // revery file resources that head with resource file must be replace by 
            //  preg_match_all('#((http(s):)?\/\/(w+)(\.w+)+(\/(w+\))?#i');


            igk_io_w2file($file, $src);
        }
        // igk_wln_e("done ----- ");
    }
    private function _hashUri($info)
    {
        $hash = '';
        $_host = igk_getv($info, 'host');
        if ($_host  != $this->domain) {
            $hash = '/' . sha1($_host);
        }
        return $hash;
    }
    private function downloadResource($uri, $outdir, &$content = '')
    {
        $g = parse_url($uri);
        if ($g && isset($g['path']) && !isset($this->m_res_downloaded[$uri])) {
            if ($content = igk_curl_post_uri($uri)) {
                if (igk_curl_status() == 200) {
                    igk_io_w2file($outdir . $g["path"], $content);
                }
            }
            $this->m_res_downloaded[$uri] = 1;
        }
    }
    /**
     * register domain resources 
     * @param string $path 
     * @param string $target 
     * @return void 
     */
    public function registerResource(string $path, string $target)
    {
        $this->m_resources[$path] = $target;
    }
    public function clearResources()
    {
        $this->m_resources = [];
    }
    /**
     * ech if handle resources 
     * @param string $uri 
     * @return null|string 
     */
    protected function handleRes(string $uri): ?string
    {
        $t = null;
        $q = parse_url($uri);
        if (isset($q['path']))
            $t = igk_getv($this->m_resources, $q['path']);
        return $t;
    }
}
