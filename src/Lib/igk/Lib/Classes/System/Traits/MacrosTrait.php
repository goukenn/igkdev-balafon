<?php

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
            "registerMacro" => function ($name, callable $callback) use (&$macros) {
                if (is_callable($callback)) {
                    $callback = Closure::fromCallable($callback);
                }
                if (__CLASS__ == static::class) {
                    $macros[$name] = $callback;
                } else {
                    $macros[static::class . MacrosConstant::ClosureSeparator . $name] = $callback;
                }
            },
            "unregisterMacro" => function ($name) use (&$macros) {
                unset($macros[static::class . MacrosConstant::ClosureSeparator . $name]);
            },
            /**
             * return the callable
             */
            "getMacro" => function ($name) use (&$macros): ?callable {
                return igk_getv($macros, static::class . MacrosConstant::ClosureSeparator . $name);
            },
            "registerExtension" => function ($classname) use (&$macros) {
                $cl = static::class;
                $f = igk_sys_reflect_class($classname);
                foreach ($f->getMethods() as $k) {
                    if ($k->isStatic()) {
                        $macros[$cl . MacrosConstant::StaticSeparator . $k->getName()] = [$classname, $k->getName()];
                    }
                }                
            },
            "getMacroKeys" => function () {
                return array_keys(self::$macros);
            },
            "getInstance" => function () {
                return igk_environment()->createClassInstance(static::class);
            }
        ];
        return $macros;
    }
}
