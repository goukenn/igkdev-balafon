<?php
// @author: C.A.D. BONDJE DOUE
// @file: BaseUriHandler.php
// @date: 20221005 13:51:50
namespace IGK\System\Http;

use IGK\Resources\R;
use IGKApplicationBase;

/**
 * 
 * @package IGK\System\Http
 */
/**
* auto generate doc.
* @package IGK\System\Http
*/
abstract class BaseUriHandler
{
    /**
    * Property: routes.
    * @var mixed
    */
    protected $m_routes;
    /**
    * Property: application.
    * @var mixed
    */
    protected $m_application;
    /**
    * .ctr
    */
    protected function __construct()
    {
        $this->m_routes = $this->initRoutes();
    }
    /**
    * Initializes Routes.
    */
    protected function initRoutes()
    {
        return [];
    }
    /**
    * auto generate doc.
    * @param string $uri
    * @param null|IGKApplicationBase $application
    * @param ?callable $bootload
    * @return void
    */
    public static function Handle(string $uri, ?IGKApplicationBase $application =null, ?callable $bootload=null )
    {
        $g = new static;
        $g->m_application = $application;
        defined('IGK_BASE_DIR') || \IGK\ApplicationLoader::InitConstants();   
        $sk = $uri;
        $tab = parse_url($uri);        
       krsort($g->m_routes, SORT_STRING |  SORT_FLAG_CASE);
       $uri = $tab["path"];
       $tlang =  igk_configs()->get('default_lang', 'en');
       list($uri, $query_options) = igk_extract(explode(';', $uri, 2), range(0,1)); 
        $query_options = ($query_options) ? igk_get_query_options($query_options): [];        
        $lang = R::GetSupportLangRegex();        
        if((strpos(trim($uri, '/'), '/')>0) && preg_match("/^(?i)\/($lang)(?=\/)/",$uri, $tab)){
            $tlang = $tab[1];
            $uri = substr($uri, strlen($tab[0]));
        }
       if (isset($g->m_routes[$uri])) {
           $r = $g->m_routes[$uri];
           if (is_callable($r)) {                          
                call_user_func_array($r, [$uri, $g, $query_options, $tlang]); 
            }
        }        
    }
}