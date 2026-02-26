<?php
// @file: IGKValueListener.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK;

use IGK\System\Html\IHtmlGetValue;
use IGK\System\Html\HtmlUtils;
use IGKObject;

/**
 * represent a value helper
 * @package 
 */
final class ValueListener extends IGKObject implements IHtmlGetValue{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_attr, $m_owner;

    /**
    * .ctr
    * @param mixed $owner
    * @param mixed $attr
    */
    public function __construct($owner, $attr){
        $this->m_owner=$owner;
        $this->m_attr=$attr;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
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

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return $this->getValue().'';
    }
}