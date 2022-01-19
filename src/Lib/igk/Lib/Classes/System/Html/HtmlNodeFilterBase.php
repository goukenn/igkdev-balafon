<?php

namespace IGK\System\Html;

use IGK\Helper\StringUtility;
use IGKException;

abstract class HtmlNodeFilterBase{
    public abstract function bind($node);

    public function prefilter($name, $args){
        return null;
    }

    public static function CreateFilter($tag){
        static $filters;
        if ($filters === null)
            $filters = [];
        
        $ns = static::GetEntryNameSpace() ?? str_replace("/","\\", dirname(str_replace("\\", "/", static::class)));
    

        $cn = "\\Filters\\".ucfirst($tag);
        $filter = $ns.$cn."Filter";

        if ($c = igk_getv($filters,  $filter)){
            if ($c==":Nofilter"){
                return null;
            }
            return $c;
        }
        $dir = static::FilterDir(); 

        if (file_exists($file = StringUtility::Dir($dir.$cn."Filter.php"))){
            require_once($file);
        } 

        if (class_exists($filter , false)){
            $o = new $filter();
            $filters[$filter] = $o;
            return $o;
        }
        $filters[$filter] = ":Nofilter";
        return null;
    }
    /**
     * filter node callback
     * @param mixed $e 
     * @return void 
     * @throws IGKException 
     */
    public static function FilterNodeCallback($e){  
        if ($filter = static::CreateFilter($e->args["tagname"])){
            $filter->bind($e->args["node"]);
        }
    }

    public static function PrefilterNodeHookCallback($e){
        $tag = $e->args["name"]; 
        if ($filter = self::CreateFilter($tag)){          
            if ($r =  $filter->prefilter($tag, $e)){
                $e->output = $r;
                $e->handle = 1;                
            }
        }
    }

    /**
     * override it to get install dir
     * @return string 
     */
    protected static function FilterDir(){
        return __DIR__;
    }
    protected static function GetEntryNameSpace(){
        return null;
    }
}