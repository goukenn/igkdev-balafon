<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Path.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\IO;

use IGK\Helper\StringUtility as str_helper;
use IGK\Helper\IO ;
use IGKException;

///<summary>manage system path</summary>
///<note>for better directory manipulation. use t
class Path{

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


    private static $sm_instance; 

    public static function GetExtension($path){
        if (empty($path))
            return null;
        if (($pos = strrpos($path, '.'))!==false){
            return substr($path, $pos);
        }
        return ($t = explode(".", $path)) > 1 ? array_pop($t) : "";
    }
    /**
     * get system path instance
     * @return Path path instance
     */
    public static function getInstance(){
        if (self::$sm_instance===null){
            self::$sm_instance = new static(); 
        }
        return self::$sm_instance;
    }
    public function prepareData(){
 
        $this->app_dir = str_helper::Uri(IGK_APP_DIR);
        $this->base_dir = str_helper::Uri(IGK_BASE_DIR);
        $this->lib_dir = str_helper::Uri(IGK_LIB_DIR);
        $this->project_dir = str_helper::Uri(IGK_PROJECT_DIR);
        $this->package_dir = str_helper::Uri(IGK_PACKAGE_DIR);
        $this->module_dir = str_helper::Uri(IGK_MODULE_DIR); 
        $this->class_dir = str_helper::UriCombine(IGK_LIB_DIR, IGK_LIB_FOLDER, IGK_CLASSES_FOLDER);
        $b = ["v"=>IGK_VERSION];
        if (igk_environment()->isDev() && igk_getr("XDEBUG_TRIGGER")){
            $b["XDEBUG_TRIGGER"] = 1;
        }
        http_build_query($b);
        $this->css_path = str_helper::uri(implode("/", [IGK_RES_FOLDER,IGK_STYLE_FOLDER,"balafon.css?". http_build_query($b)]));    
        $this->vendor_dir = str_helper::UriCombine(IGK_APP_DIR , "vendor");
        $this->sys_data_dir = str_helper::UriCombine(IGK_APP_DIR, IGK_DATA_FOLDER);
        $this->data_dir = str_helper::UriCombine(IGK_APP_DIR , IGK_DATA_FOLDER);
    }
    private function __construct(){
        $this->prepareData();
    }
    public function getPackagesDir(){
        return $this->package_dir;
    }
    /**
     * 
     * @return string 
     */
    public function getStyleUri(){ 
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
    public function getBaseDir(){
        return $this->base_dir;
    }
    public function getDataDir(){
        return $this->data_dir;
    }
    /**
     * return sys data directory
     * @return mixed 
     */
    public function getSysDataDir(){
        return $this->sys_data_dir; 
    }
   

    /**
     * 
     * @param mixed|null $dir 
     * @return string base dir
     */
    public function basedir($dir = null)
    {
        // $bdir = igk_environment()->get("basedir", $this->base_dir);
        // if (!$bdir) {
        //     return null;
        // }
        // if ($dir == null)
        //     return $bdir;
        // $l = igk_dir($bdir);
        // if (file_exists($dir) && (($hdir = igk_dir($dir)) == igk_realpath($dir))) {
        //     $rpath = IO::GetRelativePath($hdir, $l);
        //     if ($rpath)
        //         return igk_dir($l . DIRECTORY_SEPARATOR . $rpath);
        //     return $dir;
        // }
        // $s = str_replace("\\", "\\\\", $l);
        // $egext = "#^(" . $s . ")#";
        // $dir = igk_dir($dir);
        // if ($s && preg_match($egext, $dir))
        //     return $dir;
        // return igk_dir($bdir . "/" . $dir);

        $bdir = igk_environment()->get("basedir", $this->base_dir );  
        if (!$bdir) {
            return null;
        } 
        if ($dir == null)
            return $bdir;
        $l = igk_dir($bdir);
        $_r = null;
        if (file_exists($dir) && (($hdir = igk_dir($dir)) == igk_realpath($dir))) {
            $rpath = IO::GetRelativePath($hdir, $l);
            $_r = ($rpath)? igk_dir($l . DIRECTORY_SEPARATOR . $rpath) : $dir;
        }else{
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
    public function baseuri($dir = null, $secured = null, &$path = null)
    {
        if ($baseURI = igk_environment()->get("baseURI")){
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
    public function basepath($dir, $sep = DIRECTORY_SEPARATOR)
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
            // $g = igk_io_realpath($dir);
            return $this->baserelativepath(realpath($dir));
        }
        return $this->baserelativepath($p, null, $sep);
    }
    public function baserelativepath($dir, $basedir=null, $sep = DIRECTORY_SEPARATOR){
        if(empty($dir)){
            return IGK_STR_EMPTY;
        }
        $dir=str_helper::uri($dir);
        $bdir=str_helper::uri($basedir == null ? $this->basedir(): $basedir);
        return $this->relativepath($dir, $bdir); 
    }
    public function relativepath($spath, $link){
        if (is_dir($link)){
            $link = rtrim($link, "/")."/";
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
    public static function GetRelativePath($source, $target){
        $source = rtrim($source,"/");
        $target = rtrim($target,"/");
        if ($source==$target){
            return "./";
        }
        $p = [];
        if (strpos($target, $source) === 0){
            // target is a child of the source
            $found = 0;
            while (($ctag = dirname($target)) && ($ctag!= $target)){
                array_unshift($p, basename($target));
                $target = $ctag;
                if(strpos($source, $ctag) === 0){
                    $found = 1;
                    break;
                }
            } 
            return "./".implode("/", $p);
        }
        $found = 0;
        $cpath = "";
        while (($ctag = dirname($target)) && ($ctag!= $target)){
            array_unshift($p, basename($target));
            $target = $ctag; 
            if (strpos($source, $target)===0){
                $found = 1;
                break;
            } 
        } 
        if($found){ 
            $cpath = str_repeat("../",  count(explode("/", ltrim(substr($source, strlen($target)), "/")))); 
            return $cpath.implode("/", $p);
        } 
        return null;
    }

    public static function LocalPath(string $path){
        return igk_io_expand_path(
            igk_io_collapse_path($path)
        ); 
    }
}
 