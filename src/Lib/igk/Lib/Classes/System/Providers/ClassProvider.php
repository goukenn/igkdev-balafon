<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ClassProvider.php
// @date: 20220824 13:05:32
// @desc: will help to create or override some spécific class by name
namespace IGK\System\Providers;

use IGKException;

/**
 * class name resolution providers
 * @package IGK\System\Providers
 */
class ClassProvider{
    private $m_classes;

    /**
     * 
     * @param string $name 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetClass(string $name ){
        $provider = new self;
        return igk_getv($provider->m_classes, $name);
    }
    /**
     * init class provider
     * @return array 
     */
    protected function initProvider() : array{
        return [
            "controller::info"=>\IGK\System\Controllers\ControllerInfo::class,
            "module:manager"=>\IGK\System\Modules\ModuleManager::class,
            "composer:loader"=>\IGK\System\Composer\Loader::class,
        ];
    }
    public function __construct(){
        $this->m_classes = $this->initProvider();
    }

}
