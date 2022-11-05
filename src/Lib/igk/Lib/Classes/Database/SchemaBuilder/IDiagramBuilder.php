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
    function up(IDiagramSchemaBuilder $builder);
    function down(IDiagramSchemaBuilder $builder);
}