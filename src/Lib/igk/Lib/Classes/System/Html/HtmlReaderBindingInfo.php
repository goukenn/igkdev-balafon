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

    /**
    * auto generate doc.
    * @var mixed
    */
    const PATH = __CLASS__.'::Construct';

    /**
    * .ctr
    * @param HtmlReader $_reader
    * @param null|mixed $listener
    */
    public function __construct(HtmlReader $_reader, $listener=null){
        igk_set_env(self::PATH, ["reader"=>$_reader, "info"=>[            
        ], "listener"=>$listener]);
    }

    /**
    * .destructor
    * @param mixed $v
    */
    public function __get($v){
        $g=igk_get_env(self::PATH);
        return igk_getv($g["info"], $v);
    }

    /**
    * destructor
    * @param mixed $k
    * @param mixed $v
    */
    public function __set($k, $v){
        $g=igk_get_env(self::PATH);
        $g["info"][$k]=$v;
        igk_set_env(self::PATH, $g);
    }

    /**
    * auto generate doc.
    */
    public function getInfoArray(){
        $g=igk_get_env(self::PATH);
        return $g["info"];
    }

    /**
    * auto generate doc.
    * @return mixed
    */
    public function getName(): mixed{
        $g=igk_get_env(self::PATH);
        return $g["reader"]->getName();
    }

    /**
    * auto generate doc.
    * @param mixed $x
    * @param mixed $v
    */
    public function setAttribute($x, $v){
        $g=igk_get_env(self::PATH);
        $fc=$g["listener"];
        if($fc){
            $fc($x, $v);
        }
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $k
    * @param mixed $v
    */
    public function setInfo($k, $v){
        $g=igk_get_env(self::PATH);
        $fc=$g["info"];
        $fc[$k]=$v;
        igk_set_env(self::PATH, $g);
    }

    /**
    * auto generate doc.
    * @param mixed $atab
    */
    public function setInfos($atab){
        $g=igk_get_env(self::PATH);
        $fc=$g["info"];
        $fc=array_merge($fc, $atab);
        $g["info"]=$fc;
        igk_set_env(self::PATH, $g);
    }
}