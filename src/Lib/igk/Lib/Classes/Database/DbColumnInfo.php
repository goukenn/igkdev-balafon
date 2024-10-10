<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbColumnInfo.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Database;

use IGK\Database\Traits\DbColumnInfoMethodTrait;
use IGK\Database\Traits\DbColumnInfoTrait;
use IGK\Models\DataTypes;
use IGK\System\Database\DbUtils;
use IGKException;
use IGKObject;
use IGKSysUtil;
use ReflectionException;

require_once __DIR__ . "/Traits/DbColumnInfoTrait.php";
///<summary>Represent class: DbColumnInfo</summary>
/**
 * Represent DbColumnInfo class
 */
final class DbColumnInfo extends IGKObject implements IDbColumnInfo
{
    use DbColumnInfoTrait;
    use DbColumnInfoMethodTrait;

    const TYPE_LENGTH_REGEX = "/\(\s*(?P<size>\d+)\s*\)/";

    /**
     * create an auto increment field
     * @param mixed $name 
     * @return static 
     */
    public static function CreateAutoInc($name){
        return new static(['clName'=>$name, 'clAutoIncrement'=>true, 'clIsUnique'=>true]);
    }
    /**
     * get if this column info must be consider as a dump fields
     * @return bool 
     */
    public function getIsDumpField(): bool
    {
        return DbUtils::GetIsDumpField($this);
    }
    ///<summary></summary>
    ///<param name="array" default="null"></param>
    /**
     * 
     * @param mixed $array the default value is null
     */
    public function __construct($array = null)
    {
        $this->clType = "Int";
        // $this->clTypeLength = new DbDolumnDefaultLength(11);      
        $this->initialize($array);
        // + | -------------------------------------------------
        // + | fix resolved data 
        // + | 
        // if ($this->clTypeLength instanceof DbDolumnDefaultLength){
        //     $this->clTypeLength = intval($this->clTypeLength );
        // }
        if (is_null($this->clTypeLength)) {
            $this->clTypeLength = 11;
        }
    }
    /**
     * explode linkto expression 
     * @param string $data 
     * @return array 
     * @example type, column
     */
    private static function ExplodeLinkTo(string $data)
    {
        $table = explode(',', $data, 3);
        $clLinkType = array_shift($table);
        $clLinkColumn = $table ? array_shift($table) : null;
        $clType = $table ? array_shift($table) : null;

        return compact('clLinkType', 'clLinkColumn', 'clType');
    }
    /**
     * initialize db info with data
     * @param null|array|object $array 
     * @return void 
     */
    protected function initialize($array = null)
    {
        if (is_array($array)) {
            $t = get_class_vars(get_class($this));
            if (isset($array['clLinkTo'])) {
                $data = self::ExplodeLinkTo($array['clLinkTo']);
                unset($array['clLinkTo']);
                $array = array_merge($data, $array);
            }
            foreach ($array as $k => $v) {
                if (!array_key_exists($k, $t)) {
                    continue;
                }
                if ($v && preg_match("/^(false|true)$/i", $v)) {
                    $v = igk_getbool($v);
                }
                $this->$k = $v;
            }

            // + | treat type :
            self::_TreatType($this);
            // + | treat link
            if ($l = $this->clLinkType){
                $r = explode(',', $l, 2);
                if ((count($r)>1) && (!$this->clLinkColumn)){
                    $this->clLinkColumn = trim($r[1]);
                }
                $this->clLinkType = trim($r[0]);
            }

            // + | --------------------------------------------------------------------
            // + | if already setup auto - make int data to be not null
            // + |   
            // number must not allow null values
            if (is_null($this->clNotNull)) {
                $this->clNotNull = false;
            }
            if ($this->clNotNull && empty($this->clDefault) && preg_match("/(int|float)/i", $this->clType)) {
                $this->clDefault = 0;
                if (!$this->clLinkType)
                    $this->clNotNull = true;
            }
        }
        if (!self::SupportTypeLength($this->clType))
            $this->clTypeLength = null;
        if ($this->clDefault && $this->clLinkType) {
            // + | detect link expression
            // + | sample: member.operator
            if (preg_match("/(.)+\.(.)+/", $this->clDefault)) {
                $this->clDefaultLinkExpression = $this->clDefault;
            }
            $this->clDefault = null;
        }
    }
    private static function _TreatType($q)
    {
        if (is_null($q->clType)) {
            $q->clType = 'Int';
        } else if (preg_match($rgx = self::TYPE_LENGTH_REGEX, $q->clType, $tab)) {
            $q->clType = trim(preg_replace($rgx, '', $q->clType));
            $q->clTypeLength = intval($tab['size']);
        }
        switch (strtolower($q->clType)) {
            case 'guid':
                $q->clTypeLength = 38;
                if (is_null($q->clNotNull)) {
                    $q->clNotNull = true;
                }
                if (is_null($q->clIsUnique)) {
                    $q->clIsUnique = true;
                }
                $q->clType = DbDataTypes::VarChar;
                if (is_null($q->clValidator)) {
                    $q->clValidator = 'guid';
                }
                break;
            case strtolower(DbDataTypes::PhoneNumber):
                    $q->clType = DbDataTypes::VarChar;
                    $q->clTypeLength = DbDataTypes::PHONE_NUMBER_MAX_LENGTH;
                break;
        }
    }
    /**
     * return validator class 
     * @return null|string 
     */
    public function getValidatorClass()
    {
        $val = $this->clValidator;
        if (is_null($val)) {
            switch (strtolower($this->clType)) {
                case 'text':
                    return 'no-html';
                case 'guid':
                    return 'guid-validator';
                default:
                    break;
            }
        }
        return $val;
    }

