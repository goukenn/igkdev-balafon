<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ScopedAttributeTrait.php
// @date: 20220803 13:48:56
// @desc: 

namespace  IGK\System\Html\Dom\Traits;

trait ScopedAttributeTrait{
    public function getScoped():bool{
        return $this->isActive("scoped");
    }
    public function setScoped(bool $scope){
        $scope ? $this->activate("scoped"): $this->deactivate("scoped");
        return $this;
    }
}