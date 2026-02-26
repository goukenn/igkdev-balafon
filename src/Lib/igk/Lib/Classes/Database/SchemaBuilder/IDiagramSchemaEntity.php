<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaEntity.php
// @date: 20221104 11:38:15
namespace IGK\Database\SchemaBuilder;
use IGK\Database\DbConstants;
/**
 * 
 * @package IGK\Database\SchemaBuilder
 */
interface IDiagramSchemaEntity
{
    /**
     * 
     * @param string $id 
     * @return self 
     */
    function id(string $id): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $id
    * @param int $length
    * @return IDiagramSchemaEntity
    */
    function varchar(string $id, int $length= DbConstants::VARCHAR_DEFAULT_LENGTH): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $id
    * @return IDiagramSchemaEntity
    */
    function address(string $id): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param null|string $prefix
    * @return IDiagramSchemaEntity
    */
    function dateUpdate(?string $prefix = null): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $id
    * @param int $length
    * @return IDiagramSchemaEntity
    */
    function locale(string $id, int $length=DbConstants::VARCHAR_DEFAULT_LENGTH): IDiagramSchemaEntity;
    /**
     * 
     * @param string $name 
     * @param string $table_name 
     * @param string $linkColumn 
     * @param mixed $linkName 
     * @param bool $notnull 
     * @param bool $unique 
     * @param null|int $uniqueColumn 
     * @param string $inputtype 
     * @param int $default 
     * @param mixed $description 
     * @return self 
     */

    function link_guuid(
        string $name,
        string $table_name,
        $linkColumn = 'clId',
        $linkName = null,
        $notnull = false,
        bool $unique = false,
        ?int $uniqueColumn = null,
        $inputtype = "",
        $default = 0,
        $description = null
    ): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $id
    * @param mixed $type
    * @param mixed $length
    * @return IDiagramSchemaEntity
    */
    function column(string $id, $type = 'Int', $length = 9): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $id
    * @param int $length
    * @param null|array $options
    * @return IDiagramSchemaEntity
    */
    function column_varchar(string $id, int $length,?array $options = null): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $id
    * @return IDiagramSchemaEntity
    */
    function text(string $id): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $length
    * @param mixed $notnull
    * @param mixed $inputtype
    * @param mixed $default
    * @param null|mixed $description
    * @return IDiagramSchemaEntity
    */
    function email($name = "Email", $length = 30, $notnull = false, $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $name
    * @param string $table
    * @param null|string $column
    * @param null|mixed $linkName
    * @param mixed $notnull
    * @param mixed $inputtype
    * @param mixed $default
    * @param null|mixed $description
    * @return IDiagramSchemaEntity
    */
    function link(
        string $name,
        string $table,
        ?string $column = null,
        $linkName = null,
        $notnull = false,
        $inputtype = "",
        $default = 0,
        $description = null
    ): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $name
    * @param int $length
    * @return IDiagramSchemaEntity
    */
    function int(string $name, int $length = 9): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $name
    * @return IDiagramSchemaEntity
    */
    function float(string $name): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $name
    * @return IDiagramSchemaEntity
    */
    function unique(string $name): IDiagramSchemaEntity;

    /**
    * auto generate doc.
    * @param string $name
    * @return IDiagramSchemaEntity
    */
    function primary(string $name): IDiagramSchemaEntity;
    /**
     * set entity description
     * @param null|string $description 
     * @return IDiagramSchemaEntity 
     */

    function setDescription(?string $description): IDiagramSchemaEntity;
    /**
     * set last column or entity description
     * @param null|string $description 
     * @return IDiagramSchemaEntity 
     */

    function description(?string $description): IDiagramSchemaEntity;
}