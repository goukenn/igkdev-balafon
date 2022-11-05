<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramSchemaEntity.php
// @date: 20221104 11:38:15
namespace IGK\Database\SchemaBuilder;


///<summary></summary>
/**
* 
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramSchemaEntity{
    /**
     * 
     * @param string $id 
     * @return self 
     */
    function id(string $id):IDiagramSchemaEntity;
    function varchar(string $id):IDiagramSchemaEntity;
    function address(string $id):IDiagramSchemaEntity;
    function dateUpdate(string $id):IDiagramSchemaEntity;
    function link_guuid(string $name, string $tablename):IDiagramSchemaEntity;
    function column(string $id):IDiagramSchemaEntity;
    function text(string $id):IDiagramSchemaEntity;
    function link(string $name, string $table, ?string $column = null): IDiagramSchemaEntity;
    function int (string $name):IDiagramSchemaEntity; 
    function float (string $name):IDiagramSchemaEntity; 
    function unique(string $name): IDiagramSchemaEntity;
    function primary(string $name): IDiagramSchemaEntity;
    function setDescription(?string $description): IDiagramSchemaEntity;
}