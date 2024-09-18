<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitDataAnnotation.php
// @date: 20240905 03:35:24
namespace IGK\Database\ORM\Annotations;

use IGK\Controllers\BaseController;
use IGK\Models\Traits\ModelTableConstantTrait;
use IGK\System\Annotations\AnnotationBase;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Helpers\AnnotationHelper;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
* init database constant annotations
* @package IGK\Database\ORM\Annotations
* @author C.A.D. BONDJE DOUE
*/
class InitDataAnnotation extends AnnotationBase{

    /**
     * 
     * @param BaseController $controller 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function InitData(BaseController $controller, bool $recursive=false){
        $_dir_classes = $controller->getClassesDir();
		foreach(igk_io_getfiles($_dir_classes, "/\.php$/i", $recursive) as $f){
			$n = igk_io_basenamewithoutext($f);
			$cl = $controller->resolveClass($n);
			if ($cl && class_exists($cl)){
				$ref = igk_sys_reflect_class($cl);
				$annotations = AnnotationHelper::GetAnnotations($ref);
				if ($annotations && igk_sys_reflect_is_support_trait($cl, ModelTableConstantTrait::class)){ 
					foreach ($annotations as $a) {
						if (($a instanceof InitDataAnnotation)) {
							$cl::InitData();
						}
					}
				}
			}
		}
    }
}   