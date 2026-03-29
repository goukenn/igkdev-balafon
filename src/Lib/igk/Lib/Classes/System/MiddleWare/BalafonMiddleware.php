<?php
// @file: IGKBalafonMiddleware.php
// @author: C.A.D. BONDJE DOUE
// @copyright: igkdev © 2019
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Middlewares;
use ReflectionClass;
/**
* Represent IGKBalafonMiddleware class
*/
abstract class BalafonMiddleware{
    /**
    * Property: next.
    * @var mixed
    */
    private $_next;
    /**
    * Property: manager.
    * @var mixed
    */
    private static $sm_manager;
    /**
    * Property: chain flag.
    * @var mixed
    */
    var $chainFlag;
    /**
    * auto generate doc.
    */
    protected function __construct(){}
    /**
    *  attach the middleware
    * @param mixed $middlethe middleware to attach
    * @param mixed $serviceapplication service to initialize
    * @param mixed $wherelist list that store the all middleware for chain list
    */
    public static function Attach($middle, $service){
        if($c=$service->GetLastMiddleware()){
            $c->_next=$middle;
        }
        $service->Attach($middle);
        $middle->initialize($middle);
    }
    /**
    * auto generate doc.
    * @param mixed $service
    */
    public static function CreateMiddleware($name, $args=null, $service=null){
        if($name === __CLASS__)
            return null;
        if(class_exists($cl=$name) || (class_exists($cl="IGK".$name."Middleware"))){
            if(is_subclass_of($cl, __CLASS__)){
                $_ref=igk_sys_reflect_class($cl);
                $cp=0;
                $middle=null;
                if(($ctr=$_ref->getConstructor()) && (($cp=$ctr->getNumberOfRequiredParameters()) > 0)){
                    $middle=$_ref->newInstanceArgs($args);
                }
                else
                    $middle=new $cl();
                self::Attach($middle, $service);
                return $middle;
            }
        }
        return null;
    }
    /**
    * auto generate doc.
    */
    public static function GetManager(){
        if(count($c=self::$sm_manager) > 0){
            return self::$sm_manager[0];
        }
        return null;
    }
    /**
    * auto generate doc.
    */
    public function getService(){
        return self::GetManager();
    }
    ///<param name="service">IBalafonApplicationMiddlewareService instance</summary>
    /**
    * initialize the middleware
    * @param mixed $serviceIBalafonApplicationMiddlewareService instance
    */
    protected function initialize($service){}
    /**
    * auto generate doc.
    */
    public function invoke(){
        $this->next();
    }
    /**
    * auto generate doc.
    */
    protected function next(){
        if($this->_next){
            $this->_next->invoke();
        }
    }
    /**
    * auto generate doc.
    * @param mixed $wherelist
    */
    public static function Process($service, $wherelist){
        if(self::$sm_manager == null)
            self::
        $sm_manager=array();
        array_unshift(self::$sm_manager, $service);
        if(count($wherelist) > 0){
            $wherelist[0]->invoke();
        }
        array_shift(self::$sm_manager);
    }
    /**
    * auto generate doc.
    */
    protected function stopChain(){
        $this->chainFlag=1;
    }
}