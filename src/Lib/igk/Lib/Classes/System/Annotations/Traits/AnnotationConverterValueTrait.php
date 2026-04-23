<?php
// @author: C.A.D. BONDJE DOUE
// @file: AnnotationConverterValueTrait.php
// @date: 20240824 10:15:00
namespace IGK\System\Annotations\Traits;
use IGK\System\Annotations\PhpDocBlocReader;
use IGK\System\Helpers\AnnotationHelper;
use IGKException;
use ReflectionProperty;

/**
* auto generate doc.
* @package IGK\System\Annotations\Traits
* @author C.A.D. BONDJE DOUE
*/
trait AnnotationConverterValueTrait{
    /**
    * Converts Value.
    * @param mixed $obj
    * @param mixed $value
    * @param mixed $name
    * @param null|mixed $comment
    * @param null|mixed $store
    */
    abstract static function ConvertValue($obj, $value, $name, $comment = null, $store=null);
}