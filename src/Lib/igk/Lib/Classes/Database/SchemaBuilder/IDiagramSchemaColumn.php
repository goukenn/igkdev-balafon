<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaColumn.php
// @date: 20231222 14:34:12
namespace IGK\Database\SchemaBuilder;


///<summary></summary>
/**
* 
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaColumn{
    function comment(?string $comment): IDiagramSchemaColumn;
    function type(string $type):IDiagramSchemaColumn;
    function size(?int $size):IDiagramSchemaColumn;
    function id():IDiagramSchemaColumn;
    function primary():IDiagramSchemaColumn;
    function autoincrement():IDiagramSchemaColumn;
    function default($defaultValue):IDiagramSchemaColumn;
    function varchar(int $length): IDiagramSchemaColumn;
    function unique():IDiagramSchemaColumn;
    function index():IDiagramSchemaColumn;
    function notnull():IDiagramSchemaColumn;
}