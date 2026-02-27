<?php
// @file: IGKBalafonApplicationMiddlewareManager.php
// @author: C.A.D. BONDJE DOUE
// @copyright: igkdev © 2019
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\Middlewares\BalafonMiddleware;
use IGK\System\Middlewares\RunCallbackMiddleware;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Services\IBalafonApplicationMiddlewareService;
/**
* Represent IGKBalafonApplicationMiddlewareManager class
*/
class IGKBalafonApplicationMiddlewareManager implements IBalafonApplicationMiddlewareService{
    use ArrayAccessSelfTrait;

    /**
    * Property: properties.
    * @var mixed
    */
    private $_properties;

    /**
    * Collection of where list.
    * @var mixed
    */
    private $_whereList;

    /**
    * auto generate doc.
    * @param mixed $args
    */

    public function __call($n, $args){
        if(strpos(strtolower($n), "use") === 0){
            ($middle=BalafonMiddleware::CreateMiddleware($t=substr($n, 3), $args, $this)) || igk_die("failed to get middleware $t");
            return $this;
        }
        return null;
    }

    /**
    * auto generate doc.
    */
    function __construct(){
        $this->_whereList=array();
        $this->_properties=array();
    }

    /**
    * auto generate doc.
    * @param mixed $middleware
    */

    public function Attach($middleware){
        $w=& $this->_whereList;
        $w[]=$middleware;
    }

    /**
    * auto generate doc.
    */
    public function GetLastMiddleware(){
        $w=& $this->_whereList;
        if(($c=count($w)) > 0){
            return $w[$c-1];
        }
        return null;
    }

    /**
    * auto generate doc.
    * @param mixed $i
    */

    protected function _access_offsetExists($i):bool{
        return isset($this->_properties[$i]);
    }

    /**
    * auto generate doc.
    * @param mixed $i
    */

    protected function _access_offsetGet($i){
        return isset($this->_properties[$i]) ? $this->_properties[$i]: null;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    protected function _access_offsetSet($i, $v){
        if($v == null)
            unset($this->_properties[$i]);
        else
            $this->_properties[$i]=$v;
    }

    /**
    * auto generate doc.
    * @param mixed $i
    */

    protected function _access_offsetUnset($i){
        unset($this->_properties[$i]);
    }

    /**
    * auto generate doc.
    */
    public function Process(){
        BalafonMiddleware::Process($this, $this->_whereList);
    }

    /**
    * auto generate doc.
    * @param mixed $closurecallback
    */

    public function Run($callback){
        BalafonMiddleware::Attach(new RunCallbackMiddleware($callback), $this);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $middle
    */

    public function UseMiddleWare($middle){
        if(is_object($middle) && is_subclass_of(get_class($middle), BalafonMiddleware::class))
            BalafonMiddleware::Attach($middle, $this);
        return $this;
    }
}