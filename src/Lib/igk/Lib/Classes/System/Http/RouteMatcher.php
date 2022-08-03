<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RouteMatcher.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Http;

use IGK\Models\Users;
use function igk_resources_gets as __;
/**
 * 
 * @package App\Actions\Dashboard
 */
class RouteMatcher extends RouteHandler{
    /**
     * 
     * @var self route matcher chain
     */
    private $chainTo;
    private $root;
    private $throwClass;
    private function __construct($controller)
    {   
        parent::__construct("m:matcher", $controller);
    }
    /**
     * 
     * @return RouteMatcher 
     */
    function next (){
        if ($this->root ==null){
            $this->root = $this;
        }
        $c = self::Create($this->controller);
        $c->root = $this->root;
        $this->chainTo = $c;
        return $c;
    }
    /**
     * get root chain
     * @return mixed 
     */
    function root(){
        if ($this->root === null){
            return $this;
        }
        return $this->root;
    }
    public static function Create($controller){
        $m = new self($controller);
        return $m;
    }
    public function __debugInfo()
    {
        return [];
    }
    public function __toString()
    {
        return __CLASS__;
    }
    /**
     * handle all 
     * @return mixed 
     */
    public function checkAll(bool $throwException=true){
        /**
         * @var self $rc self
         */
        $rc = $this->root();
        while($rc){ 
            if ($rc->check()){
                return true;
            }
            $rc = $rc->chainTo;
        } 
        if ($throwException){
            $cl = $this->throwClass ?? PageNotFoundException::class;
            $args = [];
            if (is_array($cl)){
                $args = array_slice($cl, 1);
                $cl = $cl[0];
            }
            throw new $cl(...$args);
        }
        return false;
    }
    /**
     * 
     */
    public function check(?string $verb=null){
        $verb = $verb ?? igk_server()->REQUEST_METHOD;
        // check verb
        if (!in_array($verb, $this->verbs)) {
            return false;
        }  
        // check auth requirement
        $auth = $this->auth;
        if (is_bool($auth)){
            return $auth;
        }
        $user = Users::currentUser();
        if ($this->isAuthRequired()){
            if (!$user || !$user::auth($auth)){
                $this->throwClass = [AuthorizationRequiredException::class, 
                  __("AuthorizationRequire: {0}", implode(",", is_array($auth) ? $auth: [$auth]))
                ]; 
                
                return false;
            }
        }        
        return true;
    }

}
