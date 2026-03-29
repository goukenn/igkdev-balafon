<?php
// @author: C.A.D. BONDJE DOUE
// @file: FileHandler.php
// @date: 20240115 10:34:09
namespace IGK\System\IO;
use IGKException;
/**
* auto generate doc.
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
abstract class FileHandler{
    /**
    * Callback handler for handler.
    * @var mixed
    */
    private static $sm_handler;
    /**
    * Constant: file context global.
    * @var mixed
    */
    const FILE_CONTEXT_GLOBAL = 'global';
    /**
    * Constant: file context view.
    * @var mixed
    */
    const FILE_CONTEXT_VIEW = 'view_context';
    /**
    * Constant: file context css.
    * @var mixed
    */
    const FILE_CONTEXT_CSS = 'style_context';
    /**
    * Constant: context key.
    * @var mixed
    */
    const CONTEXT_KEY = '::context';
    /**
     * register file handler
     * @param string $extension extension and group. .ext[,.ext2][|context]
     * @param FileHandler $handler 
     * @return void 
     */
    public static function Register(string $extension, FileHandler $handler){
        if (is_null(self::$sm_handler)){
            self::$sm_handler = [];
        }
        $tab_handler = [];
        $v_context = self::FILE_CONTEXT_GLOBAL; // 'global';
        $tab = explode('|', $extension,2);
        if (isset($tab[1])){
            $v_context = $tab[1];
        }
        array_map(function($extension)use($handler, & $tab_handler){
            if (isset(self::$sm_handler[$extension])){
                if (!is_array(self::$sm_handler[$extension])){
                    self::$sm_handler[$extension] = [self::$sm_handler[$extension]];
                }
                self::$sm_handler[$extension][] = $handler;
            }else{
                self::$sm_handler[$extension] = $handler;
            }
            $tab_handler[$extension] = $extension;
        },
        explode(',', $tab[0]));
        $key = self::CONTEXT_KEY;
        if (!isset(self::$sm_handler[$key][$v_context] )){
            self::$sm_handler[$key][$v_context] = [];
        }
        self::$sm_handler[$key][$v_context] = array_merge(self::$sm_handler[$key][$v_context], $tab_handler);
    }
    /**
    * auto generate doc.
    * @return ?array
    */
    public static function ConfigFileHandler():?array{
        return igk_app()->getConfigs()->default_ext_file_handlers;
    }
    /**
     * retrieve file handler from extension
     * @param string $extension 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetFileHandlerFromExtension(string $extension){
        if (self::$sm_handler){
            $h = igk_getv(self::$sm_handler, $extension);
            if (is_array($h)){
                // + | multiple handler fond for the same extension 
                // + | configure defalt_ext_file_handlers for the globala application project 
                if ($handlers = igk_app()->getConfigs()->default_ext_file_handlers){
                    $c = igk_getv($handlers, $extension);
                    while($c && (count($h)>0)){
                        $q = array_shift($h);
                        if (get_class($q) == $c){
                            return $q;
                        }
                    }
                }
            }
            return $h;
        }
        return null;
    }
    /**
     * Get Context file handler
     * @param string $handler_context 
     * @return null|array 
     * @throws IGKException 
     */
    public static function GetContextFileHandlers(string $handler_context):?array{
        if (is_null(self::$sm_handler)){
            return null;
        }
        $key = self::CONTEXT_KEY;
        $b = igk_getv(self::$sm_handler[$key], $handler_context);
        if ($b){
            $v_o = [];
            foreach($b as $ext){
                $r = self::$sm_handler[$ext];
                $v_o[$ext] = $r;
            }
            return $v_o;
        }
        return null;
    }
    /**
    * Returns View Context File Handlers.
    */
    public static function GetViewContextFileHandlers(){
        return self::GetContextFileHandlers(self::FILE_CONTEXT_VIEW);
    }
    /**
     * resolve file in directory
     * @param string $dir 
     * @param string $base_name 
     * @param string $context 
     * @return string|false 
     * @throws IGKException 
     */
    public static function ResolveFile(string $dir, string $base_name, string $context){
        if ($g = self::GetContextFileHandlers($context)){
            $exts = array_keys($g);
            while(count($exts)>0){
                $q = array_shift($exts);
                if (is_file($f = $dir."/".$base_name.$q)){
                    return $f;
                }
            }
        }
        return false;
    }
    /**
     * transform content an return data
     * @return mixed
     */
    abstract function transform(string $content);
    /**
     * init default source
     * @return null|string 
     */
    public function initDefaultSource():?string{
        return null;
    }
}