<?php
// @file: IGKJSPostFrameCmd.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\Html\IHtmlGetValue;

/**
* Igkjspost frame cmd.
*/
class IGKJSPostFrameCmd extends IGKObject implements IHtmlGetValue{
    /**
    * Properties: global, obj, t.
    * @var mixed
    */
    private $m_global, $m_obj, $m_t;
    /**
    * .ctr
    * @param mixed $obj
    * @param mixed $t
    * @param mixed $global
    */
    public function __construct($obj, $t, $global=false){
        if(($obj == null) || !igk_reflection_class_implement($obj, IHtmlGetValue::class))
            igk_die("PostFrameCommand");
        $this->m_obj=$obj;
        $this->m_t=$t;
        $this->m_global=$global;
    }
    /**
    * Returns Value.
    * @param null|mixed $options
    */
    public function getValue($options=null){
        $s=$this->m_obj->getValue($options);
        if(preg_match("/^javascript:/", $s)){
            return $s;
        }
        return igk_js_post_frame($s, $this->m_t, $this->m_global);
    }
}