<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Loader.php
// @date: 20220829 13:55:14
// @desc: 
namespace IGK\System\Composer;

use IGK\System\Console\Logger;

/**
 * composer autoloader helper
 * @package IGK\System\Composer
 */
class Loader
{
    private $package_file;
    private $init;
    private $to_merge;

    /**
     * register misssing classes
     * @var array
     */
    var $registerMissings = [];

    public function register(string $path)
    {
        $this->package_file = $path;
        spl_autoload_register([$this, "_autoload"]);
    }
    private function _autoload($f)
    {

        if ($this->init) {
            return;
        }
        $this->init = ["class" => $f, "at" => igk_sys_request_time()];
        \spl_autoload_unregister([$this, __FUNCTION__]);

        $bck = spl_autoload_functions();
        // clean all 
        array_map(\spl_autoload_unregister::class, $bck);
        // $gfc = spl_autoload_functions();
        require_once($this->package_file);
        $nfc = spl_autoload_functions();
        $found = 0;
        $arg = [$f];
        foreach ($nfc as $a) {
            if (call_user_func_array($a, $arg)) {
                $found = 1;
                break;
            }
        }
        $this->to_merge = array_filter(array_merge($bck, $nfc, [[$this, "_final"]]));
        array_map(\spl_autoload_unregister::class, $nfc);
        array_map(\spl_autoload_register::class, $this->to_merge);
        return $found;
    }
    public function _final($f)
    {
        // missing in core definitions - possibility to be handle by compooser package
        if (!isset($this->registerMissings[$f])) {
            $this->registerMissings[$f] = $f;
            if (igk_environment()->isDev()) {
                Logger::danger("missing class : " . $f);
            }
        }
    }
}
