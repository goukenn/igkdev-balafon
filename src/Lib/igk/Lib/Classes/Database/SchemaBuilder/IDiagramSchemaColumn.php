<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaColumn.php
// @date: 20231222 14:34:12
namespace IGK\Database\SchemaBuilder;
/**
* 
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaColumn{

    /**
    * auto generate doc.
    * @param null|string $comment
    * @return IDiagramSchemaColumn
    */
    function comment(?string $comment): IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @param string $type
    * @return IDiagramSchemaColumn
    */
    function type(string $type):IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @param null|int $size
    * @return IDiagramSchemaColumn
    */
    function size(?int $size):IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    function id():IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    function primary():IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    function autoincrement():IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @param mixed $defaultValue
    * @return IDiagramSchemaColumn
    */
    function default($defaultValue):IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @param int $length
    * @return IDiagramSchemaColumn
    */
    function varchar(int $length): IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    function unique():IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    function index():IDiagramSchemaColumn;

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    function notnull():IDiagramSchemaColumn;
}