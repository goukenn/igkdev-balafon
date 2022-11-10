<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SitemapGeneratorCommand.php
// @date: 20221006 08:18:43
// @desc: sitemap generator command
namespace IGK\System\Console\Commands;

use Exception;
use IGK\Controllers\BaseController; 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\XML\XmlProcessor;
use IGK\XML\XSDValidator;
use IGKException;

/**
 * use to generate sitemap on document 
 * @package igk\sitemaps\System\Console\Commands
 */
class SitemapGeneratorCommand extends AppExecCommand{

    var $command = "--sitemap:gen";
    var $desc = "generate sitemaps";
    /**
     * sitemap exec command
     * @param mixed $command 
     * @param mixed $controller 
     * @return void 
     */
    public function exec($command, $controller =null) {
    
        $curi = igk_io_baseuri();
        $baseuri = igk_getv($command->options, "--baseuri", $curi);
        if (empty($controller)){
            $indexes = self::GetProjectIndexes();            
            echo self::GenerateSiteMapIndex($indexes, $baseuri);
            return;
        }
        if (!$ctrl  = igk_getctrl($controller, false)){
            Logger::danger("controller not found");
            return -1;
        }
        Logger::print("generate sitemap"); 
        $baseuri = $baseuri.str_replace($curi, "", $ctrl->getAppUri()); 
        
        echo self::GenerateSiteMap($ctrl->getViews(false, true), $baseuri);

    }
    public static function GetProjectIndexes(){
        $indexes = [];
        $c = igk_sys_get_projects_controllers(); 
        $def_ctrl = igk_get_defaultwebpagectrl();
        foreach($c as $project){
            if ($def_ctrl===$project){
               $indexes[] = "sitemap.xml";
            }
            else {
                $entry = $project->getConfigs()->clBasicUriPattern;
                if ($entry && strpos($entry, "^") === 0){
                    $entry = ltrim($entry, "^/");
                    $indexes[$project->getName()] = $entry."/sitemap.xml";
                 }
            }
        }
        return $indexes;
    }
    /**
     * generate sitemap index 
     * @param array $views 
     * @param string $baseuri 
     * @param array|null $error 
     * @return int|string 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function GenerateSiteMap(array $views, string $baseuri, array & $error = null){
        $options = (object)[
            "Indent"=>1,
            "header"=>(new XmlProcessor("xml"))->setAttributes(["version"=>"1.0",
                "encoding"=>"utf8",
                "standalone"=>"yes"
                ])->render()
        ]; 
        $map = igk_create_xmlnode("urlset");
        $map["xmlns"] = "http://www.sitemaps.org/schemas/sitemap/0.9";
        $map["xmlns:igk"] = "http://schemas.igkdev.com/sitemap";   
        $base_uri = rtrim($baseuri, "/");
        $mod =  date("Y-m-d");
        foreach($views as $p){
            
            if ($p == IGK_DEFAULT_VIEW){
                $p = "";
            }else{
                if (basename($p) == IGK_DEFAULT_VIEW){
                    if ( ($s = dirname($p)) != "."){
                        $p = $s;
                    }
                }
            }
            $u = $map->url();
            $u->loc()->Content = implode("/", [$base_uri, $p]);
            $u->lastmod()->Content = $mod;
            $u->changefreq()->Content = "daily";
            $u->priority()->Content = "0.5";
        }        
        $s = $map->render($options);
        if (false===XSDValidator::ValidateSourceUri($s, \IGKConstants::SITEMAP_VALIDATOR)){
            $error[] = "not a good validator";            
            return -1;
        }
        return $s; 
    }

    public static function GenerateSiteMapIndex(array $indexes, string $baseuri, array & $error = null){
        $options = (object)[
            "Indent"=>1,
            "header"=>(new XmlProcessor("xml"))->setAttributes(["version"=>"1.0",
                "encoding"=>"utf8",
                "standalone"=>"yes"
                ])->render()
        ]; 
        $map = igk_create_xmlnode("sitemapindex");
        $map["xmlns"] = "http://www.sitemaps.org/schemas/sitemap/0.9";
        $map["xmlns:igk"] = "http://schemas.igkdev.com/sitemap";   
        $base_uri = rtrim($baseuri, "/");
        $mod =  date("Y-m-d");
        foreach($indexes as $p){
            if (basename($p) == "default"){
                if ( ($s = dirname($p)) != "."){
                    $p = $s;
                }
            }
            $u = $map->sitemap();
            $u->loc()->Content = implode("/", [$base_uri, $p]);
            $u->lastmod()->Content = $mod; 
        }        
        $s = $map->render($options);
        if (XSDValidator::ValidateSourceUri($s, \IGKConstants::SITEMAP_INDEX_VALIDATOR) === false){
            $error[] = "not a good validator";            
            return -1;
        }
        return $s; 
    }

}