    /**
     * create definition column info from class definition 
     * @param string $class_name 
     * @return array 
     * @throws ReflectionException 
     * @throws IGKException 
     */
    public static function CreateDefArrayFromClass(string $class_name)
    {
        $g = igk_sys_reflect_class($class_name);
        $vars = get_class_vars($class_name);
        $cls = [];
        foreach (array_keys($vars) as $n) {
            $c = $g->getProperty($n);
            $cm = $c->getDocComment();
            $type = 'text';
            $ln = 0;
            $notnull = true;
            if (!empty($cm)) {
                // = (_(auto|index|primary))*
                if (preg_match(
                    "/@var\s+(\?)?(?P<name>(int|string|varchar|text|datetime|float|integer|json|blob)((_(auto|index|primary|unique))*)?)(\(\s*(?P<length>[0-9]+)\s*\))?/i",
                    $cm,
                    $tab
                )) {
                    $type = $tab['name'];
                    $settings = explode('_', $type);
                    $type = $settings[0];
                    $ln = intval(igk_getv($tab, 'length', 9));
                    $notnull = strpos($tab[0], '?') !== false ? false :   true;
                    $index = 0;
                    $unique = 0;
                    $primary = 0;
                    $auto = 0;
                    switch ($type) {
                        case 'string':
                            $type = 'varchar';
                            break;
                    }
                    if (count($settings) > 0) {
                        if (in_array("index", $settings)) {
                            $index = 1;
                        }
                        if (in_array("unique", $settings)) {
                            $unique = 1;
                        }
                        if (in_array("primary", $settings)) {
                            $primary = 1;
                        }
                        if (in_array("auto", $settings)) {
                            $auto = 1;
                        }
                    }
                }
            }
            $c = new static();
            $c->clName = $n;
            $c->clType = $type;
            $c->clTypeLength = $ln;
            $c->clIsIndex = $index;
            $c->clAutoIncrement = $auto;
            $c->clIsUnique = $unique;
            $c->clIsPrimary = $primary;
            $c->clDefault = null;
            $c->clNotNull = $notnull;
            $cls[$n] = $c;
        }
        return $cls;
    }

