<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Path.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\IO;
use Exception;
use IGK\Constants;
use IGK\Helper\StringUtility as str_helper;
use IGK\Helper\IO;
use IGK\Helper\Traits\IOPathCheckerTrait;
use IGKApp;
use IGKException; 

require_once IGK_LIB_CLASSES_DIR .'/Helper/Traits/IOPathCheckerTrait.php';
/**
 * core path manipulation class 
 * @package IGK\System\IO
 */
class Path
{
    use IOPathCheckerTrait;
    /**
    * Path to lib dir.
    * @var mixed
    */
    protected $lib_dir;
    /**
    * Path to class dir.
    * @var mixed
    */
    protected $class_dir;
    /**
    * Path to app dir.
    * @var mixed
    */
    protected $app_dir;
    /**
    * Path to package dir.
    * @var mixed
    */
    protected $package_dir;
    /**
    * Path to vendor dir.
    * @var mixed
    */
    protected $vendor_dir;
    /**
    * Path to base dir.
    * @var mixed
    */
    protected $base_dir;
    /**
    * Path to project dir.
    * @var mixed
    */
    protected $project_dir;
    /**
    * Path to module dir.
    * @var mixed
    */
    protected $module_dir;
    /**
    * Path to data dir.
    * @var mixed
    */
    protected $data_dir;
    /**
    * Path to sys data dir.
    * @var mixed
    */
    protected $sys_data_dir;
    /**
    * Path to css path.
    * @var mixed
    */
    protected $css_path;
    /**
    * Path to backup dir.
    * @var mixed
    */
    protected $backup_dir;
    /**
    * Path to home dir.
    * @var mixed
    */
    protected $home_dir;
    /**
    * Path to temp dir.
    * @var mixed
    */
    protected $temp_dir;
    /**
    * Cache: cache dir.
    * @var mixed
    */
    protected $cache_dir;
    /**
    * Path to public assets dir.
    * @var mixed
    */
    protected $public_assets_dir;
    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;
    /**
     * get temp directory 
     * @return mixed 
     */
    public function getTempDir()
    {
        return $this->temp_dir;
    }
    /**
    * Returns Extension.
    * @param mixed $path
    */
    public static function GetExtension($path)
    {
        if (empty($path))
            return null;
        if (($pos = strrpos($path, '.')) !== false) {
            return substr($path, $pos);
        }
        return ($t = explode(".", $path)) > 1 ? array_pop($t) : "";
    }
    /**
     * get existing file
     * @param mixed $path 
     * @param mixed $extension 
     * @return bool 
     */
    public static function GetExistingFile(&$path, array $extension = []): bool
    {
        if (igk_io_file_exists($path)) {
            return true;
        }
        while (count($extension) > 0) {
            $q = array_shift($extension);
            if (igk_io_file_exists($g = $path . $q)) {
                $path = $g;
                return true;
            }
        }
        return false;
    }
    /**
     * get system path instance
     * @return self path instance
     */
    public static function getInstance()
    {
        if (self::$sm_instance === null) {
            self::$sm_instance = new static();
        }
        return self::$sm_instance;
    }
    /**
     * get the backup directory
     * @return mixed 
     */
    public function getBackupDir()
    {
        return $this->backup_dir;
    }
    /**
     * get public asset directory
     * @return string 
     */
    public function getPublicAssetDir():string{
        return $this->public_assets_dir;
    }
    /**
     * get module directory
     * @return mixed 
     */
    public function getModuleDir()
    {
        return $this->module_dir;
    }
    /**
     * get cache directory 
     * @return mixed 
     */
    /**
     * get configured cached directory 
     * @return mixed 
     */
    public function getCacheDir(){
        if (IGKApp::IsInit() && !defined('IGK_CACHED_DIR')){
            return igk_configs()->get('cache_dir') ?: $this->cache_dir;
        }
        return  $this->cache_dir;
    }
    /**
    * auto generate doc.
    */
    public function prepareData()
    {
        if (!defined('IGK_BASE_DIR')){   
            igk_environment()->isDev() && igk_trace();         
            igk_dev_wln_e(__FILE__.":".__LINE__ , 'please setup IGK_BASE_DIR before.');
        }
        $v_is_webapp = igk_is_webapp();
        $this->app_dir = str_helper::Uri(IGK_APP_DIR);
        $this->base_dir = str_helper::Uri(IGK_BASE_DIR);
        $this->lib_dir = str_helper::Uri(IGK_LIB_DIR);
        $this->project_dir = str_helper::Uri(IGK_PROJECT_DIR);
        $this->package_dir = str_helper::Uri(IGK_PACKAGE_DIR);
        $this->module_dir = str_helper::Uri(IGK_MODULE_DIR);
        $this->class_dir = str_helper::UriCombine(IGK_LIB_DIR, IGK_LIB_FOLDER, IGK_CLASSES_FOLDER);
        $this->cache_dir = (defined('IGK_CACHED_DIR') ? constant('IGK_CACHED_DIR') : null) ?? $this->app_dir . DIRECTORY_SEPARATOR . IGK_CACHE_FOLDER;
        $this->public_assets_dir = Path::Combine($this->base_dir, IGK_RES_FOLDER);
        if ($v_is_webapp && $this->cache_dir && !is_dir($this->cache_dir)){
            IO::CreateDir($this->cache_dir, IGK_DEFAULT_CACHE_FOLDER_MASK);
        } 
        if ($v_is_webapp && $this->public_assets_dir && !is_dir($this->public_assets_dir)){
            // + | init create asset directory for web app
            IO::CreateDir($this->public_assets_dir, IGK_DEFAULT_CACHE_FOLDER_MASK);
        }
        $b = ["v" => IGK_VERSION];
        if (igk_environment()->isDev() && igk_getr("XDEBUG_TRIGGER")) {
            $b["XDEBUG_TRIGGER"] = 1;
        }
        http_build_query($b);
        $this->css_path = '/' . str_helper::uri(implode("/", [IGK_RES_FOLDER, IGK_STYLE_FOLDER, "balafon.css?" . http_build_query($b)]));
        $this->vendor_dir = str_helper::UriCombine(IGK_APP_DIR, IGK_PACKAGES_FOLDER . "/vendor");
        $this->sys_data_dir = str_helper::UriCombine(IGK_APP_DIR, IGK_DATA_FOLDER);
        $this->data_dir = str_helper::UriCombine(IGK_APP_DIR, IGK_DATA_FOLDER);
        if (defined('IGK_BACKUP_DIR')) {
            $this->backup_dir = constant('IGK_BACKUP_DIR');
        } else {
            $this->backup_dir = str_helper::UriCombine($this->data_dir, 'Backup');
        }
        $this->home_dir = igk_getv($_SERVER, "HOME", "~");
        $this->temp_dir = defined('IGK_TEMP_DIR') ? constant('IGK_TEMP_DIR') : sys_get_temp_dir();
    }
    /**
     * get home dir
     * @return null|string 
     */
    public function getHomeDir(): ?string
    {
        return $this->home_dir;
    }
    /**
    * .ctr
    * @return
    */
    private function __construct()
    {
        $this->prepareData();
    }
    /**
    * Returns Packages Dir.
    */
    public function getPackagesDir()
    {
        return $this->package_dir;
    }
    /**
    * auto generate doc.
    * @return string
    */
    public function getStyleUri()
    {
        $d = $this->css_path;
        $t = explode('?', $d,2);
        $d = array_shift($t); 
        $u = [$this->baseuri($d)]; 
        if ($t){
            $u[] = $t[0];
        }
        return implode('?', $u); 
    }
    /**
     * retrieve setup system app directory
     * @return ?string 
     */
    public function getApplicationDir()
    {
        return $this->app_dir;
    }
    /**
     * retrieve setupe system class directory
     * @return mixed 
     */
    public function getClassDir()
    {
        return $this->class_dir;
    }
    /**
    * auto generate doc.
    * @return string get server root directory
    */
    public function getRootDir()
    {
        return igk_server()->root_dir;
    }
    /**
    * Returns Base Dir.
    */
    public function getBaseDir()
    {
        return $this->base_dir;
    }
    /**
    * Returns Data Dir.
    */
    public function getDataDir()
    {
        return $this->data_dir;
    }
    /**
     * return sys data directory
     * @return string 
     */
    public function getSysDataDir(): string
    {
        return $this->sys_data_dir;
    }
    /**
    * auto generate doc.
    * @param mixed|null $dir
    * @return string base dir
    */
    public function basedir($dir = null)
    {
        $bdir = igk_environment()->get("basedir", $this->base_dir);
        if (!$bdir) {
            return null;
        }
        if ($dir == null)
            return $bdir;
        $l = igk_dir($bdir);
        $_r = null;
        if (igk_io_file_exists($dir, true) && (($hdir = igk_dir($dir)) == igk_realpath($dir))) {
            $rpath = self::GetRelativePath($hdir, $l);
            $_r = ($rpath) ? igk_dir($l . DIRECTORY_SEPARATOR . $rpath) : $dir;
        } else {
            $s = str_replace("\\", "\\\\", $l);
            $egext = "#^(" . $s . ")#";
            $dir = igk_dir($dir);
            $_r = ($s && preg_match($egext, $dir)) ?
                $dir :  $bdir . "/" . $dir;
        }
        return  !is_null($_r) ? igk_uri($_r) : null;
    }
    /**
     * get full base uri
     * @param mixed $dir : relativepath 
     * @param mixed $secured force secure path
     * @param mixed $path output path-info
     * @return string|false|null 
     * @throws IGKException 
     */
    public function baseuri($dir = null, $secured = null, &$path = null): ?string
    {
        if (!is_null($baseURI = igk_environment()->get("baseURI"))) {
            return implode("/", array_filter([$baseURI, $dir]));
        }
        $secured = $secured === null ? igk_getv($_SERVER, 'HTTPS') == 'on' : $secured;
        $path = null;
        $out = IGK_STR_EMPTY;
        $v_dir = $this->basedir($dir);
        $root = $this->getRootDir();
        if (!($s = strstr($v_dir, $root))) {
            return null;
        }
        $t = trim(str_helper::uri(substr($v_dir, strlen($root))), '/');
        if ($secured) {
            $out = 'https://';
        } else {
            $out = 'http://';
        }
        $n = rtrim(igk_server_name(), '/');
        if (!empty($n))
            $out .= $n;
        if ($c = IO::GetPort($secured)) {
            $out .= ':' . $c;
        }
        if (!empty($t))
            $out .= '/' . $t;
        $out = str_replace('\\', '/', $out);
        $path = $t;
        $s = str_helper::uri($out);
        while ($path && str_helper::EndWith($s, '/') && (($k = strlen($s)) > 0)) {
            $s = substr($s, 0, $k - 1);
        }
        return $s;
    }
    /**
     * get real path
     * @param mixed $path 
     * @return string|false|null 
     * @throws IGKException 
     */
    public function realpath(string $path)
    {
        $o = "";
        $path = str_helper::uri($path);
        $offset = 0;
        if ($o = realpath($path)) {
            return $o;
        } else {
            // + | check 
            $found = 0;
            while (($pos = strpos($path, "../", $offset)) !== false) {
                $found = 1;
                if (!($ch = realpath(substr($path, 0, $pos + 3)))) {
                    return false;
                }
                $path = str_helper::uri($ch) . "/" . substr($path, $pos + 3);
                $offset = strlen($ch);
            }
            if (!$found)
                return null;
        }
        return $path;
    }
    /**
     * retreive an 
     * @param ?string $dir 
     * @param string $sep 
     * @return mixed 
     * @throws IGKException 
     * @throws Exception 
     */
    public function basepath(?string $dir, $sep = DIRECTORY_SEPARATOR)
    {
        $p = $this->realpath($dir);
        if (empty($p)) {
            if (is_file($dir)) {
                if (strstr($dir, IGK_LIB_DIR)) {
                    return '%lib%' . substr($dir, strlen(IGK_LIB_DIR));
                }
                return $dir;
            }
            return null;
        }
        if (is_link($dir)) {
            return $this->baserelativepath(realpath($dir));
        }
        return $this->baserelativepath($p, null, $sep);
    }
    /**
    * auto generate doc.
    */
    public function baserelativepath($dir, $basedir = null, $sep = DIRECTORY_SEPARATOR)
    {
        if (empty($dir)) {
            return IGK_STR_EMPTY;
        }
        $dir = str_helper::uri($dir);
        $bdir = str_helper::uri($basedir == null ? $this->basedir() : $basedir);
        return $this->relativepath($dir, $bdir);
    }
    /**
    * auto generate doc.
    */
    public function relativepath($spath, $link)
    {
        if (is_dir($link)) {
            $link = rtrim($link, "/") . "/";
        }
        return self::GetRelativePath(str_helper::uri($spath), str_helper::uri($link));
    }
    /**
     * Get relative path
     * @param mixed $source 
     * @param mixed $target 
     * @return string|null 
     */
    public static function GetRelativePath(string $source, string $target, string $separator = DIRECTORY_SEPARATOR)
    {
        $vsource = igk_uri($source);
        $vtarget = igk_uri($target);
        if ($vsource == $vtarget) {
            return './';
        }
        $v_cpath = null;
        $v_found = false;
        $v_count = 0;
        $v_cp = [];
        if (substr($vtarget, -1) == '/') {
            $v_cp[] = '';
        }
        while (($v_cpath = dirname($vtarget)) && ($vtarget != $v_cpath)) {
            array_unshift($v_cp, basename($vtarget));
            if (strpos($vsource, $v_cpath) === 0) {
                $v_found = true;
                break;
            }
            $vtarget = $v_cpath;
        }
        if ($v_found || ($vtarget == '/')) {
            $l = '';
            if (strpos($vsource, $v_cpath) !== 0) { 
                igk_die("no matching relative path");
            }
            if ($v_cpath == '/') {
                $v_cpath = '';
            }
            $l = substr($vsource, strlen($v_cpath) + 1);
            if (empty($l) || (strpos($l, "/") === false)) {
                $v_count = 0;
            } else {
                $v_count  = count(explode('/', ltrim($l, '/'))) - 1;
            }
            $out = '';
            $out = $v_count == 0 ? './' : str_repeat("../", $v_count);
            $out .= implode("/", $v_cp);
            if ($separator != '/') {
                $out = str_replace('/', $separator, $out);
            }
            return $out;
        }
        return null; 
    }
    /**
    * auto generate doc.
    * @param string $path
    * @return mixed
    */
    public static function LocalPath(string $path)
    {
        return igk_io_expand_path(
            igk_io_collapse_path($path)
        );
    }
    /**
     * combine path 
     * @param array $path 
     * @return string 
     */
    public static function Combine(...$path)
    {
        $sep = '/';
        $filter_callback = function($a){
            return !is_null($a) && (is_numeric($a) || (is_string($a) && !empty($a)));
        };
        $path = array_values(array_filter(array_values($path), $filter_callback));
        if ($path) {
            $p = rtrim($path[0], $sep);           
            $path = array_slice($path, 1);
            $path = array_map(self::class . "::TrimDir", $path);
            $r = '';
            if (is_numeric($p) || !empty($p)){
                array_unshift($path, $p);
            }
            else{
                $r = $sep;
            }
            return $r.igk_uri(implode($sep, array_filter($path, $filter_callback)));
        }
        return null;
    }
    /**
     * trim directory separator
     * @param mixed $a 
     * @return string 
     */
    public static function TrimDir(?string $a = null, $sep = DIRECTORY_SEPARATOR)
    {
        return trim($a ?? '', $sep);
    }
    /**
     * search for file directory
     * @param string $path 
     * @param array $exts 
     * @param null|array $dirs 
     * @return string|null 
     */
    public static function SearchFile(string $path, array $exts, ?array $dirs=null){ 
           if (is_file($path)){
                return $path;
           }
            $sb = array_merge([""], $exts ?? []);
            if (is_null($dirs)){
                $pdir = dirname($path);
                if (is_dir($pdir) ) {
                    $dirs = [''];
                } else {
                    $dirs = [getcwd()];
                }
            }
            while(count($dirs)>0){
                $dir = array_shift($dirs);
                foreach($sb as $p){
                    $q = self::CombineAndFlattenPath($dir, $path).$p;
                    if (is_file($q)){
                        return $q;
                    }
                }
            }
            return null; 
    }
    /**
     * flatten path 
     * @param string $path 
     * @return string 
     */
    public static function FlattenPath(string $path)
    {
        $s = trim($path);
        if (strpos($s, '../') !== false) {
            $g = explode('../', $s);
            $p = "";
            while (count($g) > 0) {
                $q = array_shift($g);
                if (empty($p)) {
                    $p = rtrim($q, '/');
                    continue;
                }
                $p = dirname($p);
                if (empty($q)) {
                    continue;
                } else {
                    $p = self::Combine($p, $q);
                }
            }
            $s = $p;
        }
        $s = str_replace("/./", "/", $s); 
        if (igk_str_endwith($s, '/.')){
            $s = rtrim(substr($s, 0,-1),'/');
        }
        return $s;
    }
    /**
     * combine an flatten path
     * @param ?string[] $path 
     * @return string 
     */
    public static function CombineAndFlattenPath(...$path)
    {
        return self::FlattenPath(self::Combine(...$path));
    }
    /**
     * detect that path is in library
     * @param string $path 
     * @return bool 
     * @throws IGKException 
     */
    public static function IsInLibrary(string $path): bool
    {
        return self::DetectPathMode($path) == 'lib';
    }
    /**
     * chane name extension 
     * @param string $path 
     * @param string $new_extension 
     * @return string 
     */
    public static function ChangeExtensionTo(string $path, string $new_extension): string{
        return self::Combine(dirname($path), igk_io_basenamewithoutext($path).$new_extension);
    }
    /**
     * detect path mode 
     * @param string $path 
     * @return ?string 
     */
    public static function DetectPathMode(string $path): ?string
    {
        $p = igk_io_collapse_path($path);
        if (preg_match(Constants::PATH_VAR_DETECT_MODEL_REGEX, $p, $tab)) {
            return $tab['name'];
        }
        return null;
    }
    /**
     * resolve path with include path list 
     * @param string $path 
     * @return false|string 
     */
    public static function ResolvePath(string $path, ?array $include_pathlist =null){
        if (is_null($include_pathlist)){
            $include_pathlist = get_include_path();
        }
        if (($p = realpath($path))===false){
            $t = array_filter(array_map(function($n)use($path){
                if (igk_io_file_exists($f = self::CombineAndFlattenPath($n, $path))){
                    return $f;
                }
                return null;
            }, explode( PATH_SEPARATOR, $include_pathlist)));
            if ($t){
                $p = array_shift($t);
            }
        }
        return $p;
    }
    /**
 * get string local path
 */
    public static function ToLocalPath(string $path, ?string $cwd=null):string{
    $cwd = $cwd ?? getcwd();
    $c = igk_uri($path);
    $absolute = false;
    if (PHP_OS_FAMILY=='Window'){
        $absolute = !preg_match('/^([a-zA-Z]:|\/\/)/', $c);
    } else {
        $absolute = ($c[0] == '/');
    }
    if (!$absolute){
        $c = Path::FlattenPath(Path::Combine($cwd, $c));
    } 
    return igk_dir($c);
}
    /**
    * Sub local path.
    * @param string $path
    * @param string $cwd
    * @return ?string
    */
    public static function SubLocalPath(string $path, string $cwd): ?string{
     $g = self::ToLocalPath($path, $cwd);
return \IGK\System\IO\Path::GetRelativePath($cwd, $g); //
}
}