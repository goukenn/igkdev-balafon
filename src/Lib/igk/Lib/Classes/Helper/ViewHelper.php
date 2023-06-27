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
use Exception;
use IGK\Actions\ActionFormOptions;
use IGK\Controllers\BaseController;
use IGK\Helper\Traits\IOSearchFileTrait;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlNoTagNode;
use IGK\System\IO\Path;
use IGK\System\Uri;
use IGKEnvironment;
use IGKEnvironmentConstants;
use IGKException;
use IGKHtmlDoc;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionException;
use function igk_resources_gets as __;

/**
 * view context helper class 
 * @package
 * @method string File() get current view file 
 * @method IGKHtmlDoc Doc() get current document
 * @method HtmlNode TargetNode() get current target node
 */
class ViewHelper
{
    use IOSearchFileTrait;

    const ARG_KEY = "sys://io/query_args";
    const REDIRECT_PARAM_NAME = 'redirect-request-data';
    /**
     * retrieve home directory
     * @param string|null $path 
     * @return mixed 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws IGKException 
     */
    public static function Home(string $path = null){
        $dir = self::CurrentCtrl()->getViewDir();
        if ($path){
            $dir = Path::Combine($dir, $path);
        }
        return $dir;
    }
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
        return function($n)use($handler, $name){
            if (!igk_environment()->isOPS()){
                $pan = $n->div()->panel('igk-danger');
                $pan->Content = __('Action handler not found or method not found'); 
                $n->div()->Content =  'Action : <span class="code-class">'.$handler.'</span>';
                $n->div()->Content =  'method : <span class="code-function">'.$name.'</span>';
            }
        };
    }

    private static function _GetIncArgs($args=null){

        if (is_null($args)|| empty($args))
            $args = self::GetViewArgs(); 
        else {
            $args = is_array($args) ? igk_getv($args, 0, $args) : $args;
        }
        return $args;
    }
    /**
     * get included file: in current view directory
     * @param string $file relative view file in current directory
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _GetIncFile(string $file){          
        // determine if file exists with extension or in view_directory
        include_once IGK_LIB_CLASSES_DIR."/core.func.helper.php";
        $exts = [IGK_VIEW_FILE_EXT ];
        if ($c = self::SearchFile($file, $exts) ?? self::SearchFile(Path::Combine(self::Dir(), $file),$exts)){
            return $c;
        }
        igk_die("inc [".$file."] file not found = ".\IGK\typeof($c));        
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
     * include file
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
        if (isset($ctrl)) {
            $args = get_defined_vars();            
            $fc = (function(){
                extract(func_get_arg(1));
                return include self::_GetIncFile(func_get_arg(0)); 
            })->bindTo($ctrl);
            $r = $fc(func_get_arg(0), $args);   
            return $r;
        }
        return include self::_GetIncFile(func_get_arg(0));
    }
    /**
     * include sub view. from current controller view context
     * @return mixed 
     * @param string $file file to include
     * @param ?array $params override parameters
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
    /**
     * include and render view file
     * @param mixed $file 
     * @param array $args 
     * @return null|string|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     * @throws CssParserException 
     */
    public static function View($file, $args=[]){
        if (self::Include($file, $args)){
            return self::CurrentCtrl()->getTargetNode()->render();
        }
    }
    /**
     * require once 
     * @param mixed $file 
     * @return mixed 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
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

    /**
     * get base uri path
     * @param BaseController $ctrl 
     * @param string $fname 
     * @return string 
     */
    public static function BaseUriPath(BaseController $ctrl, string $fname):string{
        return (new Uri($ctrl::uri($fname)))->getPath();
    }
    /**
     * load article 
     * @param string $article 
     * @param mixed $arguments 
     * @param null|BaseController $ctrl 
     * @return HtmlItemBase 
     * @throws IGKException 
     */
    public static function Article(string $article, ?array $arguments=null, ?BaseController $ctrl = null){
        $ctrl = $ctrl ?? self::CurrentCtrl();
        $file = $ctrl->getArticle($article);
        $n = new HtmlNoTagNode; 
        $n->article($ctrl, $file, $arguments); 
        return $n;
    }
    /**
     * force directory entry
     * @param BaseController $ctrl 
     * @param string $fname 
     * @param mixed $redirect_request 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function ForceDirEntry(BaseController $ctrl, string $fname, &$redirect_request = null)
    {
        if (igk_is_cmd()){
            return;
        }
        $appuri = $ctrl->getAppUri($fname);
        $query = null;
        // $ruri = igk_io_baseuri() . igk_getv(explode('?', igk_io_base_request_uri()), 0);
        if (!empty($q = $_GET)){
            unset($q['rwc']); 
            if (!empty($q)){
                $query ='?'.http_build_query($q); 
            }
        }
        $ruri = igk_io_baseuri() . igk_io_request_uri_path();// igk_getv(explode('?', igk_io_base_request_uri()), 0);
        $buri = strstr($appuri, igk_io_baseuri());
        $entry_is_dir = 0;
        
        if (igk_sys_is_subdomain() && ($ctrl === SysUtils::GetSubDomainCtrl())) {
            $g = igk_getv(parse_url(igk_io_request_uri()), 'path');
            $entry_is_dir = preg_match("/\/$/", $g) || ((strlen($g) > 0) && 
                ($g == '/'.$fname.'/')) 
                || (($fname== IGK_DEFAULT) && (strpos($g, '/')===0));
                
            // igk_wln_e("check :::  ", $entry_is_dir, 'fname:'.$fname, $buri,  "appuri:".$appuri,  $g, igk_io_request_uri());

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
            // igk_trace();
            // igk_wln_e("try redirect on to:", $entry_is_dir, $appuri . "/".$query);
            $ctrl->setParam("redirect_request", [self::REDIRECT_PARAM_NAME => $_REQUEST]);
            igk_navto($appuri . "/".$query);
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
    public static function Dir(?string $path=null): ?string
    {   
        if ($file = self::File()){
            return dirname($file).($path ? $path : "");
        }
        return null;
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
     * retrieve view environment args definition
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
    /**
     * register view environment arg key
     * @param mixed $t 
     * @return void 
     */
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
        $t = self::GetViewContextArgs();
        if (!is_null($param) && $t ){
            return igk_getv($t, $param, $default);
        }
        return $t ?? [];
    } 
    /**
     * 
     * @return mixed|ViewEnvironmentArgs 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetViewContextArgs(?string $filter_context = null){
        $tab =  igk_get_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS);
        if ($filter_context){
            return (array)igk_createobj_filter($tab, $filter_context);
        }
        return $tab;

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
        !is_file($f) && ($f.IGK_VIEW_FILE_EXT);
        return $f;
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
    /**
     * get views manifest
     * @param bool $recursive 
     * @param mixed $pattern 
     * @param null|BaseController $controller 
     * @return null|array 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws IGKException 
     */
    public static function GetViews($recursive=true, $pattern = null, ?BaseController $controller =null){
        $ctrl = $controller ?? self::CurrentCtrl();
        $v_view_dir = $ctrl->getViewDir();
        $rsc = IO::GetFiles($ctrl->getViewDir(), "/\\".IGK_VIEW_FILE_EXT."$/",$recursive);       
        $rsc = array_filter(array_map( function($c)use($v_view_dir, $pattern){
            $v = substr($c,strlen($v_view_dir));
            if ($pattern){
                if (!preg_match($pattern, $v)){
                    return null;
                }
            }
            return igk_str_rm_last($v, IGK_VIEW_FILE_EXT );
        }, $rsc));
        return $rsc;
    }
}
