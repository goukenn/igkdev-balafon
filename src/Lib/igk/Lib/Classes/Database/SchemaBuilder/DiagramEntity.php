<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DiagramEntity.php
// @date: 20220531 16:29:44
// @desc: 

namespace IGK\Database\SchemaBuilder;

use IGK\Database\DbConstants;
use IGK\Helper\Activator;
use IGK\Resources\R;
use IGK\System\Console\Logger;
use IGKException;

/**
 * represent diagram entities
 * @package 
 */
class DiagramEntity extends DiagramPropertiesHost implements IDiagramSchemaEntity
{
    /**
     * entity name
     * @var string
     */
    private $m_name;
    /**
     * entity description 
     * @var mixed
     */
    private $m_desc;

   

    const VarChar = 'VarChar';

    public function locale(string $id, int $length = DbConstants::VARCHAR_DEFAULT_LENGTH): IDiagramSchemaEntity {
        foreach(R::GetSupportedLangs() as $lang){
            $this->addProperties([[
                'clName'=>$id.ucfirst($lang),
                'clType'=>self::VarChar,
                'clTypeLength'=>$length
            ]]);
        }
        return $this;
    }

    public function column_varchar(string $name, int $length, ?array $options = null): IDiagramSchemaEntity {
        if (is_null($options)){
            $options = [];
        }
        $this->addProperties([array_merge(
            [
                'clName'=>$name,
                'clType'=>'VarChar',
                'clTypeLength'=>$length
            ],
            $options
        )]);
 
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->m_desc;
    }
    public function setDescription(?string $description): IDiagramSchemaEntity
    {
        $this->m_desc = $description;
        return $this;
    }

    public function __construct(?string $name = null, ?string $prefix=null)
    {
        $this->m_name = $name ?? "Entity";
        $this->m_properties = [];
        $this->prefix = $prefix;
    }
    public static function __set_state(array $data)
    {
        $e = new self();
        $e->m_properties = $data["m_properties"];
        $e->m_name = $data["m_name"];
        return $e;
    }
    /**
     * add email column
     * @param mixed $prefix 
     * @param string $name 
     * @param int $length 
     * @return $this 
     */    
    public function email($name = "Email", $length=50, $notnull = false, $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([
            [
                "clName" => empty($name) ? 'Email': $name, 
                "clType" => self::VarChar,
                "clTypeLength" => $length,
                "clInputType" => "email"
            ]
        ]);
    }
    /**
     * add tel field
     * @param mixed $prefix 
     * @param int $length 
     * @return $this 
     */
    public function tel(string $prefix, string $name = "Tel", $length = 15)
    {
        return $this->addProperties([

         [
            "clName" => $prefix . $name, "clType" => self::VarChar, "clTypeLength" => $length,
            "clInputType" => "tel"
        ]]);
    }
    /**
     * add global column
     * @param string $name 
     * @param string $type 
     * @param int $length 
     * @param int $notnull 
     * @param int $isunique 
     * @param mixed $description 
     * @param mixed $inputtype 
     * @return $this 
     */
    public function column(string $name, $type = "Int", $length = 9, $notnull = 0, $isunique = 0, $description = null, $inputtype = null): IDiagramSchemaEntity
    {
        $this->m_properties[$name] = Activator::CreateNewInstance(
            DiagramEntityColumnInfo::class,
            [
                "clName" => $name, 
                "clType" => $type,
                "clTypeLength" => $length,
                "clIsUnique" => $isunique,
                "clNotNull" => $notnull,
                "clInputType" => $inputtype,
                "clDescription" => $description,
            ]
        );
        return $this;
    }
    /**
     * create guid 
     * @param string $name 
     * @param null|string $description 
     * @return $this 
     */
    public function guuid(string $name, ?string $description = null): IDiagramSchemaEntity
    {
        return $this->column($name,self::VarChar, DiagramConstants::GUID_LENGTH, 1, 1, $description);
    }
    /**
     * add auto increment id
     * @param string $name 
     * @param int $length 
     * @param int $default 
     * @param mixed $description 
     * @return $this 
     */
    public function id($name = "clId", $length = 9, $description = null): IDiagramSchemaEntity
    {

        return $this->addProperties([
            [
                "clName" => $name, "clType" => "Int", "clTypeLength" => $length,
                "clTypeLength" => "9", "clAutoIncrement" => 1, "clIsPrimary" => 1,
                "clDescription" => $description
            ]
        ]);
        // return $this;
    }
    /**
     * add primary column
     * @param string $name 
     * @param int $length 
     * @param mixed $description 
     * @return $this 
     */
    public function primary($name = "clId", $length = 9, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([[
            "clName" => $name,
            "clType" => "Int",
            "clTypeLength" => $length,
            "clTypeLength" => "9", 
            "clAutoIncrement" => 1,
            "clIsPrimary" => 1,
            "clDescription" => $description,
            "clNotNull" => 1
        ]]);
    }
    /**
     * 
     * @param string $name 
     * @param int $length 
     * @param string $type 
     * @param int $notnull 
     * @param mixed $description 
     * @return IDiagramSchemaEntity 
     * @throws IGKException 
     */
    public function unique(string $name, $length = 9, $type = self::VarChar,  $notnull = 1, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([[
            "clName" => $name,
            "clType" => $type,
            "clTypeLength" => $length,
            "clTypeLength" => $length,
            "clDescription" => $description,
            "clNotNull" => $notnull,
            "clIsUnique" => 1
        ]]);
    }
    /**
     * change description
     * @param string $description 
     * @return IDiagramSchemaEntity 
     */
    public function description (?string $description):IDiagramSchemaEntity {
        $m_last = $this->getLastProperty();
        if ($m_last)
        $m_last->clDescription = $description;
        else 
            $this->m_desc = $description;
        return $this;
    }
    public function text($name = "clId", $notnull = false, $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([[
            "clName" => $name, "clType" => "Text",
            "clDefault" => $default, "clDescription" => $description, "clInputType" => $inputtype, "clNotNull" => $notnull
        ]]);
    }
    
