<?php
//
// @file: View.php
// @desc: helper view description files
// @author: C.A.D BONDJE DOUE
//
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGKEnvironment;
use IGKEnvironmentConstants;
use IGKException;
use IGKHtmlDoc;

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

    private static function _GetIncArgs($args=null){

        if (is_null($args)|| empty($args))
            $args = self::GetViewArgs(); 
        return $args;
    }
    private static function _GetIncFile($file){
        $c = self::Dir()."/".$file;
        if (file_exists($c) || ($c.= ".phtml"))
            return $c;
        igk_die("inc [".$file."] file not found");        
    }
    /**
     * get entry fname uri
     * @param string $name path or name
     * @return string
     */
    public static function Uri(?string $name=null){
        $ctrl = self::CurrentCtrl();
        $fname = self::GetViewArgs("fname");
        if (igk_str_endwith($fname, $s = "/".IGK_DEFAULT_VIEW)){
            $fname = substr($fname, 0, strlen($fname) - strlen($s));
        }
        if (strpos($name, "/")===0){
            return $ctrl::uri($fname.$name);
        }
        $uri = $ctrl::getRouteUri($name);
        return $uri;

    }
    /**
     * 
     * @param string $path 
     * @param array|null $args 
     * @return mixed 
     * @throws IGKException 
     */
    public static function Inc(){
        if(func_num_args()==0){
            igk_die("missing file argument");
        }
        extract(self::_GetIncArgs(array_slice(func_get_args(),1))); 
        return include self::_GetIncFile(func_get_arg(0));
    }
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
        return  igk_environment()->last(IGKEnvironmentConstants::VIEW_FILE_CACHES);
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
    public static function BaseController(): ?BaseController{
        return SysUtils::CurrentBaseController();
    }
    /**
     * get controller current document
     * @return IGKHtmlDoc 
     */
    public static function CurrentDocument(){
        return self::CurrentCtrl()->getCurrentDoc();
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
  
    public static function GetUriHelper($fname){
        $ctrl = self::CurrentCtrl();
        return new ViewUriHelper($ctrl, $fname);
    }
    /**
     * get User Profiles access
     * @return object 
     */
    public static function GetUserProfile(){
        return self::CurrentCtrl()->getUser(); 
    }

    public static function GetCheckUserProfile(bool $redirect, ?string $uri=null){
        $c = self::CurrentCtrl();
        return $c->checkUser($redirect, $uri) ? 
            $c->getUser():
            null;
    }
}
