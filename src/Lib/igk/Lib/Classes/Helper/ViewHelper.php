<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewHelper.php
// @date: 20220803 13:48:58
// @desc: 

//
// @file: View.php
// @desc: helper view description files
// @author: C.A.D BONDJE DOUE
//
namespace IGK\Helper;

use Closure;
use IGK\Actions\ActionFormOptions;
use IGK\Controllers\BaseController;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKEnvironment;
use IGKEnvironmentConstants;
use IGKException;
use IGKHtmlDoc;
use ReflectionException;
use function igk_resources_gets as __;

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

    /**
     * must handle a form class 
     * @param string $name 
     * @param ActionFormOptions|array $name 
     * @return null|callable 
     */
    public static function Form(string $name, $options = null){ 
        //get action handler
        $handler = self::GetViewArgs("action_handler"); 
        if ($handler){
            if ($options)
                if (is_array($options)){
                    $options = Activator::CreateNewInstance(ActionFormOptions::class, $options);
                } 
            if (method_exists($handler,'Form')){ 
                // call a static form 
                return $handler::Form($name, $options);
            }
        }
        return function($n){
            if (!igk_environment()->isOPS()){
                $pan = $n->div()->panel('igk-danger');
                $pan->Content = __('Action handler not found');
                $pan->div()->Content = "[".$n."]";
                igk_trace();
                igk_exit();
                $n->div()->Content =  self::GetViewArgs("action_handler");
            }
        };
    }

    private static function _GetIncArgs($args=null){

        if (is_null($args)|| empty($args))
            $args = self::GetViewArgs(); 
        return $args;
    }
    private static function _GetIncFile($file){        
        $c = self::Dir()."/".$file;
        if (file_exists($c) || file_exists($c.= IGK_VIEW_FILE_EXT))
            return $c;
        igk_die("inc [".$file."] file not found");        
    }

    /**
     * import view file
     * @param string $file 
     * @param null|array $param 
     * @param null|BaseController $controller 
     * @return Closure 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Import( string $file, ?array $param=null , ?BaseController $controller=null):Closure{
        if (is_null($controller) && is_null($controller = self::CurrentCtrl())){
            igk_die("controller required.");
        }
        if (!is_file($file)){
            if (!is_file($file =  ViewHelper::GetView($file))){
                igk_die("file not found");
            }
        }
        return function($a)use($file, $controller, $param){
             /**
             * @var string file 
             * @var array params
             */
            $tg = ["t"=>$a];
            if (!is_null($param)){
                $tg["params"] = $param;
            }
            $binIncude = InvocationHelper::Include()->bindTo($controller);
            $g = $controller->getTargetNode(); 
            // replace target node 
            $controller->setTargetNode($a);            
            $o = $binIncude($file, array_merge(
                ViewHelper::GetViewArgs(),
                $controller->getExtraArgs(),
                $tg 
            ));
            $controller->setTargetNode($g);
            return $o;
        };
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
     * include path argument
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
    /**
     * include sub view. from current controller view context
     * @return mixed 
     * @param string $file file to include
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Include(){
        if(func_num_args()==0){
            igk_die("missing file argument");
        }
        if(func_num_args()>2){
            igk_die("too many argument passed");
        }
        if((func_num_args()>1) && (is_array(func_get_arg(1)))){
            extract(func_get_arg(1));
            $params = func_get_arg(1);
        }
        extract(self::GetViewArgs(), EXTR_SKIP); 
        if (!isset($ctrl)){
            igk_die('$ctrl not found from GetViewArgs');
        }
        extract($ctrl->getExtraArgs(), EXTR_SKIP);
        $_tab = get_defined_vars(); 
        $g = (function(){
            extract(func_get_arg(1)); 
            return include(func_get_arg(0));
        })->bindTo($ctrl);
        if (!is_file($file = func_get_arg(0))){
            file_exists($file = self::GetView($file)) || igk_die("failed to resolv file: ".$file);
        }
        return $g($file, $_tab);
    }
    public static function View($file, $args=[]){
        if (self::Include($file, $args)){
            return self::CurrentCtrl()->getTargetNode()->render();
        }
    }
    public static function RequireOnce($file){
        if (!file_exists($file = func_get_arg(0))){
            file_exists($file = self::GetView($file)) || igk_die("failed to resolv file: ".$file);
        }
        $ctrl = self::CurrentCtrl();
        $g = (function(){
            extract(self::GetViewArgs(), EXTR_SKIP); 
            extract($ctrl->getExtraArgs(), EXTR_SKIP);
            return require_once(func_get_arg(0));
        })->bindTo($ctrl);
        return $g($file);
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
            // + | --------------------------------------------------------
            // + | Sanitize request uri
            // + | 
            $ctrl->setParam("redirect_request", ['request' => $_REQUEST]);
            igk_navto($appuri . "/");
        } else {
            $redirect_request = $ctrl->getParam("redirect_request");
            $ctrl->setParam("redirect_request", null);
        }
        self::CurrentDocument()->setBaseUri($appuri."/");
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
     * @throws Deprecated 
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
     * return variable passed on a top view.
     * @param ?string $param by passing null you ask to get all data
     * @param mixed $default by passing a key to param return the default value 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetViewArgs(?string $param=null,$default=null){
        $t = igk_get_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS);
        if (!is_null($param) && $t ){
            return igk_getv($t, $param, $default);
        }
        return $t ?? [];
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
    /**
     * view dir
     * @param null|string $path 
     * @return string  
     */
    public static function GetView(?string $path=null){
        $f = implode("/", array_filter([self::CurrentCtrl()->getViewDir(), ltrim($path ?? "", "/")]));
        !is_file($f) && ($f.='.phtml');
        return $f;
        // return implode("/", array_filter([self::CurrentCtrl()->getViewDir(), ltrim($path ?? "", "/")]));
    }


    /**
     * resolv view file and get attached params
     * @param string $viewDir root view directory
     * @param string $view demand
     * @param string $file file
     * @param int $check enable check
     * @param mixed $param params to return
     * @return null|string 
     */
    public static function ResolvViewFile(string $viewDir, string $view, string $f, $checkfile=1, & $param=null): ?string{
        $s = null;
        $extension = IGK_DEFAULT_VIEW_EXT;
        $ext = $extension;
        $ext_regex = '/\.' . $extension . '$/i';
        $ext = preg_match($ext_regex, $view) ? '' : '.' . $ext;
        $f = $f . $ext; 
        if (!empty($ext)) {
            $ts = 1;
            $_views = array_filter(explode("/", $view));
            while ($ts && (count($_views) > 0) && ($f != $viewDir)) {
               
                if (preg_match($ext_regex, $f) && is_file($f)) {
                    return $f;
                } else {
                    $bname = basename($f);
                    $f = dirname($f);
                    $checks = [
                        $f."/".$bname,
                        $f."/".$bname.".".$extension
                    ];
                    while(count($checks)>0){
                        $rdir = array_shift($checks);                        
                        if (is_file($rdir)){
                            return $rdir;
                        }
                        if (is_dir($rdir)){
                            if (file_exists($c = $rdir."/".IGK_DEFAULT_VIEW)){
                                return $c;
                            }
                        }
                    }

                    if (($bname != IGK_DEFAULT_VIEW_FILE) && (
                        file_exists($c = $f . "/" . IGK_DEFAULT_VIEW_FILE))) {
                        if (!in_array($bname, [IGK_DEFAULT_VIEW])) {
                            array_unshift($param, array_pop($_views));
                        }
                        return $c;
                    } else {
                        array_unshift($param, array_pop($_views));
                    }
                }
            }
            // if ($s) {
                $s =  $f . "/" . IGK_DEFAULT_VIEW . '.' . $extension;
            //}
        }else {
            if (!$checkfile || ($checkfile && is_file($f))){                
                $s = $f;
            }
        }
     
        return $s;
    }
}
