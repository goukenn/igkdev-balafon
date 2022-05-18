<?php
//
// @file: View.php
// @desc: helper view description files
// @author: C.A.D BONDJE DOUE
//
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGKEnvironment;
use IGKException;

/**
 * view helper class 
 * @package
 * @method string File() get current view file 
 * @method IGKHtmlDoc Doc() get current document
 * @method HtmlNode TargetNode() get current target node
 */
class ViewHelper
{
    const ARG_KEY = "sys://io/query_args";

    public static function ForceDirEntry(BaseController $ctrl, string $fname, &$redirect_request = null)
    {
        $appuri = $ctrl->getAppUri($fname);
        // $ruri = igk_io_baseuri() . igk_getv(explode('?', igk_io_base_request_uri()), 0);
        $ruri = igk_io_baseuri() . igk_io_request_uri_path();// igk_getv(explode('?', igk_io_base_request_uri()), 0);
        $buri = strstr($appuri, igk_io_baseuri());
        $entry_is_dir = 0;
        if (igk_sys_is_subdomain() && ($ctrl === SysUtils::GetSubDomainCtrl())) {
            $g = igk_io_base_request_uri();
            $entry_is_dir = (strlen($g) > 0) && ($g[0] == "/");
        } else {
            $s = "";
            if (strstr($ruri, $buri)) {
                $s = substr($ruri, strlen($buri));
                $entry_is_dir = (strlen($s) > 0) && $s[0] == "/";
            }
        }

        

        if (!$entry_is_dir) {
            $ctrl->setParam("redirect_request", ['request' => $_REQUEST]);
            igk_navto($appuri . "/");
        } else {
            $redirect_request = $ctrl->getParam("redirect_request");
            $ctrl->setParam("redirect_request", null);
        }
    }
    /**
     * get include file
     * @return string
     */
    public static function File()
    {
        return  igk_environment()->last("viewFileCaches");
    }
    /**
     * get included file directory
     * @return string 
     * @throws IGKException 
     */
    public static function Dir(?string $path=null)
    {
        return dirname(self::File()).($path ? $path : "");
    }
    /**
     * get current controller
     * @return null|BaseController current controller
     */
    public static function CurrentCtrl(): ?BaseController
    {
        return igk_environment()->get(IGKEnvironment::CURRENT_CTRL);
    }

    /**
     * retrieve global args definition
     * @param mixed $n 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetArgs($n = null, $default = null)
    {
        $s = igk_environment()->get(self::ARG_KEY);
        if ($n == null)
            return $s;
        return igk_getv($s, $n, $default);
    }
    public static function RegisterArgs($t){
        igk_set_env(self::ARG_KEY, $t);
    } 

    /**
     * return view args
     * @param mixed $param 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetViewArgs($param=null,$default=null){
        $t =  igk_get_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS);
        if (!is_null($param) && $t ){
            return igk_getv($t, $param, $default);
        }
        return $t;
    } 
  
}
