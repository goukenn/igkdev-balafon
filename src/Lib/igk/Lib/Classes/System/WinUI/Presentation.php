<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Presentation.php
// @date: 20220803 13:48:55
// @desc: 

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