<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigarationPageHandler.php
// @date: 20220825 14:57:44
// @desc: configuration page handler
namespace IGK\System\Http;

use IGK\System\Exceptions\ArgumentTypeNotValidException;
use Exception;
use IGK\Server;
use IGK\System\Core\Configuration\CPanel;
use IGK\System\IO\Path;
use IGK\System\Uri;
use IGKException;
use ReflectionException;

/**
 * Configuration page handler.
 * @package IGK\System\Http
 */
class ConfigurationPageHandler
{
    /**
     * Property: route.
     * @var mixed
     */
    var $route;
    /**
     * Property: engine.
     * @var mixed
     */
    var $engine;
    /**
     * Property: file.
     * @var mixed
     */
    var $file;
    /**
     * auto generate doc.
     * @param null|mixed $engine closure(bool display)
     * @param null|string $file
     * @param null|string $route
     * @return void
     */
    public function __construct($engine, ?string $file = null, ?string $route = null)
    {
        $this->file = $file;
        $this->engine = $engine;
        $this->route = rtrim(strtolower($route ?? igk_configs()->get("configPageRoute", IGK_CONFIG_PAGEFOLDER)), '/');
    }
    /**
     * handle route
     * @param string $path_info 
     * @param ?callable $redirect_callback
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function handle_route(string $path_info, ?callable $redirect_callback = null)
    {
        $v_path = 0;
        $engine = $this->engine;
        $file = $this->file;
        $g = ltrim(implode('/', array_filter(explode("/", strtolower($path_info)))), '/');

        if (strpos($g, $this->route) === 0) {
            $r = explode('/', $g, 2);
            array_shift($r);
            if ($r) {
                $this->registerHandleHookUri(new Uri($r[0]));
            }

            $data = Path::getInstance()->getSysDataDir();
            if (is_file($data . "/no_config")) {
                igk_set_header("403");
                igk_navto(igk_io_baseuri());
            }
            if ($redirect_callback) {
                // + | possibility to handle special route of redirection
                igk_environment()->noPageRedirection404 = 1;
                $redirect_callback();
                igk_environment()->noPageRedirection404 = null;
            }
            defined('IGK_REDIRECTION') || define('IGK_REDIRECTION', 0);
            if (!defined("IGK_CONFIG_PAGE"))
                define("IGK_CONFIG_PAGE", 1);
            define("IGK_CURRENT_PAGEFOLDER", IGK_CONFIG_PAGEFOLDER);
            $script = $_SERVER["SCRIPT_NAME"];
            $dir = igk_str_rm_last(igk_uri(dirname($script)), '/');
            if (empty($dir) && $v_path) {
                $dir .= $script;
            }
            $g = explode("/", $g);
            $level = count($g) - 1;
            igk_io_set_dir_level($level);
            if (!empty($query = igk_server()->QUERY_STRING)) {
                $query = "?" . $query;
            }
            $rq_path = implode("/", array_slice($g, 1));
            if (!empty($rq_path)) {
                $rq_path = "/" . $rq_path;
            }
            unset($_SERVER["PHP_SELF"]);
            Server::getInstance()->prepareServerInfo();
            require_once IGK_LIB_DIR . '/igk_html_utils.php';
            // + | priority to handling controller request             
            RequestHandler::getInstance()->handle_ctrl_request_uri();
            igk_sys_config_view($file);
            igk_exit();
        }
    }
    /**
     * handle subpath configuration 
     * @param Uri $uri 
     * @return mixed 
     */
    public function registerHandleHookUri(Uri $uri)
    {
        $func = $this->_hookHandleUri();
        $obj = (object)[
            'uri' => $uri
        ];
        $c = igk_hook(CPanel::HOOK_HANDLE_URI, [$obj]);
        $this->_unregHookHandleUri($func);
        return $c;
    }
    protected function _hookHandleUri()
    {
        $fc = function ($e) {
            return $this->_handleConfigurationURI($e);
        };
        igk_reg_hook(CPanel::HOOK_HANDLE_URI, $fc);
        return $fc;
    }
    protected function _unregHookHandleUri($fc)
    {
        igk_unreg_hook(CPanel::HOOK_HANDLE_URI, $fc);
    }
    public function _handleConfigurationURI($e)
    {
        list($obj) = $e->args;


        $p = $obj->uri->getPath();
        $tab = ['robots.txt' => function () {
            igk_navto('/robots.txt');
        }];

        if ($p) {
            $fc = igk_getv($tab, $p);
            $fc();
            igk_exit();
        }
    }
}
