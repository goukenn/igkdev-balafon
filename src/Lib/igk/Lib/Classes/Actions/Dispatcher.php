<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Dispatcher.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Actions;

use Closure;
use Exception;
use IGK\Actions\IActionProcessor;
use IGK\Models\Injectors\ModelBaseInjector;
use IGK\System\Exceptions\ActionNotFoundException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\NotInjectableTypeException;
use IGK\System\Exceptions\RequireArgumentException;
use IGK\System\Http\Request;
use IGK\System\Http\RequestHeader;
use IGK\System\Http\RequestResponse;
use IGK\System\Http\WebResponse;
use IGK\System\IInjectable;
use IGK\System\Regex\MatchPattern;
use IGK\System\Services\InjectorProvider;
use IGKActionBase;
use IGKType;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * default action dispactcher
 */
class Dispatcher implements IActionProcessor
{
    private $host;
    private static $sm_macro;
    private static $sm_matches = [
        "int" => MatchPattern::Int,
        "float" => MatchPattern::Float,
    ];

    ///<sumary>.ctr</summary>
    /**
     * 
     * @param null|IGKActionBase $host 
     * @return void 
     */
    public function __construct(?IGKActionBase $host)
    {
        $this->host = $host;
    }
    public function getController(){
        return $this->host ? $this->host->getController():null;
    }
    public function getHost(){
        return $this->host;
    }
    public static function __callStatic($name, $args)
    {

        if (self::$sm_macro === null) {
            self::$sm_macro = [];
            self::$sm_macro["Dispatch"] = function ($fc, ...$args) {
                $g = new ReflectionFunction($fc); 
                $args = self::GetInjectArgs($g, $args);                 
                try {
                    return $fc(...$args);
                } catch (Exception $ex) {
                    throw $ex;
                }
            };
        }
        if (is_callable($fc = igk_getv(self::$sm_macro, $name))) {         
            return $fc(...$args);
        }
        return (new static(null))->$name(...$args);
    }
    public function __call($name, $arguments)
    {
        // igk_wln_e(__FILE__.":".__LINE__, "call in dispacher....", $name, $this->host instanceof IActionProcessor, 
        // "???".method_exists($this, $name) );
        // igk_wln_e("the host ", $this->host, $name, "?".method_exists($this, $name), is_callable($g =  [$this->host, $name]));
        // igk_wln_e("but", $this->host, $name, get_class_methods($this->host), $this->host instanceof IActionProcessor);
        if (
            method_exists($this->host, $name)  
            && (!(new ReflectionMethod($this->host, $name))->isStatic())           
            && ($fc = Closure::fromCallable([$this->host, $name])->bindTo($this->host))
        ) {
            $targs = array_merge([$fc], $arguments);  
            return self::__callStatic("Dispatch", $targs);
        } else { 
            if ($this->host instanceof IActionProcessor){  
                return $this->host->__call($name, $arguments); 
            }
        } 
        throw new ActionNotFoundException($name);
    }

    /**
     * @param ReflectionFunction #Parameter#cd4a68c3 
     * @param IGK\Actions\ref #Parameter#ca4a640a 
     * @param mixed $args 
     * @return void 
     */
    public static function ResolvDispatchMethod(ReflectionFunctionAbstract $g, &$args)
    {
        $cl = null;
        $required =  $g->getNumberOfRequiredParameters();
        $args = self::GetInjectArgs($g, $args);
        // | try to inject parameter
        // if (($required >= 1) &&
        //     ($parameters = $g->getParameters()) &&
        //     ($cl = $parameters[0]->getType()) &&
        //     (IGKType::GetName($cl) ===  Request::class)
        // ) {
        //     $req =  Request::getInstance();
        //     $req->setParam($args);
        //     $args = [$req];
        //     for ($i = 1; $i < count($parameters); $i++) {
        //         if (($p = $parameters[$i]->getType()) && (class_exists($type = IGKType::GetName($p)))) {

        //             $c = new $type();
        //             $args[] = $c;
        //             continue;
        //         }
        //         throw new NotInjectableTypeException($i);
        //     }
        // } elseif (isset($parameters)) {
        //     for ($i = 0; $i < count($parameters); $i++) {
        //         if (!$parameters[$i]->isOptional()) {
        //             if ($i >= count($args)) {
        //                 throw new RequireArgumentException($required, count($args));
        //             }
        //         }
        //         if (($p = $parameters[$i]->getType()) && (class_exists($type = IGKType::GetName($p)))) {

        //             $c = new $type();
        //             $args[$i] = $c;
        //             continue;
        //         } else {
        //             if ($p) {
        //                 $tname = IGKType::GetName($p);
        //                 $v = igk_getv($args, $i);
        //                 // igk_wln("type : ", $tname);
        //                 if ($tname == "array") {
        //                     $args[$i] = $v ? explode(",", $v) : []; // implode(",", $args[$i]);                                    
        //                 } else {
        //                     $pattern = igk_getv(self::$sm_matches, $tname, ".+");
        //                     if ($v && !preg_match_all("#^" . $pattern . "$#", $v)) {
        //                         throw new ArgumentTypeNotValidException($i);
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
    }


    public static function GetInjectArgs(ReflectionFunctionAbstract $g, $args): array
    {
        $parameters = $g->getParameters();
        if (count($parameters) == 0){
            return $args;
        }
        $targs = [];       
        $injectors = InjectorProvider::GetInjectors();  
        $i = 0; 
        foreach ($parameters as $k) {
            $arg = igk_getv($args, $i);
            $c = $arg; 

            if (($p = $k->getType()) && ($type = IGKType::GetName($p))) {
                if ($type == "array") {
                    $c = $c ? explode(",", $c) : []; // implode(",", $args[$i]);                                    
                } else {
                    $pattern = igk_getv(self::$sm_matches, $type, ".+");
                    if (is_string($c) && $c && !preg_match_all("#^" . $pattern . "$#", $c)) {                        
                        throw new ArgumentTypeNotValidException($i);
                    }
                }
                if (class_exists($type)){                    
                    if (is_subclass_of($type, IInjectable::class)){
                        $targs[] = self::_GetInjectable($type, $args);                   
                        continue;
                    }
                    $j = igk_getv($injectors, $type, InjectorProvider::getInstance()->injector($type));  
                    if ($j &&  ($c = $j->resolv($arg, $p))){                        
                        $targs[] = $c;
                        continue; 
                    } 
                }
            } else {
                if ($arg === null && $k->isDefaultValueAvailable()){
                    $c = $k->getDefaultValue();
                }
            }
            $targs[] = $c;
            $i++;
        }
        return $targs;
    }
    private static function _GetInjectable($type, $args){
        static $injects = null;
        if ($injects===null){
            $injects = [
                Request::class=>function(){
                    $i = Request::getInstance();
                    $i->setParam(func_get_args());
                    return $i;
                },
                RequestHeader::class=>new RequestHeader(),
                RequestResponse::class=>RequestResponse::CreateResponse(),
            ];
            // extract injector server 
        }
        if (!($m = igk_getv($injects, $type))){
            $m = new $type();
            $injects[get_class($m)] = $m;
        }        
        if (is_callable($m)){
            return $m(...$args);
        }
        return $m;        
    }
}



 