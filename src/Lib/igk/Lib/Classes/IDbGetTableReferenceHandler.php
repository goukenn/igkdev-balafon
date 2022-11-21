<?php

interface IDbGetTableReferenceHandler{
    public function getDataTablesReference(& $table);
    public function resolvTableDefinition(string $table);
}