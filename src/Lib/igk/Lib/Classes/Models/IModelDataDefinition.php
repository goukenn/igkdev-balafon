<?php

namespace IGK\Models;

interface IModelDataDefinition{
    function getDataTableDefinition() : DbModelDefinitionInfo;
}