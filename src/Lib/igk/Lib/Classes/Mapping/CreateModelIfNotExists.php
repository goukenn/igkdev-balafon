<?php
// @author: C.A.D. BONDJE DOUE
// @file: CreateModelIfNotExists.php
// @date: 20230123 13:16:26
namespace IGK\Mapping;
/**
* auto generate doc.
* @package IGK\Mapping
*/
class CreateModelIfNotExists{
    /**
    * Property: model.
    * @var mixed
    */
    var $model ;
    /**
    * Callback handler for def callback.
    * @var mixed
    */
    var $defCallback;
    /**
    * .ctr
    * @param mixed $model
    * @param mixed $defCallback
    */
    public function __construct($model, $defCallback)
    {
        $this->model  = $model;        
        $this->defCallback = $defCallback;
    }
    /**
    * Called when an object is used as a function.
    * @param mixed $v
    * @param mixed $k
    */
    public function __invoke($v, $k)
    {
        $defCallback = $this->defCallback ;
        return $this->model::createIfNotExists($defCallback($v));
    }
}