<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemasConstants.php
// @date: 20221116 23:40:56
namespace IGK\Database;

/**
* auto generate doc.
* @package IGK\Database
*/
class DbSchemasConstants{
    // + | --------------------------------------------------------------------
    // + | operation type
    // + |
    /**
    * Constant: migrate.
    * @var mixed
    */
    const Migrate = 'migrate';
    /**
    * Constant: downgrade.
    * @var mixed
    */
    const Downgrade = 'downgrade';
    /**
    * Constant: none.
    * @var mixed
    */
    const None = 'no-operation';
    /**
    * Constant: op drop table.
    * @var mixed
    */
    const OP_DROP_TABLE ='deletetable';
    /**
    * Constant: op create table.
    * @var mixed
    */
    const OP_CREATE_TABLE ='createtable';
    /**
    * Constant: op rm column.
    * @var mixed
    */
    const OP_RM_COLUMN ='removecolumn';
    /**
    * Constant: op add column.
    * @var mixed
    */
    const OP_ADD_COLUMN ='addcolumn';
    /**
    * Constant: op change column.
    * @var mixed
    */
    const OP_CHANGE_COLUMN = 'changecolumn';
    /**
    * Constant: op rename column.
    * @var mixed
    */
    const OP_RENAME_COLUMN = 'renamecolumn';
    /**
    * Constant: op rename table.
    * @var mixed
    */
    const OP_RENAME_TABLE = 'renametable';
}