<?php
// @file: IGKObjectStrict.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* auto generate doc.
*/
final class IGKObjectStrict{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_ins;
    /**
     * Catches calls to undefined methods and returns null.
     * @param string $n The method name being called.
     * @param array $params The arguments passed to the call.
     * @return null Always returns null.
     */

    public function __call($n, $params){
        return null;
    }
    /**
     * Constructor.
     */
    private function __construct(){
    }
    /**
     * Returns the value of a declared property by key.
     * @param string $key The property key to retrieve.
     * @return mixed The value stored under the given key.
     */

    public function __get($key){
        return igk_getv($this->m_ins, $key);
    }
    /**
     * Sets the value of a declared property, dying if the key is not allowed.
     * @param string $key The property key to set.
     * @param mixed $value The value to assign.
     */

    public function __set($key, $value){
        if(!isset($this->m_ins, $key))
            igk_die("setting of $key is not allowed");
        $this->m_ins[$key ]=$value;
    }
    /**
     * Creates a strict object from an array of allowed string keys.
     * @param array $arraykey An array of string key names to allow.
     * @return static|null A new instance with allowed keys, or null on failure.
     */

    public static function Create($arraykey){
        if(is_array($arraykey) && igk_count($arraykey) > 0){
            $m=array();
            foreach($arraykey as $n){
                if(is_string($n))
                    $m[$n]=null;
            }
            if(igk_count($m) > 0){
                $g=new IGKObjectStrict();
                $g->m_ins=$m;
                return $g;
            }
        }
        return null;
    }
}
