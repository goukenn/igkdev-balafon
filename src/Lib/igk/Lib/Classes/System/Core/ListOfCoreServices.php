<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListOfCoreServices.php
// @date: 20250815 16:36:42
namespace IGK\System\Core;

/**
* auto generate doc.
* @package IGK\System\Core
* @author C.A.D. BONDJE DOUE
*/
abstract class ListOfCoreServices
{
    // + | --------------------------------------------------------------------
    // + | service name
    // + |

    /**
    * Constant: printer.
    * @var mixed
    */
    const PRINTER = "Printer";

    /**
    * Constant: mapping service.
    * @var mixed
    */
    const MAPPING_SERVICE = "MappingService";

    /**
    * Constant: formatter service.
    * @var mixed
    */
    const FORMATTER_SERVICE = 'formatters';

    /**
    * Constant: core code highlight.
    * @var mixed
    */
    const CORE_CODE_HIGHLIGHT = 'balafon-core-code-highlight';
}
