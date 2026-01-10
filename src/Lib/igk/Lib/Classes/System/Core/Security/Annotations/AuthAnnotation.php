<?php
// @author: C.A.D. BONDJE DOUE
// @file: AuthAnnotation.php
// @date: 20260102 08:25:55
namespace IGK\System\Core\Security\Annotations;

use IGK\System\AnnotationBase;

/**
* 
* @package IGK\System\Core\Security\Annotations
* @author C.A.D. BONDJE DOUE
*/
class AuthAnnotation extends AnnotationBase{
    var $auth;
    var $strict;
    public function __construct(?string $auth)
    {
        $this->auth = $auth;
    }
    public function setAuth(?bool $strict){
        $this->strict = $strict;
    }

}