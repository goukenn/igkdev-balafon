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
    * Constant: date time.
    * @var mixed
    */
    const DateTime = 'datetime';
    /**
    * Constant: date.
    * @var mixed
    */
    const Date = 'date';
    /**
    * Constant: time.
    * @var mixed
    */
    const Time = 'time';
    /**
    * Constant: json.
    * @var mixed
    */
    const Json = 'json';
    /**
    * Constant: int.
    * @var mixed
    */
    const Int = 'int';
    /**
    * Constant: float.
    * @var mixed
    */
    const Float = 'float';
    /**
    * Constant: double.
    * @var mixed
    */
    const Double = 'double';
    /**
    * Constant: blob.
    * @var mixed
    */
    const Blob = 'blob';
    /**
    * Constant: timestamp.
    * @var mixed
    */
    const Timestamp = 'timestamp';
    /**
    * Constant: var char.
    * @var mixed
    */
    const VarChar = 'varchar';
    /**
    * Constant: enum.
    * @var mixed
    */
    const Enum = 'enum';
    /**
    * Constant: text.
    * @var mixed
    */
    const Text = 'text';
    /**
    * Constant: long text.
    * @var mixed
    */
    const LongText = 'longtext';
    /**
    * Constant: medium text.
    * @var mixed
    */
    const MediumText = 'mediumtext';
    /**
    * Constant: char.
    * @var mixed
    */
    const Char = 'char';
    /**
    * Constant: binary.
    * @var mixed
    */
    const Binary = 'binary';
    /**
    * Constant: var binary.
    * @var mixed
    */
    const VarBinary = 'varbinary';
    /**
    * Constant: medium blob.
    * @var mixed
    */
    const MediumBlob = 'mediumblob';
    /**
    * Constant: small int.
    * @var mixed
    */
    const SmallInt = 'smallint';
    /**
    * Constant: long blob.
    * @var mixed
    */
    const LongBlob = 'longblob';
    /**
    * Constant: big int.
    * @var mixed
    */
    const BigInt = 'bigint';
    /**
    * Constant: set.
    * @var mixed
    */
    const Set= 'set';
}