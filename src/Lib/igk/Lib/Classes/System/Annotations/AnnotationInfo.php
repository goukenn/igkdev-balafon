<?php
// @author: C.A.D. BONDJE DOUE
// @file: AnnotationInfo.php
// @date: 20230731 11:37:49
namespace IGK\System\Annotations;

use IGK\System\AnnotationBase;
use IGK\System\IAnnotation;

///<summary></summary>
/**
* balafon mark for annotation
* @package IGK\System\Annotations
*/
class AnnotationInfo extends AnnotationBase implements IAnnotation{
    /**
     * class | method
     * @var mixed
     */
    var $target; // 

    /**
     * allow multiple
     */
    var $multiple = false;

    public function setMultiple(?string $m){
        if (is_null($m))
            $this->multiple = false;
        else 
            $this->multiple = igk_bool_val($m);
 
    }
}