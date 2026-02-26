<?php
// @author: C.A.D. BONDJE DOUE
// @file: DomCreatorNodeService.php
// @date: 20240929 13:52:53
namespace IGK\System\Html\Dom;
use IGK\System\ServicesBase;
/**
* 
* @package IGK\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
abstract class DomCreatorNodeService extends ServicesBase{

    /**
    * Creates Node.
    * @param string $name
    * @param mixed ...$args
    */
    abstract function createNode(string $name, ...$args);
}