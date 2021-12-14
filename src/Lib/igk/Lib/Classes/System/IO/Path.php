<?php
namespace IGK\System\IO;

use IGK\Helper\StringUtility as str_helper;
use IGK\Helper\IO ; 

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

    /**
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
        $this->css_path = str_helper::uri(implode("/", [IGK_RES_FOLDER,IGK_STYLE_FOLDER,"balafon.css?v=".IGK_VERSION]));    
        $this->vendor_dir = str_helper::UriCombine(IGK_APP_DIR , "vendor");

        $this->sys_data_dir = str_helper::UriCombine(IGK_APP_DIR, IGK_DATA_FOLDER);
        $this->data_dir = str_helper::UriCombine(IGK_APP_DIR , IGK_DATA_FOLDER);
    }
    private function __construct(){
        $this->prepareData();
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
        $bdir = igk_environment()->get("basedir", constant("IGK_BASE_DIR"));
        if (!$bdir) {
            return null;
        }
        $l = igk_io_dir($bdir);
        if ($dir == null)
            return $l;
        if (file_exists($dir) && (($hdir = igk_io_dir($dir)) == igk_realpath($dir))) {
            $rpath = IO::GetRelativePath($hdir, $l);
            if ($rpath)
                return igk_io_dir($l . DIRECTORY_SEPARATOR . $rpath);
            return $dir;
        }
        $s = str_replace("\\", "\\\\", $l);
        $egext = "#^(" . $s . ")#";
        $dir = igk_io_dir($dir);
        if ($s && preg_match($egext, $dir))
            return $dir;
        return igk_io_dir($bdir . "/" . $dir);
    }
    public function baseuri($dir = null, $secured = null, &$path = null)
    {
        $secured = $secured === null ? igk_getv($_SERVER, 'HTTPS') == 'on' : $secured;
        $path = null;
        $out = IGK_STR_EMPTY;
        $v_dir = $this->basedir($dir);
        $root = $this->getRootDir(); // igk_io_rootdir();
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
    public function realpath($path)
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
        $d1 = explode("/", ltrim(str_helper::uri($spath), "/"));
        $d2 = explode("/", ltrim(str_helper::uri($link), "/"));
        $i = 0;
        $c1 =count($d1);
        while(($i < $c1) && ($d = array_shift($d2)) && ($d == $d1[$i])){
            //determine        
            $i++;
        }
        if ($i==0){
            return false; // die("path not match");
        }
        $dnew = str_repeat("../", count($d2)). implode("/", array_slice($d1,$i));
        return $dnew;
    }
}
 