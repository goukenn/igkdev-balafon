<?php
// @author: C.A.D. BONDJE DOUE
// @file: DiagramSchemaColumn.php
// @date: 20231222 14:36:31
namespace IGK\Database\SchemaBuilder;

use IGK\Database\DbColumnInfo;

///<summary></summary>
/**
* diagram schema column
* @package IGK\Database\SchemaBuilder
*/
class DiagramSchemaColumn implements IDiagramSchemaColumn{
    private $db_columnInfo;

    public function unique(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsUnique = true;
        return $this;
    }
    public function notnull(): IDiagramSchemaColumn {
        $this->db_columnInfo->clNotNull = true;
        return $this;
    }
    public function index(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsIndex = true;
        return $this;
    }
    public function varchar(int $length): IDiagramSchemaColumn {
        $this->db_columnInfo->clType = 'VarChar';
        $this->db_columnInfo->clTypeLength = $length;
        return $this;
    }
    public function getColumnInfo(){
        return $this->db_columnInfo;
    }
    public function __construct(string $name)
    {
        $this->db_columnInfo = new DbColumnInfo();
        $this->db_columnInfo->clName = $name; 

    }
    public function comment(?string $comment): IDiagramSchemaColumn { 
        $this->db_columnInfo->clComment = $comment;
        return $this;
    }

    public function type(string $type): IDiagramSchemaColumn {
        $this->db_columnInfo->clType = $type; 
        return $this;
    }

    public function size(?int $size): IDiagramSchemaColumn {return $this;}

    public function id(): IDiagramSchemaColumn {
        $this->db_columnInfo->clNotNull =false;
        $this->db_columnInfo->clIsPrimary =true;
        $this->db_columnInfo->clIsIndex = true; 
        return $this;}

    public function primary(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsPrimary =true;
        return $this;}

    public function autoincrement(): IDiagramSchemaColumn {
        $this->db_columnInfo->clAutoIncrement = true;
        return $this;
    }

    public function default($defaultValue): IDiagramSchemaColumn {
        $this->db_columnInfo->clDefault = $defaultValue; 
        return $this;}

}