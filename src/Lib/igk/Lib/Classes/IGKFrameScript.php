<?php
// @file: IGKFrameScript.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKFrameScript implements IHtmlGetValue{
    private $m_type;
    var $owner;
    ///<summary></summary>
    ///<param name="owner"></param>
    ///<param name="type" default="f"></param>
    public function __construct($owner, $type="f"){
        $this->owner=$owner;
        $this->m_type=$type;
    }
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    public function getValue($option=null){
        $n=IGK_STR_EMPTY;
        switch($n){
            case "c":
            $n="initconfirm";
            break;
            case "f":default: 
            $n="init";
            break;
        }
        return igk_get_string_format("igk.winui.frameBox.{$n}({0}],{1});", igk_getsv($this->owner->Width ? '"'.$this->owner->Width.'"': null, 'null'), igk_getsv($this->owner->Height ? '"'.$this->owner->Height.'"': null, 'null'));
    }
}
