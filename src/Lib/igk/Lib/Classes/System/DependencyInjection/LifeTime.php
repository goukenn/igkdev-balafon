<?php
// @author: C.A.D. BONDJE DOUE
// @file: LifeTime.php
// @date: 20251222 18:32:31
namespace IGK\System\DependencyInjection;


/**
* service lifetime 
* @package IGK\System\DependencyInjection
* @author C.A.D. BONDJE DOUE
*/
abstract class LifeTime{

    /**
    * auto generate doc.
    * @var mixed
    */
    const SINGLETON = 'singleton';

    /**
    * auto generate doc.
    * @var mixed
    */
    const TRANSIENT = 'transient';
}