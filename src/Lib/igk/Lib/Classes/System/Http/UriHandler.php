<?php
namespace IGK\System\Http;

use IGK\ApplicationLoader;
use IGK\Controllers\ApplicationController;
use IGK\System\Console\Commands\SitemapGeneratorCommand;
use IGK\System\Html\XML\XmlProcessor;
use IGK\XML\XSDValidator;
use IGKApp;
use IGKAppSystem;

require_once __DIR__."/BaseUriHandler.php";
/**
 * uri handler 
 * @package IGK\System\Http
 */
class UriHandler extends BaseUriHandler
{
    var $cacheoutput = 5000;
    protected function bootApp(){
        ApplicationLoader::getInstance()->bootApp($this->m_application);  
    }

    protected function __construct()
    {
        $this->m_routes = $this->initRoutes(); 
    } 
    protected function initRoutes()
    {
        return [
            "/favicon.ico" => [$this, '_favicon'],
            "/sitemap.xml"=>[$this, "_sitemap"],
            "/sitemap"=>[$this, "_sitemap"],
        ];
    }
    protected function _favicon()
    { 
        igk_set_header(
            200,
            'ok',
            [
                "Content-Type: image/png",
                "Cache-Control: max-age=31536000"
            ]
        );   
        igk_header_cache_output($this->cacheoutput);    
        include(IGK_LIB_DIR . "/Default/R/Img/balafon.ico");
        igk_exit();
    }
    public function _sitemap(){
        // if not loader boot application then get controller list 
        ApplicationLoader::InitConstants();
        $file = igk_io_cachedir()."/.sitemap.cache";
        if (file_exists($file)){
            header("Content-Type: application/xml");
            include($file);;
            igk_exit();
        }  
        $this->bootApp();        
        $indexes = SitemapGeneratorCommand::GetProjectIndexes();
        $s = SitemapGeneratorCommand::GenerateSiteMapIndex($indexes, igk_io_baseuri());
        igk_io_w2file($file, $s);
        igk_xml($s);
  }
}