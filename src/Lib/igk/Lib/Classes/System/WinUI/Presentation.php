<?php
namespace IGK\System\WinUI;

/**
 * bind presentation
 */
class Presentation{
    /**
     * present by hosting in div
     * @param mixed $i 
     * @param string $class 
     * @return void 
     */
    public static function PresentClassMethodCallback($i, string $class){
        $i->div()->host($class);
    }
}