<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbConstants.php
// @date: 20230116 13:58:25
namespace IGK\Database;

/**
* auto generate doc.
* @package IGK\Database
*/
abstract class DbConstants{
    /**
    * Constant: callback opts.
    * @var mixed
    */
    const CALLBACK_OPTS = '@callback';
    /**
    * Constant: varchar default length.
    * @var mixed
    */
    const VARCHAR_DEFAULT_LENGTH=191;
    /**
    * Constant: url max length.
    * @var mixed
    */
    const URL_MAX_LENGTH = 255;
    /**
    * Constant: count all columns.
    * @var mixed
    */
    const COUNT_ALL_COLUMNS = 'Count(*)';
    /**
    * Constant: prefix key.
    * @var mixed
    */
    const PREFIX_KEY = '%prefix%';
}