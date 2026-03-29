<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelCacheFilterBase.php
// @date: 20230120 19:29:25
namespace IGK\Database\Mapping;
/**
* model cache filter, used to validate and filter recieved data 
* @package IGK\Database\Mapping
*/
abstract class ModelCacheFilterBase{
    /**
    * Property: model.
    * @var mixed
    */
    var $model;
    /**
     * auto insertin
     * @var mixed
     */
    var $auto_insert;
    /**
    * auto generate doc.
    * @var mixed
    */
    var $column;
    /**
     * default value
     * @var mixed
     */
    var $default;
    /**
    * .ctr
    */
    protected function __construct()
    {
    }
    /**
    * Called when an object is used as a function.
    * @param null|string $data
    * @param null|string $column_name
    */
    public function __invoke(?string $data, ?string $column_name = null){
        return $this->map($data, $column_name);
    }
    /**
     * map data filter
     * @param string $data 
     * @param null|string $column_name 
     * @return mixed 
     */
    abstract function map(?string $data, ?string $column_name = null);
    /**
     * create the filter
     * @param mixed $model 
     * @param mixed $column 
     * @param bool $autoinsert 
     * @return static 
     */
    public static function CreateFilter($model, $column, $autoinsert=false){
		$s = new static;
		$s->auto_insert = $autoinsert;
		$s->model = $model;
        $s->column = $column;
		return $s;
	}
}