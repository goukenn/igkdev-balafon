<?php
// @author: C.A.D. BONDJE DOUE
// @file: AuthAnnotation.php
// @date: 20260102 08:25:55
namespace IGK\System\Core\Security\Annotations;

use IGK\System\AnnotationBase;

/**
* auto generate doc.
* @package IGK\System\Core\Security\Annotations
* @author C.A.D. BONDJE DOUE
*/
class AuthAnnotation extends AnnotationBase{

    /**
    * Property: auth.
    * @var mixed
    */
    var $auth;

    /**
    * Property: strict.
    * @var mixed
    */
    var $strict;

    /**
    * .ctr
    * @param null|string $auth
    */
    public function __construct(?string $auth)
    {
        $this->auth = $auth;
    }

    /**
    * Sets Auth.
    * @param null|bool $strict
    */
    public function setAuth(?bool $strict){
        $this->strict = $strict;
    }

}