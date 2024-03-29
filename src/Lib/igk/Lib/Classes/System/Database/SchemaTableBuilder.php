<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaTableBuilder.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Database;

use DbColumnInfo;
use IGK\Database\DbConstants;
use IGK\Database\SchemaBuilder\IDiagramSchemaEntity;
use IGK\Resources\R;
use IGKException;

/**
 * schema table builder
 * @package IGK\System\Database
 */
class SchemaTableBuilder extends SchemaBuilderHelper implements IDiagramSchemaEntity
{

    /**
     * add lang locale id
     * @param string $id 
     * @param int $length 
     * @return IDiagramSchemaEntity 
     * @throws IGKException 
     */
    public function locale(string $id, int $length = DbConstants::VARCHAR_DEFAULT_LENGTH): IDiagramSchemaEntity
    {
        foreach (R::GetSupportedLangs() as $v_lang) {
            $this->column($id . ucfirst($v_lang), 'varchar', $length);
        }
        return $this;
    }

    public function description(?string $description): IDiagramSchemaEntity
    {
        $ref = $this->getLastColumnInfo();
        if ($ref)
            $ref['clDescription'] = $description;
        else
            $this->setDescription($description);
        return $this;
    }

    public function int(string $name, int $length = 9): IDiagramSchemaEntity
    {
        $this->column($name, 'Int', $length);
        return $this;
    }

    // public function int(string $name): IDiagramSchemaEntity { return $this; }
    public function column(string $id, $type = null, $length = 9): IDiagramSchemaEntity
    {
        $this->_add_column($id, $type, $length);
        return $this;
    }

    public function id(string $id): IDiagramSchemaEntity
    {
        $this->_add_column(
            $id,
            'Int',
            0,
            null,
            true,
            true,
            true
        );
        return $this;
    }

    public function varchar(string $id, int $length = DbConstants::VARCHAR_DEFAULT_LENGTH): IDiagramSchemaEntity
    {
        $this->_add_column(
            $id,
            'VarChar',
            $length,
            null
        );
        return $this;
    }

    public function address(string $id): IDiagramSchemaEntity
    {
        return $this;
    }

    public function dateUpdate(?string $prefix = null): IDiagramSchemaEntity
    {
        return $this;
    }

    public function link_guuid(string $name, string $table_name, $linkColumn = 'clId', $linkName = null, $notnull = false, bool $unique = false, ?int $uniqueColumn = null, $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this;
    }

    public function text(string $id): IDiagramSchemaEntity
    {
        return $this;
    }

    public function email($name = "Email", $length = 30, $notnull = false, $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this;
    }

    public function link(
        string $name,
        string $table,
        ?string $column = null,
        $linkName = null,
        $notnull = false,
        $inputtype = "",
        $default = 0,
        $description = null
    ): IDiagramSchemaEntity {
        $length = 0;
        $isunique = false;
        $this->_add_column(
            $name,
            'VarChar',
            $length,
            null,
            true,
            false,
            $notnull,
            $description,
            $isunique,
        );
        return $this;
    }



    public function float(string $name): IDiagramSchemaEntity
    {
        return $this;
    }

    public function unique(string $name): IDiagramSchemaEntity
    {
        return $this;
    }

    public function primary(string $name): IDiagramSchemaEntity
    {
        return $this;
    }

    public function setDescription(?string $description): IDiagramSchemaEntity
    {
        return $this;
    }


    /**
     * represent schema table builder
     * @param mixed $node 
     * @param mixed $schema 
     * @return SchemaTableBuilder 
     */
    public static function Create($node, $schema)
    {
        $c = new static();
        $c->_output = $node;
        $c->_schema = $schema;
        return $c;
    }

    public function columnAttributes(array $attributes)
    {
        return $this->_addcolumnAttributes($attributes);
    }
    /**
     * 
     * @param string $name 
     * @param int $length 
     * @param null|array $options 
     * @return IGK\System\Database\this 
     * @throws IGKException 
     */
    public function column_varchar(string $name, int $length, ?array $options = null): static
    {
        if ($length <= 0) {
            die("length not valid");
        }
        $default = $options ? igk_getv($options, "default") : null;
        $primarykey = $options ? igk_getv($options, "primarykey") : null;
        $desc = $options ? igk_getv($options, "desc") : null;
        $is_unique = $options ? igk_getv($options, "is_unique") : null;
        $is_unique_column = $options ? igk_getv($options, "is_unique_column") : null;
        $column_member_index = $options ? igk_getv($options, "column_member_index") : null;
        return $this->_add_column(
            $name,
            "VarChar",
            $length,
            $default,
            $primarykey,
            false,
            $desc,
            $is_unique,
            $is_unique_column,
            $column_member_index
        );
    }
    /**
     * 
     * @param mixed $name 
     * @param mixed $type 
     * @param mixed $length 
     * @param mixed|null $default 
     * @param bool $primarykey 
     * @param bool $auto_increment 
     * @param string $desc 
     * @param bool $is_unique 
     * @param bool $is_unique_column 
     * @param null|int $column_member_index 
     * @return $this 
     */
    protected function _add_column(
        string $name,
        string $type,
        ?int $length,
        $default = null,
        ?bool $primarykey = false,
        ?bool $auto_increment = false,
        ?bool $not_null = false,
        ?string $desc = null,
        ?bool $is_unique = false,
        ?bool $is_unique_column = false,
        ?int $column_member_index = null
    ) {
        $this->_addcolumnAttributes([
            "clName" => $name,
            "clType" => $type ?? "Int",
            "clTypeLength" => $length,
            "clDescription" => $desc,
            "clAutoIncrement" => $auto_increment,
            "clNotNull" => $not_null,
            "clDefault" => $default,
            "clIsPrimary" => $primarykey,
            "clIsUnique" => $is_unique,
            "clIsUniqueColumnMember" => $is_unique_column,
            "clColumnMemberIndex" => $column_member_index
        ]);
        return $this;
    }
}
