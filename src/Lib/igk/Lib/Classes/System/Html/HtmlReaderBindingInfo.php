<?php
// @file: IGKReaderBindingInfo.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html;

/**
 * represent reader binding info - state in environment
 * @package IGK\System\Html
 */
final class HtmlReaderBindingInfo{
    const PATH = __CLASS__.'::Construct';
    ///<summary></summary>
    ///<param name="_reader"></param>
    ///<param name="listener" default="null"></param>
    public function __construct(HtmlReader $_reader, $listener=null){
        igk_set_env(self::PATH, ["reader"=>$_reader, "info"=>[            
        ], "listener"=>$listener]);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function __get($v){
        $g=igk_get_env(self::PATH);
        return igk_getv($g["info"], $v);
    }
    ///<summary></summary>
    ///<param name="k"></param>
    ///<param name="v"></param>
    public function __set($k, $v){
        $g=igk_get_env(self::PATH);
        $g["info"][$k]=$v;
        igk_set_env(self::PATH, $g);
    }
    ///<summary></summary>
    public function getInfoArray(){
        $g=igk_get_env(self::PATH);
        return $g["info"];
    }
    ///<summary></summary>
    public function getName(){
        $g=igk_get_env(self::PATH);
        return $g["reader"]->getName();
    }
    ///<summary></summary>
    ///<param name="x"></param>
    ///<param name="v"></param>
    public function setAttribute($x, $v){
        $g=igk_get_env(self::PATH);
        $fc=$g["listener"];
        if($fc){
            $fc($x, $v);
        }
        return $this;
    }
    ///<summary></summary>
    ///<param name="k"></param>
    ///<param name="v"></param>
    public function setInfo($k, $v){
        $g=igk_get_env(self::PATH);
        $fc=$g["info"];
        $fc[$k]=$v;
        igk_set_env(self::PATH, $g);
    }
    ///<summary></summary>
    ///<param name="atab"></param>
    public function setInfos($atab){
        $g=igk_get_env(self::PATH);
        $fc=$g["info"];
        $fc=array_merge($fc, $atab);
        $g["info"]=$fc;
        igk_set_env(self::PATH, $g);
    }
}
