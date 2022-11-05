<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaBuilder.php
// @date: 20221104 11:36:13
namespace IGK\Database\SchemaBuilder;


///<summary></summary>
/**
* 
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaBuilder{
    /**
     * create or get the diagrame entity schema
     * @param string $name . table's name
     * @return mixed 
     */
    function entity(string $name, ?string $desc=null, ?string $prefix=null) : IDiagramSchemaEntity;

    function getTablePrefix():string;

    function getTableName(string $name): string;
}