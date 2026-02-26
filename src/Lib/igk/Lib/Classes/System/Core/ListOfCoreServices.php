<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListOfCoreServices.php
// @date: 20250815 16:36:42
namespace IGK\System\Core;


/**
 * 
 * @package IGK\System\Core
 * @author C.A.D. BONDJE DOUE
 */
abstract class ListOfCoreServices
{
    // + | --------------------------------------------------------------------
    // + | service name
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const PRINTER = "Printer";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MAPPING_SERVICE = "MappingService";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FORMATTER_SERVICE = 'formatters';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CORE_CODE_HIGHLIGHT = 'balafon-core-code-highlight';
}
