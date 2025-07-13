<?php
// @file: IGKReaderBindingInfo.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
/**
 * represent reader binding info - state in environment
 * @package IGK\System\Html
 */
final class HtmlReaderBindingInfo{
    const PATH = __CLASS__.'::Construct';
    public function __construct(HtmlReader $_reader, $listener=null){
        igk_set_env(self::PATH, ["reader"=>$_reader, "info"=>[            
        ], "listener"=>$listener]);
    }
    public function __get($v){
        $g=igk_get_env(self::PATH);
        return igk_getv($g["info"], $v);
    }
    public function __set($k, $v){
        $g=igk_get_env(self::PATH);
        $g["info"][$k]=$v;
        igk_set_env(self::PATH, $g);
    }
    public function getInfoArray(){
        $g=igk_get_env(self::PATH);
        return $g["info"];
    }
    public function getName(){
        $g=igk_get_env(self::PATH);
        return $g["reader"]->getName();
    }
    public function setAttribute($x, $v){
        $g=igk_get_env(self::PATH);
        $fc=$g["listener"];
        if($fc){
            $fc($x, $v);
        }
        return $this;
    }
    public function setInfo($k, $v){
        $g=igk_get_env(self::PATH);
        $fc=$g["info"];
        $fc[$k]=$v;
        igk_set_env(self::PATH, $g);
    }
    public function setInfos($atab){
        $g=igk_get_env(self::PATH);
        $fc=$g["info"];
        $fc=array_merge($fc, $atab);
        $g["info"]=$fc;
        igk_set_env(self::PATH, $g);
    }
}