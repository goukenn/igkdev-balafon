<?php
// @file: IGKJSPostFrameCmd.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\IHtmlGetValue;

class IGKJSPostFrameCmd extends IGKObject implements IHtmlGetValue{
    private $m_global, $m_obj, $m_t;
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="t"></param>
    ///<param name="global" default="false"></param>
    public function __construct($obj, $t, $global=false){
        if(($obj == null) || !igk_reflection_class_implement($obj, IHtmlGetValue::class))
            igk_die("PostFrameCommand");
        $this->m_obj=$obj;
        $this->m_t=$t;
        $this->m_global=$global;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options=null){
        $s=$this->m_obj->getValue($options);
        if(preg_match("/^javascript:/", $s)){
            return $s;
        }
        return igk_js_post_frame($s, $this->m_t, $this->m_global);
    }
}
