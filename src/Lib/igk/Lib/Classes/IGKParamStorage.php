<?php
// @file: IGKParamStorage.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Igkparam storage.
*/
class IGKParamStorage extends IGKObject implements IParamHostService{

    /**
    * Property: params.
    * @var mixed
    */
    private $m_params;
    /**
     * Constructor.
     */

    public function __construct(){
        $this->m_params=array();
    }
    /**
     * Returns the value of a stored parameter by key.
     * @param string $key The parameter key to look up.
     * @param mixed $default The default value if the key is not found.
     * @return mixed The parameter value or the default.
     */

    public function getParam($key, $default=null){
        return igk_getv($this->m_params, $key, $default);
    }
    /**
     * Returns all stored parameter keys.
     * @return array An array of parameter key names.
     */

    public function getParamKeys(){
        return array_keys($this->m_params);
    }
    /**
     * Clears all stored parameters.
     */

    public function resetParam(){
        $this->m_params=array();
    }
    /**
     * Stores a parameter value under the given key.
     * @param string $key The parameter key to set.
     * @param mixed $value The value to associate with the key.
     */

    public function setParam($key, $value){
        $this->m_params[$key]=$value;
    }
    /**
     * Removes a stored parameter by key.
     * @param string $key The parameter key to remove.
     */

    public function unsetParam($key){
        unset($this->m_params[$key]);
    }
}
