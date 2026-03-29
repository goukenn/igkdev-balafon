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
    * Constant: make.
    * @var mixed
    */
    const MAKE = 'make';
    /**
    * Constant: database.
    * @var mixed
    */
    const DATABASE = 'db';
    /**
    * Constant: user.
    * @var mixed
    */
    const USER = 'users';
    /**
    * Constant: project.
    * @var mixed
    */
    const PROJECT = 'project';
    /**
    * Constant: modules.
    * @var mixed
    */
    const MODULES = 'modules;';
}