    ///<summary> return a filtered array of property</summary>
    /**
     * return a filtered array of property
     * @return array 
     */
    public function to_array()
    {
        $c = [];
        foreach ($this as $k => $v) {
            $c[$k] = $v;
        }
        if (!$this->clIsUniqueColumnMember) {
            unset($c["clIsUniqueColumnMember"]);
            unset($c["clColumnMemberIndex"]);
        }
        if (!$this->clNotNull) {
            unset($c["clNotNull"]);
        }
        if ((strtolower($this->clType) == "int") && ($this->clTypeLength == 11)) {
            unset($c["clTypeLength"]);
        }
        return $c;
    }
    /**
     * 
     * @param mixed $attribs 
     * @param mixed $tb 
     * @param mixed $ctrl 
     * @param mixed &$tbrelation 
     * @return DbColumnInfo 
     */
    public static function CreateWithRelation($attribs, $tb, $ctrl, &$tbrelation = null)
    {
        $cl = new DbColumnInfo(igk_to_array($attribs));
        if (!empty($cl->clLinkType)) {
            $cl->clLinkType = IGKSysUtil::DBGetTableName($cl->clLinkType, $ctrl);
        }
        if (($tbrelation !== null) && !empty($cl->clLinkType)) {
            if (!isset($tbrelation[$tb]))
                $tbrelation[$tb] = array();
            $tbrelation[$tb][$cl->clName] = array(IGK_COLUMN_TAGNAME => $cl->clName, "Table" => $cl->clLinkType);
        }
        return $cl;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
     * 
     * @param mixed $key
     */
    public function __get($key)
    {
        $d = get_class_vars(get_class($this));
        if (array_key_exists($key, $d)) {
            return $this->$key;
        }
        igk_die("__get Not implements : " . $key . " " . get_class($this));
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        igk_die("variable : [" . $key . "] Not Implements");
    }
    ///<summary>display value</summary>
    /**
     * display value
     */
    public function __toString()
    {
        return "DbColumnInfo[#" . $this->clName . "]";
    }
    ///get association info array
    /**
     */
    public static function AssocInfo($array, $tablename = null)
    {
        if (!is_array($array))
            igk_die("array is not an array");
        $t = array();
        foreach ($array as $k => $v) {
            if (is_object($v)) {
                if ($k !== $v->clName) {
                    $t[$v->clName] = $v;
                } else {
                    $t[$k] = $v;
                }
            } else {
                igk_debug_wln("v is not an object : " . igk_count($array));
            }
        }
        return $t;
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function GetColumnInfo()
    {
        return get_class_vars("DbColumnInfo");
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function NewEntryInfo()
    {
        return new DbColumnInfo(array(
            IGK_FD_NAME => IGK_FD_ID,
            IGK_FD_TYPE => "Int",
            "clAutoIncrement" => true
        ));
    }


    ///<summary> get row default value</summary>
    /**
     * get column default value
     * @param DbColumnInfo $v 
     * @return null|int|string 
     */
    public static function GetRowDefaultValue(IDbColumnInfo $v)
    {
        if ($v->clNotNull) {
            if (empty($v->clType)) {
                igk_dev_wln_e(__FILE__ . ":" . __LINE__, "is empty ");
            }
            switch (strtolower($v->clType)) {
                case "int":
                case "float":
                    if (empty($v->clDefault))
                        return 0;
                    break;
            }
            if ($v->clDefault === null) {
                return "";
            }
        }
        if (!$v->clNotNull) {
            if (!$v->clDefault) {
                return null;
            }
        }
        return $v->clDefault;
    }

    /**
     * check for db column info
     * @param IDbColumnInfo $v 
     * @param mixed $value 
     * @return bool
     */
    public static function IsDbColumnInfoFunction(IDbColumnInfo $v, $value): bool
    {
        if ($value == 'Now()') {
            return true;
        }
        return false;
    }
    public static function IsNumber(IDbColumnInfo $v){
        return preg_match('/\\b(int|float|double)\\b/i',$v->clType);
    }
}
