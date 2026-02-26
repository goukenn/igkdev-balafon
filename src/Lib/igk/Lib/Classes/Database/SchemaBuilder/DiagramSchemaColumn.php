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
    * auto generate doc.
    * @var mixed
    */
    private $db_columnInfo;

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    public function unique(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsUnique = true;
        return $this;
    }

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    public function notnull(): IDiagramSchemaColumn {
        $this->db_columnInfo->clNotNull = true;
        return $this;
    }

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    public function index(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsIndex = true;
        return $this;
    }

    /**
    * auto generate doc.
    * @param int $length
    * @return IDiagramSchemaColumn
    */
    public function varchar(int $length): IDiagramSchemaColumn {
        $this->db_columnInfo->clType = 'VarChar';
        $this->db_columnInfo->clTypeLength = $length;
        return $this;
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @param null|string $comment
    * @return IDiagramSchemaColumn
    */
    public function comment(?string $comment): IDiagramSchemaColumn { 
        $this->db_columnInfo->clComment = $comment;
        return $this;
    }

    /**
    * auto generate doc.
    * @param string $type
    * @return IDiagramSchemaColumn
    */
    public function type(string $type): IDiagramSchemaColumn {
        $this->db_columnInfo->clType = $type; 
        return $this;
    }

    /**
    * auto generate doc.
    * @param null|int $size
    * @return IDiagramSchemaColumn
    */
    public function size(?int $size): IDiagramSchemaColumn {return $this;}

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    public function id(): IDiagramSchemaColumn {
        $this->db_columnInfo->clNotNull =false;
        $this->db_columnInfo->clIsPrimary =true;
        $this->db_columnInfo->clIsIndex = true; 
        return $this;}

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    public function primary(): IDiagramSchemaColumn {
        $this->db_columnInfo->clIsPrimary =true;
        return $this;}

    /**
    * auto generate doc.
    * @return IDiagramSchemaColumn
    */
    public function autoincrement(): IDiagramSchemaColumn {
        $this->db_columnInfo->clAutoIncrement = true;
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $defaultValue
    * @return IDiagramSchemaColumn
    */
    public function default($defaultValue): IDiagramSchemaColumn {
        $this->db_columnInfo->clDefault = $defaultValue; 
        return $this;}
}