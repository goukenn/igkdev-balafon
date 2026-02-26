<?php
// @author: C.A.D. BONDJE DOUE
// @file: AccessibilityTrait.php
// @date: 20230315 09:24:56
namespace IGK\System\Html\Dom\Traits;
/**
* 
* @package IGK\System\Html\Dom\Trait
*/
trait AccessibilityTrait{

    /**
    * Aria controls.
    * @param null|string $value
    */
    public function ariaControls(?string $value){
        $this['aria-controls'] = $value;
        return $this;
    }

    /**
    * Aria labelledby.
    * @param null|string $value
    */
    public function ariaLabelledby(?string $value){
        $this['aria-labelledby'] = $value;
        return $this;
    }
    /**
     * set aria label
     * @param null|string $value 
     * @return $this 
     */

    public function ariaLabel(?string $value){
        $this['aria-label'] = $value;
        return $this;
    }
}