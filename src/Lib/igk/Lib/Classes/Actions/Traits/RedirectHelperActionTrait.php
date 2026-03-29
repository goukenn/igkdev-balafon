<?php
// @author: C.A.D. BONDJE DOUE
// @file: RedirectHelperActionTrait.php
// @date: 20221118 01:35:40
namespace IGK\Actions\Traits;
/**
* auto generate doc.
* @package IGK\Actions\Traits
*/
trait RedirectHelperActionTrait{
    /**
    * Path to redirect.
    * @var mixed
    */
    protected $redirect;
    /**
    * Path to redirect coder.
    * @var mixed
    */
    protected $redirectCoder;
    /**
     * set the redirect 
     * @param string $path 
     * @return void 
     */
    protected function redirectTo(?string $path=null, ?int $code = 301){
        $this->redirect = $this->getController()->uri($path);
        $this->redirectCode = $code; 
    }
}