<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DataAdapterTypes.php
// @date: 20221203 17:31:11
// @desc: type constants
namespace IGK\System\Database\MySQL;
/**
 * mysql suppport data type - 
 * @package 
 */
abstract class DataAdapterTypes{

    /**
    * auto generate doc.
    * @var mixed
    */
    const DateTime = 'datetime';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Date = 'date';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Time = 'time';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Json = 'json';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Int = 'int';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Float = 'float';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Double = 'double';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Blob = 'blob';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Timestamp = 'timestamp';

    /**
    * auto generate doc.
    * @var mixed
    */
    const VarChar = 'varchar';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Enum = 'enum';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Text = 'text';

    /**
    * auto generate doc.
    * @var mixed
    */
    const LongText = 'longtext';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MediumText = 'mediumtext';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Char = 'char';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Binary = 'binary';

    /**
    * auto generate doc.
    * @var mixed
    */
    const VarBinary = 'varbinary';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MediumBlob = 'mediumblob';

    /**
    * auto generate doc.
    * @var mixed
    */
    const SmallInt = 'smallint';

    /**
    * auto generate doc.
    * @var mixed
    */
    const LongBlob = 'longblob';

    /**
    * auto generate doc.
    * @var mixed
    */
    const BigInt = 'bigint';

    /**
    * auto generate doc.
    * @var mixed
    */
    const Set= 'set';
}