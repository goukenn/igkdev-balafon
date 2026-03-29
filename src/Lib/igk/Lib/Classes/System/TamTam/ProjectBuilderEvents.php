<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilderEvents.php
// @date: 20230309 20:59:31
namespace IGK\System\TamTam;
/**
* building Project Hook
* @package IGK\System\TamTam
*/
abstract class ProjectBuilderEvents{
    /**
    * Constant: before build.
    * @var mixed
    */
    const BEFORE_BUILD = 'BEFORE_GRAPH_BUILD';
    /**
    * Constant: build.
    * @var mixed
    */
    const BUILD = 'GRAPH_BUILD';
    /**
    * Constant: after build.
    * @var mixed
    */
    const AFTER_BUILD = 'AFTER_GRAPHBUILD';
}