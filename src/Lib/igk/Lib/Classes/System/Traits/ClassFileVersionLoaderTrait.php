<?php


// @author: C.A.D. BONDJE DOUE
// @filename: ClassFileVersionLoaderTrait.php
// @date: 20220901 07:15:22
// @desc: 
namespace IGK\System\Traits;

use IGKException;

/**
 * loading version trait
 */
trait ClassFileVersionLoaderTrait
{
    private $_load_classes = [];
    public function getClassesCacheFiles()
    {
        return IGK_LIB_DIR . "/.Caches/.classes.cache";
    }
    public function registerClass(string $file, string $classname, ?string $version = null)
    {
        $file = $file;
        if (empty($this->_load_classes)) {
            $this->_load_classes = ["cl" => [], "files" => [], "versions" => []];
        }
        $index = -1;
        if (!isset($this->_load_classes["cl"][$classname])) {
            $index = count($this->_load_classes["cl"]);
            $this->_load_classes["cl"][$classname] = $index;
            if (!($finfo = igk_getv($this->_load_classes["files"], $file))) {
                $finfo = (object)['p' => $file, 'v'=>$version];
                $this->_load_classes["files"][$file] = $finfo;
            }
            $this->_load_classes["files"][$index] = $finfo;
        } else {
            $index = $this->_load_classes["cl"][$classname];
        }

        if (!isset($this->_load_classes["versions"][$index])) {
            if (!empty($version)) {
                $this->_load_classes["versions"][$index] = $version;
            }
        } else {
            $tv = $this->_load_classes["versions"][$index];
            if (!is_array($tv)) {
                $this->_load_classes["versions"][$index] = [$tv => $this->_load_classes["files"][$index]];
            }
            if (empty($version)) {
                $version = "_"; // current version
            }
            $this->_load_classes["versions"][$index][$version] = $file;
        }
        $this->_load_classes["c"] = 1;
    }
    private function _initClassRegister()
    {
        if (is_file($fc = $this->getClassesCacheFiles())) {
            if (($src = @file_get_contents($fc))!== false){
                $this->_load_classes = unserialize($src); 
            }
        }
    }
    /**
     * 
     * @param string $classname 
     * @return ?string the registrated class 
     * @throws IGKException 
     */
    public function getRegisterClass(string $classname, ?string $version=null ): ?string    
    {       
        if (!empty($this->_load_classes) && !is_null($index = igk_getv($this->_load_classes["cl"], $classname))) {
            $finfo = $this->_load_classes["files"][$index];
            $version = igk_php_sversion($version); 
            // igk_debug_wln("finfo ", $finfo, "::::".$version); 
            if ($tv = igk_getv($this->_load_classes["versions"], $index)) {
                // check for version to match
                list($major, $minor) = explode('.', $version);
                foreach ([$major . "." . $minor, $major, "_"] as $t) {                    
                    if ($tp = igk_getv($tv, $t)) {
                        if (is_object($tp)){
                            return $tp->p;
                        }
                        return $tp;
                    }
                }
                return null;
            }
            return $finfo->p;
        }
        return null;
    }
}
