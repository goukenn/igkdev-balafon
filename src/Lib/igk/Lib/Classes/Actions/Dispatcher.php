<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Dispatcher.php
// @date: 20220803 13:48:58
// @desc: action dispatcher 


namespace IGK\Actions;

use Closure;
use Exception;
use IGK\Actions\IActionProcessor;
use IGK\System\Exceptions\ActionNotFoundException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Request;
use IGK\System\Http\RequestHeader;
use IGK\System\Http\RequestResponse;
use IGK\System\IInjectable;
use IGK\System\Regex\MatchPattern;
use IGK\System\Services\InjectorProvider;
use IGK\Actions\ActionBase;
use IGK\Controllers\ControllerParams;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\OperationNotAllowedException;
use IGKException;
use IGKType;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use TypeError;

/**
 * default action dispactcher
 */
class Dispatcher implements IActionProcessor, IActionDispatcher
{
    /**
     * 
     * @var null|ActionBase|IActionProcessor|object
     */
    private $m_host;
    private static $sm_macro;
    private static $sm_matches = [
        "int" => MatchPattern::Int,
        "float" => MatchPattern::Float,
    ];

    ///<sumary>.ctr</summary>
    /**
     * .ctr
     * @param null|IGKActionBase $host 
     * @return void 
     */
    public function __construct(?ActionBase $host)
    {
        $this->m_host = $host;
    }

    public function setBaseActionName(string $actionName)
    {
        $this->m_host->baseActionName = $actionName;
    }

