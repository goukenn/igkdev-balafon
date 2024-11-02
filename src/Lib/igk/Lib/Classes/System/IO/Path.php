<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Path.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\IO;

use Exception;
use IGK\Helper\StringUtility as str_helper;
use IGK\Helper\IO;
use IGKException; 

///<summary>core path manipulation class</summary>
///<note>for better directory manipulation.</note>
/**
 * core path manipulation class 
 * @package IGK\System\IO
 */
class Path
{

    protected $lib_dir;
    protected $class_dir;
    protected $app_dir;
    protected $package_dir;
    protected $vendor_dir;
    protected $base_dir;
    protected $project_dir;
    protected $module_dir;
    protected $data_dir;
    protected $sys_data_dir;
    protected $css_path;
    protected $backup_dir;
    protected $home_dir;
    protected $temp_dir;
    protected $cache_dir;
    protected $public_assets_dir;


    private static $sm_instance;

    /**
     * get temp directory 
     * @return mixed 
     */
    public function getTempDir()
    {
        return $this->temp_dir;
    }
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
        if (file_exists($path)) {
            return true;
        }
        while (count($extension) > 0) {
            $q = array_shift($extension);
            if (file_exists($g = $path . $q)) {
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
    public function getCacheDir(){
        return $this->cache_dir;
    }
    public function prepareData()
    {
        if (!defined('IGK_BASE_DIR')){
            igk_trace();
            igk_wln_e('please setup IGK_BASE_DIR before.');
        }
        $v_is_webapp = igk_is_webapp();
        $this->app_dir = str_helper::Uri(IGK_APP_DIR);
        $this->base_dir = str_helper::Uri(IGK_BASE_DIR);
        $this->lib_dir = str_helper::Uri(IGK_LIB_DIR);
        $this->project_dir = str_helper::Uri(IGK_PROJECT_DIR);
        $this->package_dir = str_helper::Uri(IGK_PACKAGE_DIR);
        $this->module_dir = str_helper::Uri(IGK_MODULE_DIR);
        $this->class_dir = str_helper::UriCombine(IGK_LIB_DIR, IGK_LIB_FOLDER, IGK_CLASSES_FOLDER);


        $this->cache_dir =  $this->app_dir . DIRECTORY_SEPARATOR . IGK_CACHE_FOLDER;
        $this->public_assets_dir = Path::Combine($this->base_dir, IGK_RES_FOLDER);
        // check an create cache folder on init - build - hook - context 
        if ($v_is_webapp && $this->cache_dir && !is_dir($this->cache_dir)){
            // create cache directory for web app
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
        // used to resolve symbolic links
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
    private function __construct()
    {
        $this->prepareData();
    }
    public function getPackagesDir()
    {
        return $this->package_dir;
    }
    /**
     * 
     * @return string 
     */
    public function getStyleUri()
    {
        return $this->baseuri($this->css_path);
    }
    public function getApplicationDir()
    {
        return $this->app_dir;
    }
    public function getClassDir()
    {
        return $this->class_dir;
    }
    /**
     * 
     * @return string get server root directory 
     */
    public function getRootDir()
    {
        return igk_server()->root_dir;
    }
    public function getBaseDir()
    {
        return $this->base_dir;
    }
    public function getDataDir()
    {
        return $this->data_dir;
    }
    /**
     * return sys data directory
     * @return mixed 
     */
    public function getSysDataDir()
    {
        return $this->sys_data_dir;
    }


    /**
     * 
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
        if (file_exists($dir) && (($hdir = igk_dir($dir)) == igk_realpath($dir))) {
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
            //check if contains
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
    public function baserelativepath($dir, $basedir = null, $sep = DIRECTORY_SEPARATOR)
    {
        if (empty($dir)) {
            return IGK_STR_EMPTY;
        }
        $dir = str_helper::uri($dir);
        $bdir = str_helper::uri($basedir == null ? $this->basedir() : $basedir);
        return $this->relativepath($dir, $bdir);
    }
    public function relativepath($spath, $link)
    {
        if (is_dir($link)) {
            $link = rtrim($link, "/") . "/";
        }
        return self::GetRelativePath(str_helper::uri($spath), str_helper::uri($link));

        // $d1 = explode("/", ltrim(str_helper::uri($spath), "/"));
        // $d2 = explode("/", ltrim(str_helper::uri($link), "/"));
        // $i = 0;
        // $c1 =count($d1);
        // while(($i < $c1) && ($d = array_shift($d2)) && ($d == $d1[$i])){
        //     //determine        
        //     $i++;
        // }
        // if ($i==0){
        //     return false; // die("path not match");
        // }
        // $dnew = str_repeat("../", count($d2)). implode("/", array_slice($d1,$i));
        // return $dnew;
    }


    ////<summary>get relative path</summary>
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
            // retrieve start directory to source 
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
                igk_die("no matching");
            }
            if ($v_cpath == '/') {
                $v_cpath = '';
            }
            $l = substr($vsource, strlen($v_cpath) + 1);
            if (empty($l) || (strpos($l, "/") === false)) {
                // found is in subfolder 
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
     * 
     * @param string $path 
     * @return mixed 
     * @throws IGKException 
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
        $path = array_values(array_filter(array_values($path)));
        if ($path) {
            $p = rtrim($path[0], $sep);           
            $path = array_slice($path, 1);
            $path = array_map(self::class . "::TrimDir", $path);
            $r = '';
            if (!empty($p)){
                array_unshift($path, $p);
            }
            else{
                $r = $sep;
            }
            return $r.igk_uri(implode($sep, array_filter($path)));
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
        // if (strpos($s, '../') > 0) {
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
     * detect path mode 
     * @param string $path 
     * @return ?string 
     */
    public static function DetectPathMode(string $path): ?string
    {
        $p = igk_io_collapse_path($path);
        if (preg_match(\IGKConstants::PATH_VAR_DETECT_MODEL_REGEX, $p, $tab)) {
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
                if (file_exists($f = self::CombineAndFlattenPath($n, $path))){
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
}
