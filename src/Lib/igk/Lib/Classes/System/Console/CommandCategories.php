<?php
// @author: C.A.D. BONDJE DOUE
// @file: CommandCategories.php
// @date: 20240923 09:28:11
namespace IGK\System\Console;
/**
* system primary category command
* @package IGK\System\Console
* @author C.A.D. BONDJE DOUE
*/
abstract class CommandCategories{

    /**
    * auto generate doc.
    * @var mixed
    */
    const MAKE = 'make';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DATABASE = 'db';

    /**
    * auto generate doc.
    * @var mixed
    */
    const USER = 'users';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PROJECT = 'project';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MODULES = 'modules;';
}