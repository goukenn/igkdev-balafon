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
    const DateTime = 'datetime';
    const Date = 'date';
    const Time = 'time';
    const Json = 'json';
    const Int = 'int';
    const Float = 'float';
    const Double = 'double';
    const Blob = 'blob';
    const Timestamp = 'timestamp';
    const VarChar = 'varchar';
    const Enum = 'enum';
    const Text = 'text';
    const LongText = 'longtext';
    const MediumText = 'mediumtext';
    const Char = 'char';
    const Binary = 'binary';
    const VarBinary = 'varbinary';
    const MediumBlob = 'mediumblob';
    const SmallInt = 'smallint';
    const LongBlob = 'longblob';
    const BigInt = 'bigint';
    const Set= 'set';
}
