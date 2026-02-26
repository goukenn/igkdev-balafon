<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemasConstants.php
// @date: 20221116 23:40:56
namespace IGK\Database;
/**
* 
* @package IGK\Database
*/
class DbSchemasConstants{
    // + | --------------------------------------------------------------------
    // + | operation type
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const Migrate = 'migrate';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Downgrade = 'downgrade';

    /**
    * auto generate doc.
    * @var mixed
    */
    const None = 'no-operation';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OP_DROP_TABLE ='deletetable';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OP_CREATE_TABLE ='createtable';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OP_RM_COLUMN ='removecolumn';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OP_ADD_COLUMN ='addcolumn';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OP_CHANGE_COLUMN = 'changecolumn';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OP_RENAME_COLUMN = 'renamecolumn';

    /**
    * auto generate doc.
    * @var mixed
    */
    const OP_RENAME_TABLE = 'renametable';
}