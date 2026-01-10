<?php
// @author: C.A.D. BONDJE DOUE
// @file: SecurityAnnotation.php
// @date: 20251231 18:28:03
namespace IGK\System\Core\Security\Annotations;

use IGK\System\AnnotationBase;

/**
* annotation used to bind security to call method request 
* @package IGK\System\Core\Security\Annotations
* @author C.A.D. BONDJE DOUE
*/
class SecurityAnnotation extends AnnotationBase{
    /**
     * authentication list 
     * @var ?string|array
     */
    var $auth;
    /**
     * security
     * @var ?string
     */
    var $security;
    /**
     * stict definition 
     * @var ?bool
     */
    var $strict;

    public function __construct(?string $security)
    {
        $this->security = $security;
    }
    public function setStrict(?bool $strict){
        $this->strict = $strict;
    }
    /**
     * 
     * @param mixed $auth 
     * @return void 
     */
    public function setAuth($auth){
        if (is_string($auth)){
            $this->auth = explode(',', $auth);
            return;
        }
        $this->auth = $auth;
    }
}