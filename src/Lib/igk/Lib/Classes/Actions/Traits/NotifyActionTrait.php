<?php
// @author: C.A.D. BONDJE DOUE
// @file: NotifyActionTrait.php
// @date: 20221117 21:42:51
namespace IGK\Actions\Traits;

/**
* auto generate doc.
* @package IGK\Actions\Traits
*/
trait NotifyActionTrait{
    /**
     * manual change the notication name
     * @var mixed
     */
    var $notifyActionName;
    /**
    * Error.
    * @param string $message
    */
    protected function error(string $message){
        $not = $this->getNoticationController();
        return $not->error($message);        
    }
    /**
    * Danger.
    * @param string $message
    */
    protected function danger(string $message){
        $not = $this->getNoticationController();
        return $not->danger($message);        
    }
    /**
    * Success.
    * @param string $message
    */
    protected function success(string $message){
        $not = $this->getNoticationController();
        return $not->success($message);        
    }
    /**
    * Msg.
    * @param string $message
    * @param string $type
    */
    protected function msg(string $message, string $type){
        $not = $this->getNoticationController();
        return $not->msg($message, $type);        
    }
    /**
    * Notifies.
    * @param string $message
    * @param null|string $type
    */
    protected function notify(string $message, ?string $type='default'){
        if ($not = $this->getNoticationController()){
            return $not->msg($message, $type);
        }
    }
    /**
    * Returns Notication Controller.
    */
    protected function getNoticationController(){
        return igk_notifyctrl($this->notifyActionName ?? $this->fname);
    }
}