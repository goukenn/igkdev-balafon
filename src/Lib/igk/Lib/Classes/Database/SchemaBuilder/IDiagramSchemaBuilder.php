<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaBuilder.php
// @date: 20221104 11:36:13
namespace IGK\Database\SchemaBuilder;

use IGK\Database\SchemaBuilder\IDiagramSchemaEntity;

///<summary></summary>
/**
* 
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaBuilder{
    /**
     * create or get the diagrame entity schema
     * @param string $name . table's name
     * @return IGK\Database\SchemaBuilder\IDiagramSchemaEntity|DiagramEntity 
     */
    function entity(string $name, ?string $desc=null, ?string $prefix=null);

    function getTablePrefix():string;

    function getPrefixTable(string $name): string;

    /**
     * add drop entity to schema builder 
     * @return mixed 
     */
    function dropEntity(string $name): void;

    /**
     * add migration column info 
     * @param string $table 
     * @param string $name 
     * @return IDiagramSchemaColumn 
     */
    function addColumn(string $table, string $name):IDiagramSchemaColumn;

    /**
     * 
     * @param string $table 
     * @param mixed $column 
     * @return mixed 
     */
    function addIndex(string $table, $column);

    /**
     * 
     * @param string $table 
     * @param mixed $column 
     * @return mixed 
     */
    function dropIndex(string $table,  $column);
}