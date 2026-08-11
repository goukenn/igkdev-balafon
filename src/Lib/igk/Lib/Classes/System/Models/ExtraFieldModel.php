<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExtraFieldModel.php
// @date: 20260811 13:14:07
namespace IGK\System\Models;

use ArrayAccess;
use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use JsonSerializable;

/**
* use to encapsulate a model and add extra properties
* @package IGK\System\Models
* @author C.A.D. BONDJE DOUE
*/
class ExtraFieldModel implements ArrayAccess, JsonSerializable{
    use ArrayAccessSelfTrait;

    private $model;
    private $m_data;
    public function __construct(ModelBase $model)
    {
        $this->model = $model;
        $this->m_data = [];
    }

    public function jsonSerialize(): mixed
    {
        return $this->to_array();
    }
    /**
     * 
     * @param string $name 
     * @return mixed 
     */
    public function __get(string $name)
    {
        if (key_exists($name, $this->m_data)){
            return igk_getv($this->m_data, $name);
        }
        return $this->model->{$name};
    }
    /**
     * 
     * @param string $key 
     * @param mixed $value 
     * @return void 
     */
    public function __set(string $key, $value){
        $bkey = [$key];
        if ($prefix = $this->model->getTableInfo()->prefix){
            $l = StringUtility::AutoPrefix($key, $prefix);
            if ($l!=$bkey[0]){
                $bkey[] = $l;
            }
        }
        while(count($bkey)){
            $q = array_shift($bkey);
            if ($this->model->columnExists($q)){
                $this->model->{$q} = $value;
                return;
            }

        }
        $this->m_data[$key] = $value;
    }
    public function to_array(){
        return array_merge($this->model->to_array(), $this->m_data);
    }
    /**
     * 
     * @param mixed $key 
     * @return mixed 
     */
    public function _access_OffsetGet($key){
        return $this->__get($key);
    }
    /**
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @return void 
     */
    public function _access_OffsetSet($key, $value){
        $this->__set($key, $value);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->model, $name], $arguments);
    }
}