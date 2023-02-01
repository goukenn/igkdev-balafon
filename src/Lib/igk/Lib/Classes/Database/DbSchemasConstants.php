<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemasConstants.php
// @date: 20221116 23:40:56
namespace IGK\Database;


///<summary></summary>
/**
* 
* @package IGK\Database
*/
class DbSchemasConstants{
    // + | --------------------------------------------------------------------
    // + | operation type
    // + |
    
    const Migrate = 'migrate';
    const Downgrade = 'downgrade';
    const None = 'no-operation';

    const OP_DROP_TABLE ='deletetable';
    const OP_CREATE_TABLE ='createtable';
    const OP_RM_COLUMN ='removecolumn';
    const OP_ADD_COLUMN ='addcolumn';
    const OP_CHANGE_COLUMN = 'changecolumn';
    const OP_RENAME_COLUMN = 'renamecolumn';
    const OP_RENAME_TABLE = 'renametable';
}