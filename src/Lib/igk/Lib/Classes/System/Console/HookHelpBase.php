<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookHelpBase.php
// @date: 20260323 20:57:59
namespace IGK\System\Console;
/**
* auto generate doc.
* @package IGK\System\Console
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console
*/
abstract class HookHelpBase{
    abstract function info(): ?array;
    /**
    * auto generate doc.
    * @param array $args
    * @return
    */
    abstract function filter(array $args);
}