    public function getBaseActionName(): string
    {
        return $this->m_host->baseActionName;
    }
    public function getController()
    {
        return $this->m_host ? $this->m_host->getController() : null;
    }
    public function getHost()
    {
        return $this->m_host;
    }
    /**
     * 
     * @param callable $fc 
     * @param mixed $args 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    protected static function _HandleDispatch(callable $fc, ...$args)
    {
        $g = new ReflectionFunction($fc);
        $args = self::GetInjectArgs($g, $args);
        try {
            return $fc(...$args);
        } catch (Exception $ex) {
            throw $ex;
        } catch (TypeError $ex) {
            // + | call to function but arguments injection no valid

            throw new OperationNotAllowedException('Dispatcher failed: '.$ex->getMessage(), 405, $ex);
        }
    }
    public static function __callStatic($name, $args)
    {
        if (self::$sm_macro === null) {
            self::$sm_macro = [];
            self::$sm_macro["Dispatch"] = function ($fc, ...$args) {

                return static::_HandleDispatch($fc, ...$args);
            };
        }
        if (is_callable($fc = igk_getv(self::$sm_macro, $name))) {
            return $fc(...$args);
        }
        return (new static(null))->$name(...$args);
    }
    public function invoke(string $name, ...$args)
    {
        return $this->__call($name, $args);
    }
    public function __call($name, $arguments)
    {
        // igk_wln_e(__FILE__.":".__LINE__, "call in dispacher....", $name, $this->host instanceof IActionProcessor, 
        // "???".method_exists($this, $name) );
        // igk_wln_e("the host ", $this->host, $name, "?".method_exists($this, $name), is_callable($g =  [$this->host, $name]));
        // igk_wln_e("but", $this->host, $name, get_class_methods($this->host), $this->host instanceof IActionProcessor);
        $v_host = $this->m_host;
        if (
            method_exists($v_host, $name)
            && (!(new ReflectionMethod($v_host, $name))->isStatic())
            && ($fc = Closure::fromCallable([$v_host, $name])->bindTo($v_host))
        ) {
            $v_host->getController()->{ControllerParams::REPLACE_URI} = true;
            $targs = array_merge([$fc], $arguments);
            return self::__callStatic("Dispatch", $targs);
        } else {
            if ($v_host instanceof IActionProcessor) {
                return call_user_func_array(
                    [$this->m_host, '__call'],
                    [$name, $arguments]
                );
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
        $args = self::GetInjectArgs($g, $args);
    }

    /**
     * get argument to inject or dispatch
     * @param mixed $parameters 
     * @param mixed $args 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetInjectArgsByParameters($parameters, $args)
    {
        $targs = [];
        self::_GetInjectedParameters($targs, $parameters, $args);
        return $targs;
    }

    /**
     * get injected args
     * @param ReflectionFunctionAbstract $g 
     * @param mixed $args 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetInjectArgs(ReflectionFunctionAbstract $g, $args): array
    {
        $parameters = $g->getParameters();
        if (count($parameters) == 0) {
            return $args;
        }
        $targs = [];
        self::_GetInjectedParameters($targs, $parameters, $args);

        // $injectors = InjectorProvider::GetInjectors();
        // $ctrl = ViewHelper::CurrentCtrl();
        // $i = 0;
        // $services = null;
        // if ($ctrl) {
        //     // + | --------------------------------------------------------------------
        //     // + | resolving services for injection
        //     // + |            
        //     $services = file_exists($fservice = $ctrl->configFile('services')) ?
        //         ViewHelper::Inc($fservice, ['ctrl' => $ctrl]) : null;
        // }

        // foreach ($parameters as $k) {
        //     $arg = igk_getv($args, $i);
        //     $c = $arg;

        //     if (($p = $k->getType()) && ($type = IGKType::GetName($p))) {
        //         if ($type == 'string') {
        //             $targs[] = $c;
        //             $i++;
        //             continue;
        //         }
        //         if ($type == "array") {
        //             $c = $c ? explode(',', $c) : []; // implode(",", $args[$i]);                                    
        //         } else {
        //             $pattern = igk_getv(self::$sm_matches, $type, ".+");
        //             if (is_string($c) && $c && !preg_match_all("#^" . $pattern . "$#", $c)) {
        //                 throw new ArgumentTypeNotValidException($i);
        //             }
        //         }
        //         // + | get inject table class printer service


        //         if (!IGKType::IsPrimaryType($type) && is_subclass_of($type, IInjectable::class) && $services && isset($services[$type])) {
        //             $rtype = $services[$type];
        //             $targs[] = DispatcherService::CreateOrGetServiceInstance($ctrl, $rtype);
        //             continue;
        //         }

        //         $v_primary = IGKType::IsPrimaryType($type);

        //         if (!$v_primary && class_exists($type)) {

        //             if (is_subclass_of($type, IInjectable::class)) {
        //                 $targs[] = self::_GetInjectable($type, $args);
        //                 continue;
        //             }
        //             $j = igk_getv($injectors, $type, InjectorProvider::getInstance()->injector($type));
        //             if ($j &&  ($c = $j->resolv($arg, $p))) {
        //                 $targs[] = $c;
        //                 continue;
        //             }
        //         } else if ($v_primary && is_null($c)) {
        //             if ($k->isDefaultValueAvailable()) {
        //                 $c =  $k->getDefaultValue();
        //             } else {
        //                 $c = preg_match("/(int|float|double|decimal)/i", $type) ? 0 : $c;
        //             }
        //         }
        //     } else {
        //         if ($arg === null && $k->isDefaultValueAvailable()) {
        //             $c = $k->getDefaultValue();
        //         }
        //     }
        //     $targs[] = $c;
        //     $i++;
        // }
        // if ($i < count($args)) {
        //     $targs = array_merge($targs, array_slice($args, $i));
        // }
        return $targs;
    }
    /**
     * 
     * @param array $targs 
     * @param mixed $parameters 
     * @param mixed $args 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _GetInjectedParameters(array &$targs, $parameters, $args)
    {
        $targs = [];
        $injectors = InjectorProvider::GetInjectors();
        $ctrl = ViewHelper::CurrentCtrl();
        $i = 0;
        $services = null;
        if ($ctrl) {
            // + | --------------------------------------------------------------------
            // + | resolving services for injection
            // + |            
            if ($fservice = $ctrl->configFile('services')){ 
                $services = file_exists($fservice) ?
                ViewHelper::Inc($fservice, ['ctrl' => $ctrl]) : null;
            }
        }

        foreach ($parameters as $k) {
            $arg = igk_getv($args, $i);
            $c = $arg;

            if (($p = $k->getType()) && ($type = IGKType::GetName($p))) {
                if ($type == 'string') {
                    $targs[] = $c;
                    $i++;
                    continue;
                }
                if ($type == "array") {
                    $c = $c ? explode(',', $c) : []; // implode(",", $args[$i]);                                    
                } else {
                    $pattern = igk_getv(self::$sm_matches, $type, ".+");
                    if (is_string($c) && $c && !preg_match_all("#^" . $pattern . "$#", $c)) {
                        throw new ArgumentTypeNotValidException($i);
                    }
                }
                // + | get inject table class printer service


                if (!IGKType::IsPrimaryType($type) && is_subclass_of($type, IInjectable::class) && $services && isset($services[$type])) {
                    $rtype = $services[$type];
                    $targs[] = DispatcherService::CreateOrGetServiceInstance($ctrl, $rtype);
                    continue;
                }

                $v_primary = IGKType::IsPrimaryType($type);

                if (!$v_primary && class_exists($type)) {
                    if (is_subclass_of($type, IInjectable::class)) {
                        $targs[] = self::_GetInjectable($type, $args);                        
                        continue;
                    }
                    $j = igk_getv($injectors, $type, InjectorProvider::getInstance()->injector($type));
                    if ($j &&  ($c = $j->resolve($arg, $p))) {
                        $targs[] = $c;
                        $i++;
                        continue;
                    }
                } else if ($v_primary && is_null($c)) {
                    if ($k->isDefaultValueAvailable()) {
                        $c =  $k->getDefaultValue();
                    } else {
                        $c = preg_match("/(int|float|double|decimal)/i", $type) ? 0 : $c;
                    }
                }
            } else {
                if ($arg === null && $k->isDefaultValueAvailable()) {
                    $c = $k->getDefaultValue();
                }
            }
            $targs[] = $c;
            $i++;
        }
        if ($i < count($args)) {
            $targs = array_merge($targs, array_slice($args, $i));
        }
        return $targs;
    }
    /**
     * retrieve injectable from deispacther
     * @param mixed $class_name 
     * @param mixed $type 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetInjectTypeInstance($class_name){
        return self::_GetInjectable($class_name, []);
    }
    private static function _GetInjectable($type, $args)
    {
        static $injects = null;
        if ($injects === null) {
            $injects = [
                Request::class => function () {
                    $i = Request::getInstance();
                    $i->setParam(func_get_args());
                    return $i;
                },
                RequestHeader::class => new RequestHeader(),
                RequestResponse::class => RequestResponse::CreateResponse(),
            ];
            // extract injector server 
        }
        if (!($m = igk_getv($injects, $type))) {
            $m = new $type();
            $injects[get_class($m)] = $m;
        }
        if (is_callable($m)) {
            return $m(...$args);
        }
        return $m;
    }
}
