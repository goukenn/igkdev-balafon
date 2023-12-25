<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDiagramBuilder.php
// @date: 20221104 10:06:34
namespace IGK\Database\SchemaBuilder;

 

///<summary></summary>
/**
* 
* @package IGK\Database\SchemaBuilder
*/
interface IDiagramBuilder{
    /**
     * schema upgrade schema builder. call when need to upgrade the schema 
     */
    function upgrade(IDiagramSchemaBuilder $builder); 
    /**
     * downgrade schema builder
     * @param IDiagramSchemaBuilder $builder . call when need to downgrade the schema
     * @return mixed 
     */
    function downgrade(IDiagramSchemaBuilder $builder); 
}