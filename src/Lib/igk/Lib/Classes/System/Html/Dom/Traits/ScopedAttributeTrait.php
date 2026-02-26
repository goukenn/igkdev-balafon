<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ScopedAttributeTrait.php
// @date: 20220803 13:48:56
// @desc: 
namespace  IGK\System\Html\Dom\Traits;

/**
* Trait providing scoped attribute functionality.
* @package IGK\System\Html\Dom\Traits
*/
trait ScopedAttributeTrait{

    /**
    * Returns Scoped.
    * @return bool
    */
    public function getScoped():bool{
        return $this->isActive("scoped");
    }

    /**
    * Sets Scoped.
    * @param bool $scope
    */
    public function setScoped(bool $scope){
        $scope ? $this->activate("scoped"): $this->deactivate("scoped");
        return $this;
    }
}