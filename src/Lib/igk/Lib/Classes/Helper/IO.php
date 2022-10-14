<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IO.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Helper;

use Exception;
use IGK\Helper\StringUtility as IGKString;
use IGK\Resources\R;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\FileWriter; 
use IGKException;
use ReflectionException;
use function igk_resources_gets as __; 


/**
 * IO utility helper
 * @package IGK\Helper
 */
class IO
{

    /**
     * resolv path constant
     * @param mixed $dir 
     * @param mixed $value 
     * @return string 
     */
    public static function ResolvPathConstant($dir, $value)
    {
        $p = realpath($value);
        if (empty($p)) {
            return str_replace("\\", "/", $dir . "/" . $value);
        }
        return $p;
    }

    public static function GetArticleInDir($dir, $name)
    {
        if ($dir == null) {
            $dir = IGK_LIB_DIR . "/" . IGK_ARTICLES_FOLDER;
        }
        $f = $dir . "/" . $name;
        if (file_exists($f))
            return $f;
        $s = IGK_ARTICLE_TEMPLATE_REGEX;
        if (preg_match($s, $name)) {
            return igk_dir($dir . "/" . $name);
        }
        $lang = R::GetCurrentLang();
        foreach (["." . $lang, ""] as $lg) {
            foreach (["phtml", 'html'] as $v) {
                $f = igk_dir($dir . "/{$name}{$lg}.{$v}");
                if (file_exists($f))
                    return $f;
            }
        }
        $ext = igk_get_article_ext();
        return igk_dir($dir . "/" . $name . $ext);
    }
    /**
     * collapse string path
     * @param mixed $str 
     * @return mixed 
     * @throws IGKException 
     */
    public static function CollapsePath(string $str)
    {
        $tp = array_flip(igk_environment()->getEnvironmentPath());
        krsort($tp);
        $path = igk_uri($str);
        foreach ($tp as $c => $t) {
            $gp = [$c];
            if ((($tc = realpath($c)) && ($tc != $c)) || is_link($c)) {
                if ($tc === false) {
                    $tc = $c;
                }
                $gp[] = $tc;
            }
            foreach ($gp as $tm) {
                if (strpos($path, $tm) === 0) {
                    $path = str_replace($tm, $t, $path);
                    break 2;
                }
            }
        }
        return $path;
    }
    /**
     * 
     * @param string $p source path
     * @param string $c parent path
     * @return bool 
     * @throws IGKException 
     */
    public static function IsSubDir($p, $c)
    {
        if (DIRECTORY_SEPARATOR != "/") {
            $p = str_replace("\\", "/", $p);
            $c = str_replace("\\", "/", $c);
        }
        if (empty($p)) {
            igk_die(__FUNCTION__ . "::p is empty ");
        }
        if (empty($c)) {
            igk_die(__FUNCTION__ . "::c is empty");
        }
        return (strpos($c, $p) === 0);
    }
    /**
     * create a symlinks
     */
    public static function SymLink($target, $cibling)
    {
        $r = false;
        $fc = !igk_is_function_disable("exec");

        if (!igk_server()->WINDIR) {
            // + | UNIX Allow us to create link relatively 
            if ($fc) {
                exec("ln -s '{$target}' '$cibling'");
            } else {
                `ln -s '$target' '$cibling'`;
            }
        } else {
            @symlink($target, $cibling);
        }
        $r = is_link($cibling);
        return $r;
    }
    ///<summary></summary>
    ///<param name="path"></param>
    ///<param name="separator" default="DIRECTORY_SEPARATOR"></param>
    /**
     * 
     * @param mixed $path
     * @param mixed $separator the default value is DIRECTORY_SEPARATOR
     */
    private static function __fixPath($path, $separator = DIRECTORY_SEPARATOR)
    {
        if ($separator == "/") {
            return preg_replace('/([\/]+)/i', '/', $path);
        }
        if ($separator == "\\") {
            return preg_replace('/([\\' . $separator . '\/]+)/i', '' . $separator . '', $path);
        }
        return $path;
    }
    ///<summary></summary>
    ///<param name="filename"></param>
    ///<param name="content"></param>
    ///<param name="chmod" default="IGK_DEFAULT_FILE_MASK"></param>
    /**
     * 
     * @param mixed $filename
     * @param mixed $content
     * @param mixed $chmod the default value is IGK_DEFAULT_FILE_MASK
     */
    public static function AppendToFileAsUTF8WBOM($filename, $content, $chmod = IGK_DEFAULT_FILE_MASK)
    {
        return self::WriteToFile($filename, $content, true, $chmod, "a+");
    }
    ///<summary></summary>
    ///<param name="inputDir"></param>
    ///<param name="outputDir"></param>
    ///<param name="recursive" default="false"></param>
    ///<param name="overwrite" default="false"></param>
    /**
     * 
     * @param mixed $inputDir
     * @param mixed $outputDir
     * @param mixed $recursive the default value is false
     * @param mixed $overwrite the default value is false
     */
    public static function CopyFiles($inputDir, $outputDir, $recursive = false, $overwrite = false)
    {
        $ddir = [["d"=>$inputDir,"path"=>$outputDir]];
        $sep = '/';
        $ln = strlen($inputDir);

        while ($q = array_pop($ddir)) {
            $inputDir = $q["d"];
            $outputDir = $q["path"];

            $hdir = opendir($inputDir);
            if ($hdir) { 
                while (($r = readdir($hdir))) {
                    if ($r == "." || ($r == ".."))
                        continue;
                    $f = $inputDir . $sep . $r;
                    $p = $outputDir . $sep . $r;
                    if (is_dir($f)) {
                        self::CreateDir($p);
                        if ($recursive) {
                            array_push($ddir, ["d"=>$f, "path"=>$p]);
                        }
                        continue;
                    }
                    if (!is_file($p) || $overwrite) {
                        if ($overwrite && is_file($p)){
                            unlink($p);
                        }
                        self::CreateDir(dirname($p));                  
                        copy($f, $p);                           
                    } 
                }
                closedir($hdir);
            }
        }
    }
    ///<summary></summary>
    ///<param name="dirname"></param>
    ///<param name="mode" default="IGK_DEFAULT_FOLDER_MASK"></param>
    /**
     * 
     * @param mixed $dirname
     * @param mixed $mode the default value is IGK_DEFAULT_FOLDER_MASK
     */
    public static function CreateDir($dirname, $mode = IGK_DEFAULT_FOLDER_MASK)
    {
        return FileWriter::CreateDir($dirname, $mode);
    }
    ///<summary> Create a directory recursivily</summary>
    ///<dir>directory to create</dir>
    ///<root>mus add a as directory separator </root>
    ///<return>-1 if dir is empty, </return>
    /**
     *  Create a directory recursivily
     */
    public static function CreateRDir($dir, $root = false)
    {
        if (empty($dir)) {
            return -1;
        }
        if (is_dir($dir))
            return 1;
        $d = explode(DIRECTORY_SEPARATOR, igk_dir($dir));
        $s = IGK_STR_EMPTY;
        for ($i = 0; $i < count($d); $i++) {
            if ($root || ($i > 0)) {
                $s .= DIRECTORY_SEPARATOR;
            }
            $s .= $d[$i];
            if (empty($s) || is_dir($s))
                continue;
            if (!@mkdir($s))
                return false;
        }
        return true;
    }
    ///<summary>DIRECTORY FUNCTION.  </summary>
    /**
     * DIRECTORY FUNCTION.
     */
    public static function GetBaseDir($dir = null)
    {
        return igk_io_basedir($dir);
    }
    ///<summary> get relative path according to the IGK_APP_DIR</summary>
    ///<param name="dir">must be a full path to existing file or  existing directory </param>
    /**
     *  get relative path according to the IGK_APP_DIR
     * @param mixed $dir must be a full path to existing file or existing directory
     */
    public static function GetBaseDirRelativePath($dir, $separator = DIRECTORY_SEPARATOR)
    {
        $doc_root = self::GetBaseDir();
        return self::GetSysRelativePath($dir, $doc_root, $separator);
    }
    ///<summary>GET BASE FOLDER FULLPATH</summary>
    /**
     * GET BASE FOLDER FULLPATH
     */
    public static function GetBaseFolderFullpath($dir)
    {
        $d = igk_app()->CurrentPageFolder;
        if (!empty($d) && ($d != IGK_HOME_PAGEFOLDER))
            return igk_dir(igk_io_currentrelativepath(IGK_APP_DIR . "/" . $d . "/" . $dir));
        return igk_dir(igk_io_currentrelativepath(IGK_APP_DIR . "/" . $dir));
    }
    ///<summary>get the current base uri according to local specification</summary>
    ///<param name="dir">null or existing fullpath directory or file element. </param>
    /**
     * get the current base uri according to local specification
     * @param mixed $dir null or existing fullpath directory or file element.
     */
    public static function GetBaseUri($dir = null, $secured = false, &$path = null)
    {
        return igk_io_baseuri($dir, $secured, $path);
    }
    ///<summary></summary>
    ///<param name="source"></param>
    ///<param name="destination"></param>
    ///<param name="separator" default="DIRECTORY_SEPARATOR"></param>
    /**
     * 
     * @param mixed $source
     * @param mixed $destination
     * @param mixed $separator the default value is DIRECTORY_SEPARATOR
     */
    public static function GetChildRelativePath($source, $destination, $separator = DIRECTORY_SEPARATOR)
    {
        $doc_root = igk_uri($source);
        $dir = igk_uri($destination);
        if (strpos($dir, $doc_root) !== 0)
            return;
        $i = IGKString::IndexOf($dir, $doc_root);
        if ($i != -1) {
            $dir = substr($dir, $i + strlen($doc_root));
        }
        $basedir = self::GetRootBaseDir();
        if ($basedir != "/")
            $dir = str_replace($basedir, IGK_STR_EMPTY, $dir);
        while ((strlen($dir) > 0) && ($dir[0] == "/")) {
            $dir = substr($dir, 1);
        }
        return empty($dir) ? null : self::__fixPath($dir, $separator);
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function GetCurrentDir()
    {
        return getcwd();
    }
    ///<summary>get relative path according to IGK_APP_DIR base dir</summary>
    ///@dir: absolute path or basedir relative path
    /**
     * get relative path according to IGK_APP_DIR base dir
     */
    public static function GetCurrentDirRelativePath($dir, $mustexists = 1, $separator = DIRECTORY_SEPARATOR)
    {
        $doc = igk_io_rootdir();
        $cdir = self::GetCurrentDir();
        $bdir = self::GetBaseDir();
        $dir = igk_dir($dir);
        $i = -1;
        $v_iscurrent = ($bdir == $cdir);
        if ($v_iscurrent) {
            if ($mustexists) {
                if (file_exists($dir))
                    $dir = igk_realpath($dir);
                $d = self::GetBaseDirRelativePath($dir);
            } else {
                $dir = $cdir . $separator . $dir;
                $d = self::GetBaseDirRelativePath($dir);
            }
            return $d;
        }
        if (empty($dir)) {
            return self::GetRelativePathToDir($dir, $cdir, $bdir);
        }
        $r = igk_realpath($dir);
        if ($r != null)
            $r = self::GetSysRelativePath($r, $cdir);
        else {
            $r = self::GetSysRelativePath(igk_io_basedir($dir), $cdir);
        }
        return $r;
    }
    ///<summary>return relative uri from server requested URI</summary>
    ///<param name="dir"> full path to resources</param>
    /**
     * return relative uri from server requested URI
     * @param mixed $dir full path to resources
     */
    public static function GetCurrentRelativeUri($dir = IGK_STR_EMPTY, ?string $path = null)
    {
        $rootdir = igk_io_rootdir();
        $bdir = igk_io_basedir();
        if ($path === null) {
            $path = igk_io_request_uri();
        }
        if (!empty($dir)) {
            if (strpos($dir, $bdir) === 0) {
                //sub path or relative dir            
                if (realpath($dir)) {
                    // path exists
                    // -----------
                    die("not emplement");
                }
            }
        }
        $bdir = implode("/", array_filter([$bdir, ltrim($path, "/")]));
        if (strpos($bdir, $rootdir) === 0) {
            //path is subdir
            if ($rootdir == $bdir) {
                if (empty($dir)) {
                    $r = "./";
                } else {
                    $r = self::GetRootRelativePath($dir);
                }
                return $r;
            }
            // get 
            $p = "";
            $cbdir = $bdir;
            while ($cbdir != $rootdir) {
                $p .= "../";
                $cbdir = dirname($cbdir);
            }
            return $p . ltrim($dir, "/");
        }
        return null;

        // die("not implement ". __FILE__);
        // igk_wln(
        //     "root_c  = ".self::GetRootRelativePath($dir),
        //     "rootdir = ".igk_io_rootdir(),
        //     "basedir = ".igk_io_basedir(),
        //     "request = ".self::GetRequestBaseUri(),
        //     "base--  = ".self::GetRootUri(self::GetRequestBaseUri()),
        //     "rrq     = ".igk_io_request_uri()
        // );

        // $__dir=$dir;
        // $v_basedir=igk_io_basedir();
        // $r_uri=null;
        // $v_isdir=true;
        // $sep="/";
        // $r_uri= self::GetRequestBaseUri(); // igk_getv(explode("?", igk_io_request_uri()), 0);
        // $v_isdir=IGKString::EndWith($r_uri, '/');
        // $cdir=self::GetRootUri(rtrim($r_uri, '/'));
        // $is_root=igk_io_basedir_is_root();
        // $bdir=$is_root ? igk_io_baseuri(): self::GetRootUri();
        // $dir=ltrim(igk_uri($dir), $sep);

        // igk_wln_e(get_defined_vars());

        // if(!$is_root){
        //     $sbdir=igk_uri(igk_io_basedir());
        //     $srdir=igk_uri(igk_io_rootdir());
        //     // igk_wln_e("null", $is_root, IGK_APP_DIR,  "sbdir:".$sbdir, "root:".$srdir, "rootdir is empty ".igk_io_rootdir());
        //     if(strstr($sbdir, $srdir)){
        //         $child=igk_str_rm_last(substr($sbdir, strlen($srdir) + 1), $sep);
        //         $dir=$child.$sep.$dir;
        //     }
        //     else{               
        //         return null;
        //     }
        // }
        // $i=-1;

        // if($bdir == $cdir){
        //     if(empty($dir))
        //         return "./";
        //     return self::GetRootRelativePath($dir);
        // }
        // $i=IGKString::IndexOf($cdir, $bdir);
        // $c=0;
        // $h="";
        // if(($lv=igk_io_dir_level()) > 0){
        //     $h=str_repeat("../", $lv);
        // }
        // if(($sdir=IGK_BASE_DIR) != $v_basedir){
        //     while($sdir != "." && !strstr($v_basedir, $sdir)){
        //         $h .= "../";
        //         $sdir=dirname($sdir);
        //         $c++;
        //     }
        //     return igk_uri($h.substr($v_basedir, strlen($sdir) + 1).$sep.$dir);
        // }
        // else{
        //     $sdir=$cdir;
        //     $counter=0;
        //     while(!empty($sdir) && ($sdir != $bdir)){
        //         $h .= "../";
        //         $sdir=dirname($sdir);
        //         if($sdir == ".")
        //             break;
        //     }
        //     if(empty($dir))
        //         return $h;
        //     return igk_uri($h.$dir);
        // }
    }
    ///<summary>tranforme le repertoire passer en paramètre en une chemin compatible celon le systeme d'exploitation serveur</summary>
    /**
     * tranforme le repertoire passer en paramètre en une chemin compatible celon le systeme d'exploitation serveur
     */
    public static function GetDir($dir, $separator = DIRECTORY_SEPARATOR)
    {
        if ($dir === null) {
            return $dir;
        }
        $d = $separator;
        $out = IGK_STR_EMPTY;
        if (ord($d) == 92) {
            $out = preg_replace("/\//", '\\', $dir);
            $out = str_replace("\\", "\\", $out);
        } else {
            $d = "/[\\\\]/";
            $out = preg_replace($d, '/', $dir);
            $out = str_replace("//", "/", $out);
        }
        return $out;
    }
    ///<summary></summary>
    ///<param name="folder"></param>
    /**
     * 
     * @param mixed $folder
     */
    public static function GetDirFileList($folder)
    {
        if (!is_dir($folder))
            return false;
        $dirs = array();
        $hdir = opendir($folder);
        if ($hdir) {
            while (($cdir = readdir($hdir))) {
                if (($cdir == ".") || ($cdir == ".."))
                    continue;
                $f = self::GetDir($folder . "/" . $cdir);
                if (is_file($f)) {
                    $dirs[] = $f;
                }
            }
            closedir($hdir);
        }
        return $dirs;
    }
    ///<summary></summary>
    ///<param name="folder"></param>
    /**
     * 
     * @param mixed $folder
     */
    public static function GetDirList($folder)
    {
        if (!is_dir($folder))
            return false;
        $dirs = array();
        $hdir = opendir($folder);
        if ($hdir) {
            while (($cdir = readdir($hdir))) {
                if (($cdir == ".") || ($cdir == ".."))
                    continue;
                $f = self::GetDir($folder . "/" . $cdir);
                if (is_dir($f)) {
                    $dirs[] = $f;
                }
            }
            closedir($hdir);
        }
        return $dirs;
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    ///<param name="match"></param>
    ///<param name="recursive" default="false"></param>
    /**
     * 
     * @param mixed $dir
     * @param mixed $match
     * @param mixed $recursive the default value is false
     */
    public static function GetDirs($dir, $match, $recursive = false)
    {
        if (is_dir($dir) === false)
            return null;
        $v_out = array();
        $hdir = @opendir($dir);
        if ($hdir) {
            while (($r = readdir($hdir))) {
                if ($r == "." || ($r == ".."))
                    continue;
                $f = $dir . DIRECTORY_SEPARATOR . $r;
                if (is_dir($f) && (($match == null) || (($match != null) && (preg_match($match, $f))))) {
                    $v_out[] = $f;
                }
                if ($recursive) {
                    foreach (igk_io_dirs($f, $match, $recursive) as $k) {
                        $v_out[] = $k;
                    }
                }
            }
            closedir($hdir);
        }
        return $v_out;
    }
    ///<summary></summary>
    ///<param name="filename"></param>
    /**
     * 
     * @param mixed $filename
     */
    public static function GetFileExt($filename)
    {
        $pathinfo = pathinfo($filename);
        try {
            if (isset($pathinfo["extension"]))
                return $pathinfo["extension"];
        } catch (Exception $exception) {
            die($filename);
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="filename"></param>
    /**
     * 
     * @param mixed $filename
     */
    public static function GetFileName($filename)
    {
        $pathinfo = pathinfo($filename);
        $b = $pathinfo["basename"];
        return $b;
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    ///<param name="match"></param>
    ///<param name="recursive" default="false"></param>
    ///<param name="excludedir" default="null" ref="true"></param>
    /**
     * 
     * @param mixed $dir
     * @param mixed $match
     * @param mixed $recursive the default value is false
     * @param ?array|mixed * $excludedir the default value is null. "@--ignore_hidden--" is a flag used to ignore hidden folder in search
     * @param callable $callback callback called* $excludedir the default value is null
     */
    public static function GetFiles($dir, $match, $recursive = false, ?array &$excludedir = null, ?callable $callback = null)
    { 
        if (is_dir($dir) === false)
            return null;
        $v_out = array();
        $dir = rtrim(igk_uri($dir), '/');
        $q = 0;
        $dirs = array();
        array_push($dirs, $dir);
        $iscallable = is_callable($match);
        $ignore_hidden = false;
        $sep = '/';
        $fc = function () {
            return false;
        };
        if (is_string($excludedir)) {
            $fc = function ($d, $m, $ignoredname) {
                return preg_match("#" . $ignoredname . "#", $m);
            };
        } else if (is_array($excludedir)) {
            $ignore_hidden = igk_getv($excludedir, "@--ignore_hidden--");
            $fc = function ($d, $m, $ignoredname) {
                return isset($ignoredname[$m]) || isset($ignoredname[$d]);
            };
        }
        if (!$iscallable && is_string($match)) {
            $_include_match = function ($f) use ($match) {
                return preg_match($match, $f);
            };
        } else if ($iscallable) {
            $_include_match = function ($f) use ($match, &$excludedir) { 
                return $match($f, $excludedir);
            };
        }

        while ($q = array_pop($dirs)) {
            if ($hdir = @opendir($q)) {
                while ($hdir && ($r = readdir($hdir))) {
                    if ($r == "." || ($r == ".."))
                        continue;
                    $mdata = 0;
                    $f = $q . $sep . $r;
                    // igk_debug_wln("first file ".$f);
                    if (!is_dir($f)) {
                        if ($_include_match && $_include_match($f)) {
                            //igk_debug_wln_e("call null ", $mdata===false, $is_match_nil, $match);
                            if ($mdata == -1) {
                                continue;
                            } 
                            $v_out[] = $f;
                            $callback && $callback($f);
                        }
                    } else {                        
                        if (!($ignore_hidden && (strpos($r, ".")===0)) && !$fc($f, $r, $excludedir) && $recursive) {
                            array_push($dirs, $f);
                        }
                    }
                }
                closedir($hdir);
            }
        }
        return $v_out;
    }
    ///<summary></summary>
    ///<param name="size"></param>
    /**
     * 
     * @param mixed $size
     */
    public static function GetFileSize($size)
    {
        if ($size == 0)
            return "0 Bytes";
        $sizes = array(
            'Bytes',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'
        );
        return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    ///<param name="recursive" default="true"></param>
    /**
     * 
     * @param mixed $dir
     * @param mixed $recursive the default value is true
     */
    public static function GetPictureFile($dir, $recursive = true)
    {
        if (is_dir($dir) === false)
            return null;
        $tab = array();
        $tdir = array();
        $hdir = opendir($dir);
        if ($hdir) {
            while (($r = readdir($hdir))) {
                if ($r == "." || ($r == ".."))
                    continue;
                $f = $dir . DIRECTORY_SEPARATOR . $r;
                if (is_file($f)) {
                    $ext = strtolower(self::GetFileExt($f));
                    switch ($ext) {
                        case "png":
                        case "jpeg":
                        case "jpg":
                        case "ico":
                            $tab[] = $f;
                            break;
                    }
                } else if (is_dir($f)) {
                    $tdir[] = $f;
                }
            }
            closedir($hdir);
        }
        if ($recursive) {
            foreach ($tdir as $k) {
                $m = self::GetPictureFile($k);
                if ($m != null) {
                    $tab = array_merge($tab, $m);
                }
            }
        }
        return $tab;
    }
    ///<summary></summary>
    ///<param name="secure" default="false"></param>
    /**
     * 
     * @param mixed $secure the default value is false
     */
    public static function GetPort($secure = false)
    {
        $p = igk_getv($_SERVER, 'SERVER_PORT');
        if (($secure) && ($p != 443) || (!$secure && ($p != 80)))
            return $p;
        return null;
    }
    ///<summary></summary>
    ///<param name="sourcepath"></param>
    ///<param name="targetdir"></param>
    ///<param name="separator" default="DIRECTORY_SEPARATOR"></param>
    /**
     * 
     * @param mixed $sourcepath
     * @param mixed $targetdir
     * @param mixed $separator the default value is DIRECTORY_SEPARATOR
     */
    public static function GetRelativePath($sourcepath, $targetdir, $separator = DIRECTORY_SEPARATOR)
    {
        $i = IGKString::IndexOf($targetdir, $sourcepath);
        if ($i != -1) {
            $s = self::__fixpath(substr($targetdir, strlen($sourcepath)));
            while (!empty($s) && IGKString::StartWith($s, DIRECTORY_SEPARATOR)) {
                $s = substr($s, 1);
            }
            return $s;
        }
        $dir = $sourcepath;
        $cdir = $sourcepath;
        $bdir = $targetdir;
        $i = -1;
        $c = 0;
        $tsdir = explode(DIRECTORY_SEPARATOR, $cdir);
        $tbdir = explode(DIRECTORY_SEPARATOR, $bdir);
        $rstep = false;
        while (($c < count($tbdir)) && ($c < count($tsdir))) {
            if ($tbdir[$c] != $tsdir[$c]) {
                $rstep = true;
                break;
            }
            $c++;
        }
        $s = IGK_STR_EMPTY;
        if ($rstep) {
            for ($h = $c; $h < count($tbdir); $h++) {
                $s .= ".." . DIRECTORY_SEPARATOR;
            }
        }
        for ($h = $c; $h < count($tsdir); $h++) {
            if ($h > $c)
                $s .= DIRECTORY_SEPARATOR;
            $s .= $tsdir[$h];
        }
        return $s;
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    ///<param name="cdir"></param>
    ///<param name="bdir"></param>
    /**
     * 
     * @param mixed $dir
     * @param mixed $cdir
     * @param mixed $bdir
     */
    private static function GetRelativePathToDir($dir, $cdir, $bdir)
    {
        $i = IGKString::IndexOf($cdir, $bdir);
        if ($i != -1) {
            $cdir = substr($cdir, $i + strlen($bdir));
        }
        $i = IGKString::IndexOf($dir, $bdir);
        if ($i != -1) {
            $dir = substr($dir, $i + strlen($bdir));
        }
        $dir = self::RemoveFirstDirectorySeparator($dir);
        $cdir = self::RemoveFirstDirectorySeparator($cdir);
        $t = count(explode(DIRECTORY_SEPARATOR, $cdir));
        for ($i = 0; $i < $t; $i++) {
            $dir = ".." . DIRECTORY_SEPARATOR . $dir;
        }
        return empty($dir) ? null : self::__fixPath($dir);
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function GetRequestBaseUri()
    {
        return self::GetRootUri(igk_getv(explode("?", igk_io_request_uri() ?? ""), 0));
    }
    ///end relative
    ///<summary>Get the Root directory according to DocumentRoot apache configuration </summary>
    ///@get the root dir according to document root. uses for css script file
    ///<param name="dir">relative dirctory that will be append to result</param>
    /**
     * Get the Root directory according to DocumentRoot apache configuration
     * @param mixed $dir relative dirctory that will be append to result
     */
    public static function GetRootBaseDir($dir = "")
    {
        $s = self::GetBaseDir();
        $s = str_replace("\\", "/", $s);
        $doc = StringUtility::Uri(igk_io_rootdir());
        $dir = StringUtility::Uri($dir);

        if (strlen($s) > 0) {
            if ($s[0] == "/") {
                $s = strstr($s, $doc);
                $s = trim(substr($s, strlen($doc)));
                if ((strlen($s) > 0) && ($s[0] != "/"))
                    $s = "/" . $s;
            } else {
                $s = substr($s, strlen($doc));
                if ((strlen($s) > 0) && $s["0"] != "/")
                    $s .= "/";
            }
        }
        if ($dir) {
            if ($s == "/")
                $s = IGK_STR_EMPTY;
            if (0 === strpos("/", $dir))
                $s .= $dir;
            else
                $s .= "/" . $dir;
        }
        return $s;
    }
    ///<summary> get relative path according to the DOCUMENT_ROOT</summary>
    ///<remark>full path from root dir</remark>
    /**
     *  get relative path according to the DOCUMENT_ROOT
     */
    public static function GetRootRelativePath(?string $dir = null, $separator = DIRECTORY_SEPARATOR)
    {
        $doc_root = igk_io_rootdir();
        $bdir = self::GetRootBaseDir();
        $i = IGKString::IndexOf($dir, $doc_root);
        $c = IGK_STR_EMPTY;
        if ($i != -1) {
            $dir = substr($dir, $i + strlen($doc_root));
            $bdir = igk_dir($doc_root . $separator . $bdir);
            $c = igk_io_get_relativepath($bdir, $doc_root);
        }
        $dir = str_replace($bdir, IGK_STR_EMPTY, $dir);
        while ((strlen($dir) > 0) && ($dir[0] == $separator)) {
            $dir = substr($dir, 1);
        }
        if ($c)
            $dir = $c . $separator . $dir;
        return igk_uri(empty($dir) ? null : self::__fixPath($dir));
    }
    ///<summary></summary>
    ///<param name="uri" default="IGK_STR_EMPTY"></param>
    ///<param name="secured" default="null"></param>
    /**
     * 
     * @param mixed $uri the default value is IGK_STR_EMPTY
     * @param mixed $secured the default value is null
     */
    public static function GetRootUri($uri = IGK_STR_EMPTY, $secured = null)
    {
        if (!$secured && igk_sys_srv_is_secure())
            $secured = true;
        if ($secured) {
            $out = 'https://';
        } else {
            $out = 'http://';
        }
        $port = "";
        if ($c = self::GetPort($secured)) {
            $port = ':' . $c;
        }
        $n = igk_server_name();
        if (!empty($n))
            $out .= igk_str_rm_last($n, '/') . $port;
        if (!empty($uri))
            $out .= '/' . ltrim($uri, '/');
        $out = str_replace('\\', '/', $out);
        return $out;
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    ///<param name="doc_root"></param>
    ///<param name="separator" default="DIRECTORY_SEPARATOR"></param>
    /**
     * 
     * @param mixed $dir
     * @param mixed $doc_root
     * @param mixed $separator the default value is DIRECTORY_SEPARATOR
     */
    public static function GetSysRelativePath($dir, $doc_root, $separator = DIRECTORY_SEPARATOR)
    {
        if (empty($dir) || empty($doc_root))
            return null;
        $i = IGKString::IndexOf($dir, $doc_root);
        if ($i != -1) {
            $dir = ltrim(substr($dir, $i + strlen($doc_root)), $separator);
            return $dir;
        }
        $p = "../";
        $found = false;
        while (!empty($doc_root)) {
            $doc = dirname($doc_root);
            if ($doc == $doc_root) {
                break;
            }
            $doc_root = $doc;
            $i = IGKString::IndexOf($dir, $doc_root);
            if ($i == -1) {
                $p .= "../";
            } else {
                $found = true;
                break;
            }
        }
        if ($found) {
            $dir = ltrim(substr($dir, $i + strlen($doc_root)), $separator);
            return igk_dir($p . $dir);
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="uri"></param>
    /**
     * 
     * @param mixed $uri
     */
    public static function IsAbsolutePath($uri)
    {
        $uri = igk_dir($uri);
        return file_exists($uri) && ($uri == igk_realpath($uri));
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    /**
     * 
     * @param mixed $dir
     */
    public static function IsDirEmpty($dir)
    {
        if (!is_dir($dir))
            return true;
        $hdir = @opendir($dir);
        if ($hdir) {
            $empty = true;
            while ($s = readdir($hdir)) {
                if (($s == ".") || ($s == ".."))
                    continue;
                $empty = false;
                break;
            }
            closedir($hdir);
            return $empty;
        } else {
            igk_debug_wln("warning:impossible d'ouvir le repertoire : " . $dir);
        }
        return true;
    }
    ///<summary>check is this file is present on server .symbolink link resolved</summary>
    /**
     * check is this file is present on server .symbolink link resolved
     */
    public static function IsRealAbsolutePath($uri)
    {
        $uri = igk_dir($uri);
        return !empty($c = igk_realpath($uri));
    }
    ///<summary>read entiere file in one shot. speed for small file</summary>
    /**
     * read entiere file in one shot. speed for small file
     */
    public static function ReadAllText($filename)
    {
        if (!is_file($filename))
            return null;
        $fsize = @filesize($filename);
        if ($fsize <= 0)
            return null;
        $str = '';
        if ($fw = fopen($filename, "r")) {
            while ($fsize > 0) {
                if (empty($b = fread($fw, $fsize))) {
                    die(__("Failed to read data"));
                }
                $str .= $b;
                $fsize -= strlen($b);
            }
            fclose($fw);
        } else {
            igk_ilog(__("Failed to open : {0}", $filename));
        }
        return $str;
    }
    ///<summary></summary>
    ///<param name="f"></param>
    ///<param name="offset"></param>
    ///<param name="ln"></param>
    /**
     * 
     * @param mixed $f
     * @param mixed $offset
     * @param mixed $ln
     */
    public static function ReadFile($f, $offset, $ln)
    {
        if (!file_exists($f))
            return null;
        $fsize = filesize($f);
        $ln = min($ln, $fsize - $offset);
        if ($ln > 0) {
            $hf = fopen($f, "r");
            fseek($hf, $offset);
            $o = fread($hf, $ln);
            fclose($hf);
            return $o;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    /**
     * 
     * @param mixed $dir
     */
    public static function RemoveFirstDirectorySeparator($dir)
    {
        while ((!empty($dir) && ($dir[0] == DIRECTORY_SEPARATOR))) {
            $dir = substr($dir, 1);
        }
        return $dir;
    }
    ///<summary>REMOVE FOLDER</summary>
    /**
     * REMOVE FOLDER
     */
    public static function RmDir($dir, $recursive = true, $callback = null)
    {
        if (!is_dir($dir))
            return false;
        $pdir = array($dir);
        $kdir = array($dir);
        $d = 1;
        while ($dir = array_pop($pdir)) {
            $hdir = opendir($dir);
            if (!$hdir)
                return false;
            while (($f = readdir($hdir))) {
                if (($f == ".") || ($f == ".."))
                    continue;
                $v = igk_dir($dir . "/" . $f);

                if ($callback && !$callback($v)) {
                    continue;
                }
                if (is_link($v)) {
                    @unlink($v);
                    continue;
                }
                if (is_dir($v)) {
                    if ($recursive) {
                        array_push($pdir, $v);
                        array_push($kdir, $v);
                    } else {
                        $d = 0;
                        break;
                    }
                } else if (is_file($v) || is_link($v)) {
                    if ($recursive)
                        unlink($v);
                    else {
                        $d = 0;
                        break;
                    }
                }
            }
            closedir($hdir);
        }
        while ($d && ($dir = array_pop($kdir))) {
            if (is_link($dir)) {
                unlink($dir);
            } else {
                @rmdir($dir);
            }
        }
        return igk_count($kdir) == 0;
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    ///<param name="pattern" default="null"></param>
    /**
     * 
     * @param mixed $dir
     * @param mixed $pattern the default value is null
     */
    public static function RmFiles($dir, $pattern = null)
    {
        if (!is_dir($dir))
            return false;
        $hdir = opendir($dir);
        if (!$hdir)
            return false;
        while (($f = readdir($hdir))) {
            if (($f == ".") || ($f == ".."))
                continue;
            $v = igk_dir($dir . "/" . $f);
            if (is_file($v)) {
                if (($pattern == null) || preg_match($pattern, $v)) {
                    unlink($v);
                }
            }
        }
        closedir($hdir);
        return true;
    }
    ///<summary>write text to a file</summary>
    ///<remarks>return true if success. or throw exception</remarks>
    /**
     * write text to a file
     */
    public static function WriteToFile($filename, $content, $overwrite = true, $chmod = IGK_DEFAULT_FILE_MASK, $type = "w+")
    {
        return igk_io_save_file_as_utf8_wbom($filename, $content, $overwrite, $chmod, $type);
    }
    ///<summary></summary>
    ///<param name="filename"></param>
    ///<param name="content"></param>
    ///<param name="overwrite" default="true"></param>
    ///<param name="chmod" default="IGK_DEFAULT_FILE_MASK"></param>
    /**
     * 
     * @param mixed $filename
     * @param mixed $content
     * @param mixed $overwrite the default value is true
     * @param mixed $chmod the default value is IGK_DEFAULT_FILE_MASK
     */
    public static function WriteToFileAsUtf8WBOM($filename, $content, $overwrite = true, $chmod = IGK_DEFAULT_FILE_MASK)
    {
        return self::WriteToFile($filename, $content, $overwrite, $chmod);
    }

    /**
     * read file lines 
     * @param string $filename 
     * @param int $start 
     * @param int $end 
     * @return string 
     */
    public static function ReadLines(string $filename, int $start, int $end)
    {
        $g = explode("\n", file_get_contents($filename));
        $g = array_slice($g, $start, $end - $start);
        return implode("\n", $g);
    }

    /**
     * get unix path - to search for real file
     * @param string $path 
     * @param bool $mustExist check if the path must exist
     * @return null|string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetUnixPath(string $path, bool $mustExist = false, $start = "/"): ?string
    {
        if (!igk_environment()->isUnix() || ($path[0] != "/")) {
            return null;
        }
        $_viewdir = $start;
        $od = rtrim($start, "/");
        if ($dir = @opendir($_viewdir)) {
            $cp = array_filter(explode("/", $path));
            while ($dir && ($tq = array_shift($cp))) {
                $q = strtolower($tq);
                $found = false;
                while (false !== ($cdir = readdir($dir))) {
                    if (strtolower($cdir) == $q) {
                        $found = true;
                        $od .= "/" . $cdir;
                        break;
                    }
                }
                if ($found) {
                    closedir($dir);
                    $dir = null;
                    if (is_dir($od)) {
                        ($dir = opendir($od)) || igk_die("failed to open : " . $od);
                    }
                } else {
                    if (!$mustExist) {
                        $od .= rtrim("/" . $tq . "/" . implode("/", $cp), "/");
                    }
                    $cp = null;
                    break;
                }
            }
            closedir($dir);
        }
        return $od;
    }
}
