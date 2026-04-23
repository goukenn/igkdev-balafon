<?php
// @author: C.A.D. BONDJE DOUE
// @file: BaseUriHandler.php
// @date: 20221005 13:51:50
namespace IGK\System\Http;
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
    * @param null|IGKApplicationBase $application
    * @return void
    */
    public static function Handle(string $uri, ?IGKApplicationBase $application =null, ?callable $bootload=null )
    {
        $g = new static;
        $g->m_application = $application;
        $sk = $uri;
        $tab = parse_url($uri);        
       krsort($g->m_routes, SORT_STRING |  SORT_FLAG_CASE);
       $uri = $tab["path"];
       if (isset($g->m_routes[$uri])) {
           $r = $g->m_routes[$uri];
           if (is_callable($r)) {    
                defined('IGK_BASE_DIR') || \IGK\ApplicationLoader::InitConstants();                      
                call_user_func_array($r, [$uri, $g]); 
            }
        }        
    }
}