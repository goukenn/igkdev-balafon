<?php
// @author: C.A.D. BONDJE DOUE
// @file: AnnotationInfoAnnotation.php
// @date: 20230731 11:37:49
namespace IGK\System\Annotations;
use IGK\System\AnnotationBase;
use IGK\System\IAnnotation;
/**
* balafon's annotation for describe an annotation
* @package IGK\System\Annotations
*/
class AnnotationInfoAnnotation extends AnnotationBase implements IAnnotation{
    /**
     * class | method | property
     * @var ?string
     */
    var $target; 
    /**
     * allow multiple
     */
    var $multiple = false;

    /**
    * auto generate doc.
    * @param null|string $m
    * @return void
    */
    public function setMultiple(?string $m){
        if (is_null($m))
            $this->multiple = false;
        else 
            $this->multiple = igk_bool_val($m);
    }
    /**
     * get or set the target 
     * @param null|string $target 
     * @return void 
     */
    public function setTarget(?string $target){
        $p = explode('|', $target ?? '');
        $s = [];
        foreach(['class', 'method', 'property'] as $tp){
            if (in_array($tp , $p)){
                $s[] = $p;
            }
        }
        if (empty($s)){
            $s[] = '*';
        }
        $this->target = $s;
    }
}