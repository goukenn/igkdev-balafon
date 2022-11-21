<?php
// @author: C.A.D. BONDJE DOUE
// @file: NotifyActionTrait.php
// @date: 20221117 21:42:51
namespace IGK\Actions\Traits;


///<summary></summary>
/**
* 
* @package IGK\Actions\Traits
*/
trait NotifyActionTrait{
    /**
     * manual change the notication name
     * @var mixed
     */
    var $notifyActionName;
    
    protected function error(string $message){
        $not = $this->getNoticationController();
        return $not->error($message);        
    }
    protected function danger(string $message){
        $not = $this->getNoticationController();
        return $not->danger($message);        
    }
    protected function success(string $message){
        $not = $this->getNoticationController();
        return $not->success($message);        
    }
    protected function msg(string $message, string $type){
        $not = $this->getNoticationController();
        return $not->msg($message, $type);        
    }
    protected function notify(string $message, ?string $type='default'){
        if ($not = $this->getNoticationController()){
            return $not->msg($message, $type);
        }
    }
    protected function getNoticationController(){
        return igk_notifyctrl($this->notifyActionName ?? $this->fname);
    }
}