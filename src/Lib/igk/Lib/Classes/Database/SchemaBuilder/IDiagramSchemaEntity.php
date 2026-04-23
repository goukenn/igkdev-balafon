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
/**
* auto generate doc.
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaEntity
{
    /**
    * auto generate doc.
    * @param string $id
    * @return self
    */
    function id(string $id): IDiagramSchemaEntity;
    /**
    * Varchar.
    * @param string $id
    * @param int $length
    * @return IDiagramSchemaEntity
    */
    function varchar(string $id, int $length= DbConstants::VARCHAR_DEFAULT_LENGTH): IDiagramSchemaEntity;
    /**
    * Address.
    * @param string $id
    * @return IDiagramSchemaEntity
    */
    function address(string $id): IDiagramSchemaEntity;
    /**
    * Date update.
    * @param null|string $prefix
    * @return IDiagramSchemaEntity
    */
    function dateUpdate(?string $prefix = null): IDiagramSchemaEntity;
    /**
    * Locale.
    * @param string $id
    * @param int $length
    * @return IDiagramSchemaEntity
    */
    function locale(string $id, int $length=DbConstants::VARCHAR_DEFAULT_LENGTH): IDiagramSchemaEntity;
    /**
    * auto generate doc.
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
    * Column.
    * @param string $id
    * @param mixed $type
    * @param mixed $length
    * @return IDiagramSchemaEntity
    */
    function column(string $id, $type = 'Int', $length = 9): IDiagramSchemaEntity;
    /**
    * Column varchar.
    * @param string $id
    * @param int $length
    * @param null|array $options
    * @return IDiagramSchemaEntity
    */
    function column_varchar(string $id, int $length,?array $options = null): IDiagramSchemaEntity;
    /**
    * Text.
    * @param string $id
    * @return IDiagramSchemaEntity
    */
    function text(string $id): IDiagramSchemaEntity;
    /**
    * Email.
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
    * Link.
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
    * Int.
    * @param string $name
    * @param int $length
    * @return IDiagramSchemaEntity
    */
    function int(string $name, int $length = 9): IDiagramSchemaEntity;
    /**
    * Float.
    * @param string $name
    * @return IDiagramSchemaEntity
    */
    function float(string $name): IDiagramSchemaEntity;
    /**
    * Unique.
    * @param string $name
    * @return IDiagramSchemaEntity
    */
    function unique(string $name): IDiagramSchemaEntity;
    /**
    * Primary.
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