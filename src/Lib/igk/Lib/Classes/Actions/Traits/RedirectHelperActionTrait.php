<?php
// @author: C.A.D. BONDJE DOUE
// @file: RedirectHelperActionTrait.php
// @date: 20221118 01:35:40
namespace IGK\Actions\Traits;


///<summary></summary>
/**
* 
* @package IGK\Actions\Traits
*/
trait RedirectHelperActionTrait{
    protected $redirect;
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