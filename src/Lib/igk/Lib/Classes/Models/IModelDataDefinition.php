<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IModelDataDefinition.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Models;

interface IModelDataDefinition{
    function getDataTableDefinition() : DbModelDefinitionInfo;
}