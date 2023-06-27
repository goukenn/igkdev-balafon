<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MacrosTrait.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Traits;

use Closure;

trait MacrosTrait
{
    private static $macros;

    public static function __callStatic($name, $arguments)
    {
        if (is_null(self::$macros)) {
            self::$macros = [];
            static::InitMacros(self::$macros);
        }
        if (method_exists(static::class, "_InvokeMacros")) {
            return static::_InvokeMacros(self::$macros, $name, $arguments);
        }
    }
    public function __call($name, $arguments)
    {
        array_unshift($arguments, $this);
        return self::__callStatic($name, $arguments);
    }
    protected static function InitMacros(& $macros)
    {
        $macros = [
            MacrosConstant::RegisterMacroMethod => function ($name, callable $callback) use (&$macros) {
                if (is_callable($callback)) {
                    $callback = Closure::fromCallable($callback);
                }
                if (__CLASS__ == static::class) {
                    $macros[$name] = $callback;
                } else {
                    $macros[static::class . MacrosConstant::ClosureSeparator . $name] = $callback;
                }
            },
            MacrosConstant::UnRegisterExtensionMethod  => function ($name) use (&$macros) {
                unset($macros[static::class . MacrosConstant::ClosureSeparator . $name]);
            },
            /**
             * return the callable
             */
            MacrosConstant::getMacroMethod => function ($name) use (&$macros): ?callable {
                return igk_getv($macros, static::class . MacrosConstant::ClosureSeparator . $name);
            },
            MacrosConstant::RegisterExtensionMethod => function ($classname) use (&$macros) {
                $cl = static::class;
                $f = igk_sys_reflect_class($classname);
                foreach ($f->getMethods() as $k) {
                    if ($k->isStatic()) {
                        $macros[$cl . MacrosConstant::StaticSeparator . $k->getName()] = [$classname, $k->getName()];
                    }
                }                
            },
            MacrosConstant::getMacroKeysMethod => function () {
                return array_keys(self::$macros);
            },
            MacrosConstant::getInstanceMethod  => function () {
                return igk_environment()->createClassInstance(static::class);
            }
        ];
        return $macros;
    }
}
