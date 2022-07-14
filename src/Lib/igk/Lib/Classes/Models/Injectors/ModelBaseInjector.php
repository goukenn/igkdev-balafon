<?php

namespace IGK\Models\Injectors;

use IGK\Models\ModelBase;

class ModelBaseInjector{
    protected $model;

    public function __construct(?ModelBase $model=null)
    {
        $this->model = $model;
    }

    public function resolv($i){
        if (is_numeric($i)){
            return $this->model::select_row($i);
        }
    }
}
