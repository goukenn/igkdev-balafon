<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SitemapGeneratorCommand.php
// @date: 20221006 08:18:43
// @desc: sitemap generator command
namespace IGK\System\Console\Commands;
use Exception;
use IGK\Constants;
use IGK\Controllers\BaseController; 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\XML\XmlProcessor;
use IGK\System\Regex\Replacement;
use IGK\XML\XSDValidator;
use IGKException;
use ReflectionException;

/**
 * use to generate sitemap on document 
 * @package igk\sitemaps\System\Console\Commands
 */
class SitemapGeneratorCommand extends AppExecCommand{
    const URL_SET_OPTION = 'url-set';
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--sitemap:gen";
    /**
    * Property: desc.
    * @var mixed
    */
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
        if (!$ctrl  = self::GetController($controller, false)){
            Logger::danger("controller not found");
            return -1;
        }
        Logger::print("generate sitemap"); 
        $baseuri = $baseuri.str_replace($curi, "", $ctrl->getAppUri()); 
        echo self::GenerateSiteMap($ctrl->getViews(false, true), $baseuri);
    }
    /**
    * Returns Project Indexes.
    */
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
    * @param ?array & $error
    * @throws Exception
    * @throws IGKException
    * @return int|string
    */
    public static function GenerateSiteMap(array $views, string $baseuri, ?array & $error = null){
        $options = (object)[
            "Indent"=>1,
            "header"=>implode("\n", [
            (new XmlProcessor("xml"))->setAttributes(["version"=>"1.0"])->render(),            
            (new XmlProcessor("xml-stylesheet"))->setAttributes(["href"=>"/assets/balafon-urlset-sitemap.xsl", "type"=>"text/xsl"])->render(),
            ''])
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
        if (false===XSDValidator::ValidateSourceUri($s, Constants::SITEMAP_VALIDATOR)){
            $error[] = "not a good validator";            
            return -1;
        }
        return $s; 
    }
    /**
    * auto generate doc.
    * @param array $indexes
    * @param string $baseuri
    * @param array|null &$error
    * @return int|null|string
    */
    public static function GenerateSiteMapIndex(array $indexes, string $baseuri, ?array & $error = null){
    
        $options = (object)[
            "Indent"=>1,
            "header"=>implode("\n", [(new XmlProcessor("xml"))->setAttributes([
                "version"=>"1.0"
                ])->render(),
                '<?xml-stylesheet type="text/xsl" href="/assets/balafon-sitemap.xsl"?>'
            ])
        ]; 
        $map = igk_create_xmlnode("sitemapindex");
        $map["xmlns"] = "http://www.sitemaps.org/schemas/sitemap/0.9";
        $map["xmlns:igk"] = "http://schemas.igkdev.com/sitemap";   
        $base_uri = rtrim($baseuri, "/");
        $mod =  date("Y-m-d");
        foreach(['title','description'] as $k){
            $map->add('igk:'.$k)->content =  '%sitemap-'.$k.'%';
        }
        foreach(['location','lastupdate', 'counting-ref'] as $k){
            $map->add('igk:'.$k)->content =  '%res-'.$k.'%';
        }
        foreach($indexes as $p){
            if (basename($p) == "default"){
                if ( ($s = dirname($p)) != "."){
                    $p = $s;
                }
            }
            if (empty($p)){
                //$p = ';'.self::URL_SET_OPTION;
            }
            $u = $map->sitemap();
            $u->loc()->Content = implode("/", [ $base_uri, $p]);
            $u->lastmod()->Content = $mod; 
        }        
        $s = $map->render($options);
        if (!igk_environment()->isDev() && (XSDValidator::ValidateSourceUri($s, Constants::SITEMAP_INDEX_VALIDATOR) === false)){
            $error[] = "not a good validator";            
            return -1;
        } 
        $s = str_replace($base_uri, '%base-uri%', $s);
        return $s; 
    }
    /**
     * replace site definition 
     * @param string $def 
     * @param array $option 
     * @return string 
     */
    public static function ReplaceSitemapDefinition(string $def, array $option){
        $rp = new Replacement;
        foreach($option as $k=>$v){
            $rp->add($k, $v);
        }
        return $rp->replace($def);
    }

}