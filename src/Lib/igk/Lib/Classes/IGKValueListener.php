<?php
// @file: IGKValueListener.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\HtmlUtils;

final class IGKValueListener extends IGKObject implements IIGKHtmlGetValue{
    private $m_attr, $m_owner;
    ///<summary></summary>
    ///<param name="owner"></param>
    ///<param name="attr"></param>
    public function __construct($owner, $attr){
        $this->m_owner=$owner;
        $this->m_attr=$attr;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options=null){
        $k=$this->m_attr;
        $v="";
        if(method_exists(get_class($this->m_owner), $k)){
            $v=$this->m_owner->$k($options);
        }
        else
            $v=$this->m_owner->$k;
        if($v){
            $rv= HtmlUtils::GetValue($v, $options);
            return $rv;
        }
        return null;
    }
}
