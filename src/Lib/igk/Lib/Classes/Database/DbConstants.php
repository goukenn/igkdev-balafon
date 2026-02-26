<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbConstants.php
// @date: 20230116 13:58:25
namespace IGK\Database;
/**
* 
* @package IGK\Database
*/
abstract class DbConstants{

    /**
    * auto generate doc.
    * @var mixed
    */
    const CALLBACK_OPTS = '@callback';

    /**
    * auto generate doc.
    * @var mixed
    */
    const VARCHAR_DEFAULT_LENGTH=191;

    /**
    * auto generate doc.
    * @var mixed
    */
    const URL_MAX_LENGTH = 255;

    /**
    * auto generate doc.
    * @var mixed
    */
    const COUNT_ALL_COLUMNS = 'Count(*)';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PREFIX_KEY = '%prefix%';
}