    /**
     * add varchar column
     * @param string $name 
     * @param int $length 
     * @param bool $notnull 
     * @param string $inputtype 
     * @param int $default 
     * @param mixed $description 
     * @return $this 
     */
    public function varchar(string $name, $length = 191, $notnull = false, $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([
            [
                "clName" => $name, "clType" => self::VarChar, "clTypeLength" => $length,
                "clDefault" => $default, "clDescription" => $description, "clInputType" => $inputtype, "clNotNull" => $notnull
            ]
        ]);
    }
    /**
     * add float type
     * @param string $name 
     * @param int $length 
     * @param bool $notnull 
     * @param string $inputtype 
     * @param int $default 
     * @param mixed $description 
     * @return $this 
     */
    public function float(string $name,  $notnull = false, $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([[
            "clName" => $name, "clType" => "Float",
            "clDefault" => $default, "clDescription" => $description, "clInputType" => $inputtype, "clNotNull" => $notnull
        ]]);
    }
    /**
     * add link to table
     * @param string $name 
     * @param string $table_name 
     * @param string $linkColumn 
     * @param mixed $linkName 
     * @param bool $notnull 
     * @param string $inputtype 
     * @param int $default 
     * @param mixed $description 
     * @return IDiagramSchemaEntity 
     * @throws IGKException 
     */
    public function link(string $name, string $table, ?string $column = null, $linkName = null, $notnull = false,
    $inputtype = "", $default = 0, $description = null
    ): IDiagramSchemaEntity
    //(string $name, $table_name, $linkColumn = 'clId', $linkName = null, $notnull = false, 
   // $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([[
            "clName" => $name, "clType" => "Int",
            "clDefault" => $default, "clDescription" => $description, "clLinkType" => $table, 
            "clLinkColumn" => $column,
            "clInputType" => $inputtype, "clNotNull" => $notnull, "clLinkConstraintName" => $linkName
        ]]);
    }
    /**
     * 
     * @param string $name 
     * @param string $table_name 
     * @param string $linkColumn 
     * @param mixed $linkName 
     * @param bool $notnull 
     * @param bool $unique 
     * @param null|int $uniqueColumn 
     * @param string $inputtype 
     * @param int $default 
     * @param mixed $description 
     * @return IDiagramSchemaEntity 
     * @throws IGKException 
     */
    public function link_guuid(string $name, string $table_name, $linkColumn = 'clId', $linkName = null, 
        $notnull = false,
        bool $unique=false,
        ?int $uniqueColumn=null,
        $inputtype = "", $default = 0, $description = null): IDiagramSchemaEntity
    {
        return $this->addProperties([[
            "clName" => $name, "clType" => self::VarChar,
            "clTypeLength"=> DiagramConstants::GUID_LENGTH,
            "clDefault" => $default, "clDescription" => $description, 
            "clLinkType" => $table_name, "clLinkColumn" => $linkColumn,
            "clIsUnique"=>$unique,
            "clIsUniqueColumnMember"=>!is_null($uniqueColumn),
            "clColumnMemberIndex"=>$uniqueColumn,
            "clInputType" => $inputtype, "clNotNull" => $notnull, "clLinkConstraintName" => $linkName
        ]]);
    }
    /**
     * add int column
     * @param mixed $name 
     * @param int $length 
     * @param mixed $description 
     * @return $this 
     */
    public function int(string $name, $length = 9, $default = 0, $description = null, ?array $extra = null): IDiagramSchemaEntity
    {
        $data = [
            "clName" => $name, "clType" => "Int", "clTypeLength" => $length,
            "clInputType" => "int", "clDescription" => $description, "clDefault" => $default, "clNotNull" => 1
        ];
        if ($extra){
            $data = array_merge($data, $extra);
        }
        return $this->addProperties([$data]);
    }
    public function address(string $prefix=""): IDiagramSchemaEntity{
        return $this->addProperties([
            ["clName"=>"{$prefix}AddrStreet","clType"=>"Text",],
            ["clName"=>"{$prefix}AddrNumber","clType"=>self::VarChar ,"clTypeLength"=>15],
            ["clName"=>"{$prefix}AddrBox", "clType"=>self::VarChar ,"clTypeLength"=>10],
            ["clName"=>"{$prefix}AddrPostalCode","clType"=>self::VarChar,"clTypeLength"=>10],
            ["clName"=>"{$prefix}AddrCity", "clType"=>"Text"],
            ["clName"=>"{$prefix}AddrCountry", "clType"=>self::VarChar, "clTypeLength"=>"4", "clDescription"=>"country's iso code"],
        ]);
    }
    public function date($name, $notnull = false, $default = 0, $description = null)
    {
        return $this->addProperties([[
            "clName" => $name,
            "clType" => "Date",
            "clInputType" => "int",
            "clNotNull" => $notnull, "clDescription" => $description,
            "clDefault" => $default, "clNotNull" => $notnull
        ]]);
    }
    public function datetime($name, $notnull = false, $default = 0, $description = null)
    {
        return $this->addProperties([[
            "clName" => $name, "clType" => "DateTime",
            "clDescription" => $description, "clNotNull" => $notnull, "clDefault" => $default, "clNotNull" => 1
        ]]);
    }
    /**
     * add a primary auto increment field
     * @param mixed $name 
     * @param int $length 
     * @param int $default 
     * @param mixed $description 
     * @return $this 
     */
    public function primary_auto($name, $length = 9, $default = 0, $description = null)
    {
        return $this->addProperties([[
            "clName" => $name, "clType" => "Int", "clTypeLength" => $length,
            "clInputType" => "int", "clDescription" => $description, "clDefault" => $default, "clNotNull" => 1, "clIsPrimary" => true, "clAutoIncrement" => 1
        ]]);
    }

    public function getName()
    {
        return $this->m_name;
    }

    /**
     * prefix
     * @param string $prefix 
     * @return $this 
     */
    public function dateUpdate($prefix = ""): IDiagramSchemaEntity
    {
        $this->addProperties([
            ["clName" => $prefix . "CreateAt", "clType" => "Datetime", "clDefault" => "NOW()", "clNotNull" => 1,]
        ]);
        $this->addProperties([
            ["clName" => $prefix . "UpdateAt", "clType" => "Datetime", "clDefault" => "NOW()", "clUpdateFunction" => "Now()", "clNotNull" => 1,]
        ]);
        return $this;
    }

    /**
     * set unit column members
     * @param mixed $column 
     * @param int $index 
     * @return $this 
     * @throws IGKException 
     */
    public function setUniqueColumnMember($column, $index=1){
        foreach($column as $n){
            if ($g = igk_getv($this->m_properties, $n)){
                $g->clIsUniqueColumnMember  = true;
                $g->clColumnMemberIndex  = $index;
            }
        }
        return $this;
    }

    /**
     * drop the entity definition 
     * @return void 
     */
    public function drop(){
        Logger::warn(sprintf('drop entity [%s]', $this->m_name));
        

    }
}
