<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilderEvents.php
// @date: 20230309 20:59:31
namespace IGK\System\TamTam;


///<summary></summary>
/**
* 
* @package IGK\System\TamTam
*/
abstract class ProjectBuilderEvents{
    const BEFORE_BUILD = 'BEFORE_GRAPH_BUILD';
    const BUILD = 'GRAPH_BUILD';
    const AFTER_BUILD = 'AFTER_GRAPHBUILD';
}