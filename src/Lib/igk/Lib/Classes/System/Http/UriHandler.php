<?php

namespace IGK\System\Http;

use Exception;
use IGK\ApplicationLoader;
use IGK\Controllers\ApplicationController;
use IGK\System\Console\Commands\SitemapGeneratorCommand;
use IGK\System\Html\XML\XmlProcessor;
use IGK\XML\XSDValidator;
use IGKApp;
use IGKAppSystem;
use IGKException;
use IGKSubDomainManager;

require_once __DIR__ . "/BaseUriHandler.php";
/**
 * uri handler 
 * @package IGK\System\Http
 */
class UriHandler extends BaseUriHandler
{
    var $cacheoutput = 5000;
    protected function bootApp()
    {
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
            "/sitemap.xml" => [$this, "_sitemap"],
            "/sitemap" => [$this, "_sitemap"],
        ];
    }
    /**
     * facade the favicon 
     * @return never 
     * @throws IGKException 
     * @throws Exception 
     */
    protected function _favicon()
    {
        ApplicationLoader::InitConstants();
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
    public function _sitemap()
    {
        // if not loader boot application then get controller list 
        ApplicationLoader::InitConstants();
        IGKSubDomainManager::Init();
        $_is_sub = IGKSubDomainManager::IsSubDomain();
        if (!$_is_sub) { 
            $file = igk_io_cachedir() . "/.sitemap.cache";
            if (file_exists($file)) {
                header("Content-Type: application/xml");
                echo file_get_contents($file);
                igk_exit();
            } else {
                $this->bootApp();
                $buri = igk_io_baseuri();
                $indexes = SitemapGeneratorCommand::GetProjectIndexes();
                $s = SitemapGeneratorCommand::GenerateSiteMapIndex($indexes, $buri);
                if ($s == -1) {
                    header("Content-Type: application/xml");
                    echo '<?xml version="1.0"?><sitemapindex></sitemapindex>';
                } else {
                    igk_io_w2file($file, $s);
                    igk_xml($s);
                }
            }
            igk_exit();
        } 
        // + | --------------------------------------------------------------------
        // + | leave site map for handling by Project
        // + | 
    }
}
