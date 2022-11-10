<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ConfigarationPageHandler.php
// @date: 20220825 14:57:44
// @desc: configuration page handler

namespace IGK\System\Http;

use IGK\System\Exceptions\ArgumentTypeNotValidException;
use Exception;
use IGK\Server;
use IGK\System\IO\Path;
use IGKException; 
use ReflectionException;

class ConfigurationPageHandler
{
    var $route;
    var $engine;
    var $file;
    /**
     * 
     * @param IEngineRunner|Callable $engine engine to run
     * @param null|string $file entry file 
     * @param null|string $route 
     * @return void 
     * @throws IGKException 
     */
    public function __construct($engine, ?string $file = null, ?string $route = null)
    {
        $this->file = $file;
        $this->engine = $engine;
        $this->route = rtrim(strtolower($route ?? igk_configs()->get("configPageRoute", IGK_CONFIG_PAGEFOLDER)), '/');
    }
    /**
     * handle route
     * @param mixed $path_info 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function handle_route($path_info)
    {
        $v_path = 0;
        $engine = $this->engine;
        $file = $this->file;
        $g = ltrim(implode('/', array_filter(explode("/", strtolower($path_info)))), '/');
        if (strpos($g, $this->route) === 0) {

            $data = Path::getInstance()->getSysDataDir();
            if (is_file($data . "/no_config")) {
                igk_set_header("403");
                igk_navto(igk_io_baseuri());
            }


            // igk_wln_e("configation handle");
            define('IGK_REDIRECTION', 0);
            if (!defined("IGK_CONFIG_PAGE"))
                define("IGK_CONFIG_PAGE", 1);
            define("IGK_CURRENT_PAGEFOLDER", IGK_CONFIG_PAGEFOLDER);
            $script = $_SERVER["SCRIPT_NAME"];
            $dir = igk_str_rm_last(igk_uri(dirname($script)), '/');
            if (empty($dir) && $v_path) {
                $dir .= $script;
            }
            $g = explode("/", $g); //substr($g, strlen($this->route));
            $level = count($g) - 1;
            igk_io_set_dir_level($level);
            if (!empty($query = igk_server()->QUERY_STRING)) {
                $query = "?" . $query;
            }
            $rq_path = implode("/", array_slice($g, 1));
            if (!empty($rq_path)) {
                $rq_path = "/" . $rq_path; // .$query;
            }
            // igk_wln_e("query : ", $query,   $_SERVER["REQUEST_URI"]);
            // $_SERVER["REQUEST_URI"]=$dir."/".IGK_CONFIG_PAGEFOLDER."{$rq_path}";
            unset($_SERVER["PHP_SELF"]); //=$dir."/".IGK_CONFIG_PAGEFOLDER."/DTA";
            Server::getInstance()->prepareServerInfo();
            require_once IGK_LIB_DIR. '/igk_html_utils.php';
             
            // + | priority to handling controller request             
            RequestHandler::getInstance()->handle_ctrl_request_uri();
            igk_sys_config_view($file); 
            igk_exit();
        } 
    }
}
