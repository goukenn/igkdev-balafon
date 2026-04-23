<?php
// @author: C.A.D. BONDJE DOUE
// @file: AnnotationBase.php
// @date: 20230731 09:37:23
namespace IGK\System;
use IGK\System\Traits\PropertyObjectTrait;

/**
* auto generate doc.
* @package IGK\System
*/
abstract class AnnotationBase implements IAnnotation{
    /**
    * Property: params.
    * @var mixed
    */
    private $m_params;
    use PropertyObjectTrait;
    /**
     * set parameter changed
     * @param array $params 
     * @return void 
     */
    public function setParams(array $params){
        $this->m_params = $params;
    }
    /**
     * get parameter 
     * @return mixed 
     */
    public function getParams():array{
        return $this->m_params;
    }
}