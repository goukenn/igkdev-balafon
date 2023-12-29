<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaBuilder.php
// @date: 20221104 11:36:13
namespace IGK\Database\SchemaBuilder;

use IGK\Database\SchemaBuilder\IDiagramSchemaEntity;

///<summary>Schema builder blueprint</summary>
/**
* Schema builder blueprint
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaBuilder{
    /**
     * create or get the diagrame entity schema
     * @param string $name table's name
     * @return IGK\Database\SchemaBuilder\IDiagramSchemaEntity|DiagramEntity 
     */
    function entity(string $name, ?string $desc=null, ?string $prefix=null);

    /**
     * retrieve configured table prefix
     * @return string 
     */
    function getTablePrefix():string;

    /**
     * get table prefix 
     * @param string $name 
     * @return string 
     */
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
     * add table index 
     * @param string $table 
     * @param mixed $column array<string> | string
     * @return mixed 
     */
    function addIndex(string $table, $column);

    /**
     * drop table index 
     * @param string $table 
     * @param mixed $column array<string>|string list of colonne 
     * @return mixed 
     */
    function dropIndex(string $table,  $column);

    /**
     * remove column from schema
     * @param string $tabble 
     * @param mixed $column 
     * @return mixed 
     */
    function dropColumn(string $tabble, string $column):void;

    function description(?string $desc ): IDiagramSchemaBuilder;
}