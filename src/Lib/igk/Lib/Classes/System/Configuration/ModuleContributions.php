<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleContributions.php
// @date: 20251118 13:04:20
namespace IGK\System\Configuration;
/**
* enumeration of contributions
* @package IGK\System\Configuration
* @author C.A.D. BONDJE DOUE
*/
abstract class ModuleContributions{
    /**
    * Constant: database.
    * @var mixed
    */
    const database = 'database';
    /**
    * Constant: theme.
    * @var mixed
    */
    const theme = 'theme';
    /**
    * Constant: layout.
    * @var mixed
    */
    const layout = 'layout';
    /**
    * Constant: dashboard.
    * @var mixed
    */
    const dashboard = 'dashboard';
    /**
    * Constant: dom.
    * @var mixed
    */
    const dom ='dom';
}