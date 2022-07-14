<?Php
namespace IGK\System\Traits;

trait MethodPropertyChainTrait{
    public function __call($n, $args){
        method_exists($this, "isAllowed") || igk_die("isAllowed method is missing in ".static::class); 
        if ($this->isAllowed($n, $args)){
            if (count($args)==1)
                $this->$n = $args[0];
            else {
                $this->$n = $args;
            }
        }
        return $this;
    }
    // use of this trait require a isAllowed method in order to work properly
    public function __get($n){
        method_exists($this, "isAllowed") || igk_die("isAllowed method is missing in ".static::class); 
        if ($this->isAllowed($n, null)){
            return null;
        } else {
            igk_die("property not allowed");
        }
    }
}
