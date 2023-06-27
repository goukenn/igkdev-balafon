<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterServiceActionTrait.php
// @date: 20221115 08:47:02
namespace IGK\Actions\Traits;

use IGK\Actions\Dispatcher;
use IGK\Models\Mailinglists;
use IGK\System\Exceptions\ActionNotFoundException;
use igk_default\Actions\MailingStates;
use ReflectionMethod;

///<summary></summary>
/**
* use to register mail actions - follows us 
* @package IGK\Actions\Traits
*/
trait RegisterServiceActionTrait{
    /**
     * register service
     */
    protected function registerService(string $t){
        $action = str_replace("-", "_", $t);
        if (method_exists($this, $action)){
            $args = Dispatcher::GetInjectArgs(new ReflectionMethod($this, $action), array_slice(func_get_args(), 1));
            return $this->$action(...$args); 
        }
        throw new ActionNotFoundException($t);        
    }

    private function activate_mail(){
        $g = base64_decode(igk_getr("q"));
        parse_str($g, $q);
        $r = false;
        if ($c = igk_getv($q, "email")){
            $r = Mailinglists::update([
                "clState"=>MailingStates::MAILING_STATE_ACTIVE], [
                "clEmail"=>$c
            ]) !== null;            
        }
        $g = $this->getController();
        igk_navto($g::uri("/?q=MailService&r=".$r));

    }
   
    private function unregister_mail(){
        $g = base64_decode(igk_getr("q"));
        parse_str($g, $q);
        $r = false;
        if ($c = igk_getv($q, "email")){
            $r = Mailinglists::update(["clState"=>MailingStates::MAILING_STATE_UNSUBCRIBE], [
                "clEmail"=>$c
            ]) !== null;            
        }
        $g = $this->getController();
        igk_navto($g::uri("/?q=MailService&r=".$r));
    }
}