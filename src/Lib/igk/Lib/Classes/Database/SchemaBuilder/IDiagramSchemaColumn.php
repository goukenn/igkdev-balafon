<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaColumn.php
// @date: 20231222 14:34:12
namespace IGK\Database\SchemaBuilder;

/**
* auto generate doc.
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaColumn{

    /**
    * Comment.
    * @param null|string $comment
    * @return IDiagramSchemaColumn
    */
    function comment(?string $comment): IDiagramSchemaColumn;

    /**
    * Type.
    * @param string $type
    * @return IDiagramSchemaColumn
    */
    function type(string $type):IDiagramSchemaColumn;

    /**
    * Size.
    * @param null|int $size
    * @return IDiagramSchemaColumn
    */
    function size(?int $size):IDiagramSchemaColumn;

    /**
    * Id.
    * @return IDiagramSchemaColumn
    */
    function id():IDiagramSchemaColumn;

    /**
    * Primary.
    * @return IDiagramSchemaColumn
    */
    function primary():IDiagramSchemaColumn;

    /**
    * Autoincrement.
    * @return IDiagramSchemaColumn
    */
    function autoincrement():IDiagramSchemaColumn;

    /**
    * Default.
    * @param mixed $defaultValue
    * @return IDiagramSchemaColumn
    */
    function default($defaultValue):IDiagramSchemaColumn;

    /**
    * Varchar.
    * @param int $length
    * @return IDiagramSchemaColumn
    */
    function varchar(int $length): IDiagramSchemaColumn;

    /**
    * Unique.
    * @return IDiagramSchemaColumn
    */
    function unique():IDiagramSchemaColumn;

    /**
    * Index.
    * @return IDiagramSchemaColumn
    */
    function index():IDiagramSchemaColumn;

    /**
    * Notnull.
    * @return IDiagramSchemaColumn
    */
    function notnull():IDiagramSchemaColumn;
}