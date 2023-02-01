<?php
// @author: C.A.D. BONDJE DOUE
// @file: CreateModelIfNotExists.php
// @date: 20230123 13:16:26
namespace IGK\Mapping;


///<summary></summary>
/**
* 
* @package IGK\Mapping
*/
class CreateModelIfNotExists{
    var $model ;
    var $defCallback; 
    public function __construct($model, $defCallback)
    {
        $this->model  = $model;        
        $this->defCallback = $defCallback;
    }
    public function __invoke($v, $k)
    {
        $defCallback = $this->defCallback ;
        return $this->model::createIfNotExists($defCallback($v));
    }
}