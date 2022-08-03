<?php
// @author: C.A.D. BONDJE DOUE
// @filename: default.controller.php
// @date: 20220803 13:48:59
// @desc: 

/**
 * default controller setting
 */
return array(
    "clDataAdapterName" => IGK_CSV_DATAADAPTER,
    "clDataSchema" => false,
    "clDisplayName" => null,
    "clRegisterName" => null,
    "clParentCtrl" => null,
    "clTargetNodeIndex" => 0,
    "clVisiblePages" => "*",
    "clDescription" => null,
    /**
     * auto cache view. when caching in controller is active. view will
     * be pre-evaluate
     */
    "no_auto_cache_view"=>0
);