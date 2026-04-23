<?php
// @author: C.A.D. BONDJE DOUE
// @file: DiagramSchemaColumn.php
// @date: 20231222 14:36:31
namespace IGK\Database\SchemaBuilder;
use IGK\Database\DbColumnInfo;

/**
* diagram schema column
* @package IGK\Database\SchemaBuilder
*/
class DiagramSchemaColumn implements IDiagramSchemaColumn{
    /**
    * Property: db column info.
    * @var mixed
    */
    private $db_columnInfo;
    /**
    * Unique.
    * @return IDiagramSchemaColumn
    */
    public function unique(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsUnique = true;
        return $this;
    }
    /**
    * Notnull.
    * @return IDiagramSchemaColumn
    */
    public function notnull(): IDiagramSchemaColumn {
        $this->db_columnInfo->clNotNull = true;
        return $this;
    }
    /**
    * Index.
    * @return IDiagramSchemaColumn
    */
    public function index(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsIndex = true;
        return $this;
    }
    /**
    * Varchar.
    * @param int $length
    * @return IDiagramSchemaColumn
    */
    public function varchar(int $length): IDiagramSchemaColumn {
        $this->db_columnInfo->clType = 'VarChar';
        $this->db_columnInfo->clTypeLength = $length;
        return $this;
    }
    /**
    * Returns Column Info.
    */
    public function getColumnInfo(){
        return $this->db_columnInfo;
    }
    /**
    * .ctr
    * @param string $name
    */
    public function __construct(string $name)
    {
        $this->db_columnInfo = new DbColumnInfo();
        $this->db_columnInfo->clName = $name; 
    }
    /**
    * Comment.
    * @param null|string $comment
    * @return IDiagramSchemaColumn
    */
    public function comment(?string $comment): IDiagramSchemaColumn { 
        $this->db_columnInfo->clComment = $comment;
        return $this;
    }
    /**
    * Type.
    * @param string $type
    * @return IDiagramSchemaColumn
    */
    public function type(string $type): IDiagramSchemaColumn {
        $this->db_columnInfo->clType = $type; 
        return $this;
    }
    /**
    * Size.
    * @param null|int $size
    * @return IDiagramSchemaColumn
    */
    public function size(?int $size): IDiagramSchemaColumn {return $this;}
    /**
    * Id.
    * @return IDiagramSchemaColumn
    */
    public function id(): IDiagramSchemaColumn {
        $this->db_columnInfo->clNotNull =false;
        $this->db_columnInfo->clIsPrimary =true;
        $this->db_columnInfo->clIsIndex = true; 
        return $this;}
    /**
    * Primary.
    * @return IDiagramSchemaColumn
    */
    public function primary(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsPrimary =true;
        return $this;}
    /**
    * Autoincrement.
    * @return IDiagramSchemaColumn
    */
    public function autoincrement(): IDiagramSchemaColumn {
        $this->db_columnInfo->clAutoIncrement = true;
        return $this;
    }
    /**
    * Default.
    * @param mixed $defaultValue
    * @return IDiagramSchemaColumn
    */
    public function default($defaultValue): IDiagramSchemaColumn {
        $this->db_columnInfo->clDefault = $defaultValue; 
        return $this;}
}