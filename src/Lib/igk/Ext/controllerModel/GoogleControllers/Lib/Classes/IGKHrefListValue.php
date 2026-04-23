<?php
// @file: IGKHrefListValue.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Represent IGK\Core\Ext\Google namespace
*/
namespace IGK\Core\Ext\Google;
/**
* represent list get value storage
*/
final class IGKHrefListValue{
    /**
    * Property: values.
    * @var mixed
    */
    var $values;
    /**
    * auto generate doc.
    */
    public function __construct(){
        $this->values=func_get_args();
    }
    /**
    * display value
    */
    public function __toString(){
        return $this->getValue();
    }
    /**
    * auto generate doc.
    * @param mixed $options the default value is null
    */
    public function getValue($options=null){
        $o=0;
        if(isset($options->Document)){
            if(is_array($this->values)){
                $o=array_shift($this->values);
                if(count($this->values) == 1)
                    $this->values=$this->values[0];
            }
            else
                $o=$this->values;
        }
        else{
            if(is_array($this->values) && (count($this->values) > 0))
                $o=$this->values[0];
            else
                $o=$this->values;
        }
        if(is_object($o)){
            $o=$o->getValue();
        }
        return $o;
    }
}