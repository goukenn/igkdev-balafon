<?php
namespace IGK\Models;

use IGK\Controllers\BaseController;
use IGK\System\Database\QueryBuilder;  
use IGK\Database\DbQueryResult;
use IGK\Database\DbSchemas;
use IGK\System\Database\DbConditionExpressionBuilder;
use IGK\System\Database\MySQL\MYSQLQueryFetchResult;
use IGKException;
use IGKQueryResult;
use SQLCondExpression;

use function igk_resources_gets as __;
use function igk_getv as getv;
use function igk_get_robjs as get_robjs;
use function igk_count as fcount;
use function igk_environment as environment;
use function igk_form_input_type as form_input_type;


///<summary>Extension</summary>
abstract class ModelEntryExtension{

    ///<summary>get the current model expression</summary>
   public static function model(ModelBase $model){
       return $model; 
   }
   /**
    * retrieve controller static extension
    * @param ModelBase $model 
    * @return null|BaseController 
    * @throws IGKException 
    */
   public static function controller(ModelBase $model){
        return $model->getController();
   }

   public static function create(ModelBase $model, $raw=null){        
        $cl = get_class($model);
        $c = new $cl($raw); 
        if ($craw = $c->to_array()) {
            $g = $c->insert($craw);
            if (($g!== false) && ($g!==null)){
                // if (igk_environment()->querydebug){
                //     igk_wln_e("explore ");
                //     igk_wln_e($g->to_array(), $model->last_id());
                // }
                if ($g instanceof $model)
                {
                    $c->updateRaw($g);  
                }
            } else {
                return null;
            }
        }
        return $c;  
   }
   public static function createEmptyRow(ModelBase $model){
      
        $ctrl = igk_getctrl($model->controller ?? SysDbController::class);
        return DbSchemas::CreateRow($model->getTable(), $ctrl);
        
   }
    /**
     * create a model from an object. 
     * @param ModelBase $ctrl 
     * @param mixed $object 
     * @return mixed 
     * @throws Exception 
     */
    public static function createFromCache(ModelBase $model, $object){
        static $caches;
        if ($caches===null){
            $caches = [];
        }
        if ($object==null){
            return null;
        }

        $id = spl_object_id($object);
        if ($v = getv($caches, $id)){ 
            return $v->_cache;
        } 
        $cl = get_class($model);
        $_obj = new $cl($object); 
        $v = (object)["_cache"=>$_obj, "object"=>$object];
        $caches[$id] = $v;
        return $v->_cache;
    }

    public static function createIfNotExists(ModelBase $model, $condition, $extra=null){
        
        if (! ($row = $model->select_row($condition))){
            if ($extra){
                $condition = (object)$condition;
                foreach($extra as $k=>$v){
                    $condition->$k = $v;
                }
            }
            return $model::create($condition);
        }
        return $row;
    }
    public static function insertOrUpdate(ModelBase $model, $condition, callable $updating=null){
        if (!($row = $model->select_row($condition))){
            return $model::create($condition);
        }
        if ($updating)
            $updating($condition);
        $p = $model->getPrimaryKey(); 
        $model::update($condition, [$p=>$row->{$p}]);
        return null;
    }
    public static function insertIfNotExists(ModelBase $model, ?array $condition){
       if (!($model->select_row($condition))){
            return self::insert($model, $condition,false);
       }
       return false;
    }
    public static function updateOrCreateIfNotExists(ModelBase $model, $condition, $update_extras=null){
        if (!($row = $model->select_row($condition))){
            if ($update_extras){
                $condition = array_merge($condition, $update_extras);
            }
            return $model::create($condition);
        }
        if ($update_extras){
            $condition = array_merge($condition, $update_extras);
        }
        $p = $model->getPrimaryKey(); 
        return $model::update($condition, [$p=>$row->{$p}]);
    }
    public static function beginTransaction(ModelBase $model){
        return $model->getDataAdapter()->beginTransaction();
    }
    public static function commit(ModelBase $model){
        return $model->getDataAdapter()->commit();
    }
    public static function rollback(ModelBase $model){
        return $model->getDataAdapter()->rollback();
    }
    
    /**
     * 
     * @param ModelBase $model 
     * @return array 
     * @throws Exception 
     */
    public static function select(ModelBase $model, $conditions=null, $options=null)
    {
        return self::select_all($model, $conditions, $options);
    }
    
    public static function select_all(ModelBase $model, $conditions=null, $options=null){  

        $tab = [];
        $driver = $model->getDataAdapter(); 
        $cl = get_class($model);
        if ($data = $driver->select($model->getTable(), $conditions, $options)){
            foreach($data->getRows() as $row){
                $c = new $cl($row->to_array());  
                $tab[] = $c; 
            }  
        }
        return $tab;
    }
    /**
     * get model dataadapter driver
     * @param ModelBase $model 
     * @return mixed 
     * @throws IGKException 
     */
    public static function driver(ModelBase $model){
        return $model->getDataAdapter();
    }
    /**
     * create query condition for grammar helper
     * @param ModelBase $model 
     * @param string $operand 
     * @param null|array $field_key_values 
     * @return DbConditionExpressionBuilder 
     */
    public static function query_condition(ModelBase $model, string $operand, ?array $field_key_values = null){
        $g = new DbConditionExpressionBuilder($operand);
        if ($field_key_values !== null){
            foreach($field_key_values as $k=>$v){
                $g->add($k, $v);
            }
        }
        return $g;
    }
    public static function query_all(ModelBase $model, $conditions=null, $options=null){  
        $driver = $model->getDataAdapter(); 
        return  $driver->select($model->getTable(), $conditions, $options);
    }
    public static function count(ModelBase $model, $conditions=null, $options=null){  
        $driver = $model->getDataAdapter();  
        $r = 0; 
        if ($m = $driver->selectCount($model->getTable(), $conditions, $options)){
            $r = $m->getRowAtIndex(0)->count; 
        }
        return $r;
    }
    public static function select_row(ModelBase $model, $conditions, $options=null ){     
        $cl = get_class($model);   
        if (is_numeric($conditions)){
            $conditions = [$model->getPrimaryKey()=>$conditions]; 
        }
        $r= $model->getDataAdapter()->select($model->getTable(), $conditions, $options);
        if($r && $r->RowCount == 1){
            $g=$r->getRowAtIndex(0);
            $g->{"sys:table"}=$model->getTable(); 
            return new $cl($g->to_array());  
        }
        return null;
    }

    public static function select_row_query(ModelBase $model, $conditions, $options=null ){     
        $r = static::select_query($model, $conditions, $options);        
        if($r && $r->RowCount == 1){
            $g=$r->getRowAtIndex(0);
            $g->{"sys:table"}=$model->getTable(); 
            return $g;
        }
        return null;
    }
    public static function select_query(ModelBase $model, $conditions=null, $options=null){
        return $model->getDataAdapter()->select($model->getTable(), $conditions, $options);     
    } 
    public static function select_query_rows(ModelBase $model, $conditions=null, $options=null){
        if ($g = static::select_query($model, $conditions, $options)){
            return $g->getRows();
        }
        return null;
    }
    /**
     * retrieve select query to send
     * @param ModelBase $model 
     * @param mixed $conditions 
     * @param mixed $options 
     * @return mixed 
     * @throws IGKException 
     */
    public static function get_query(ModelBase $model ,$conditions=null, $options=null) {
        return $model->getDataAdapter()->get_query($model->getTable(), $conditions, $options  );
    }

    
    public static function update(ModelBase $model, $value=null, $conditions=null){
        $driver = $model->getDataAdapter(); 
        $primary = $model->getPrimaryKey();
        if ($model->is_mock()){
            if (is_numeric($conditions)){            
                $conditions = [$model->getPrimaryKey()=> $conditions];            
            } 
            return $driver->update($model->getTable(), $value, $conditions, $model->getTableInfo());
        }
        if ($value===null){ 
            $value = $model->to_array();
            if ($g = $model->getUpdateUnset()){
                foreach($g as $k){
                    unset($value[$k]);
                }
            } 
            return $driver->update($model->getTable(), $value, [$primary=>$model->$primary]);
        }
        $r = $driver->update($model->getTable(), $value, $conditions); 
        return $r;
    }
    public static function delete(ModelBase $model, $conditions=null){        
        $driver = $model->getDataAdapter(); 
        $_pm = $model->getPrimaryKey();
        if ($model->is_mock()){
            if (is_numeric($conditions)){            
                $conditions = [$_pm=> $conditions];            
            } 
        }else {
            $conditions = [$_pm=>$model->{$_pm}];
        }
        return $driver->delete($model->getTable(), $conditions);
    }
    /**
     * insert and update the entry  
     * @param ModelBase $model 
     * @param array|object $entries 
     * @return mixed 
     * @throws IGKException 
     */
    public static function insert(ModelBase $model, $entries, $update=true){ 
     
        $ad = $model->getDataAdapter();
        if ( $r = $ad->insert($model->getTable(), $entries, $model->getTableInfo())){
 
            if ($update){
                $ref_id = $model->getRefId(); 
                if (($id = $ad->last_id()) && ($id !== -1)){ 
                    $s = $model::select_row([$ref_id=>$id]);            
                    if ($s && is_object($entries)){
                        foreach($s->to_array() as $k => $v){
                            $entries->$k = $v ;
                        }
                    }
                    return $s;
                }
            }
            return true;
        }
        return false;
    }
    public static function last_id(ModelBase $model){ 
        return $model->getDataAdapter()->last_id();
    }
    public static function last_error(ModelBase $model){
        return $model->getDataAdapter()->last_error();
    }
    public static function query(ModelBase $model, $query){
        $driver = $model->getDataAdapter();
        return $driver->sendQuery($query);
    } 
    public static function factory(ModelBase $model){
        // create a factory object
        $c = get_class($model);
        $cl = $model->getFactory();
        if (class_exists($cl)){
            $arg = func_get_args(); 
            return new $cl(...$arg);
        }
        die("factory class not found . ".$cl);
    }
    /**
     * display for this model view
     * @return void 
     */
    public static function display(ModelBase $model){
        return "display:".$model->to_json();
    }

    /**
     * return the selected data
     * @param ModelBase $model 
     * @return string 
     * @throws Exception 
     */
    public static function formSelectData(ModelBase $model, $selected=null, ?callable $callback=null){
        $data = [];
        foreach($model::select_all() as $m){    
            if ($callback){
                if ($g = $callback($m)){
                    $data[] = $g;
                }
                continue;
            }
            $g = ["i"=>$m->{$m->getPrimaryKey()},"t"=>$m->display()];
            if ((is_callable($selected) && $selected($m)) || ($selected && ($selected == $g["i"]))) {
                $g["selected"] = true;
            } 
            $data[] = $g;
        }
        return $data;
    }

    public static function colKeys(ModelBase $model){
         if ($tablekey = igk_db_get_table_info($model->getTable())){
            $inf = [];
            array_map(function($b)use (& $inf){
                $inf[$b->clName]  = $b;
            }, $tablekey["ColumnInfo"]);
            return array_keys($inf);
         }
        return null;
    }

    /**
     * drop the table
     */
    public static function drop(ModelBase $model){
        $driver = $model->getDataAdapter();
        return $driver->dropTable($model->getTable()); 
    } 
    public static function createTable(ModelBase $model){
        $driver = $model->getDataAdapter(); 
        $info = $model::getDataTableDefinition();    
        return $driver->createTable($model::table(), igk_getv($info, "tableRowReference"), igk_getv($info, "Description"));
    }

    /**
     * return this model form fields
     * @param ModelBase $model 
     * @param bool $edition
     * @return array 
     * @throws IGKException 
     */
    public static function formFields(ModelBase $model, $edit=false){
        $cl = $model->getFormFields();
        $t = [];
        
        $inf =  $model->getTableInfo(); //  igk_db_get_table_info($model->getTable());
        // igk_wln_e($tablekey);
        $ctrl = $model->getController();
        // array_map(function($b)use (& $inf){
        //     $inf[$b->clName]  = $b;
        // }, $tablekey["ColumnInfo"]);
 
        $binfo = [];

        $b = (igk_count($cl)>0) ? $cl : array_keys($model->to_array());
       // igk_wln_e($model->to_json());
        //use only data for field
        foreach($b as $v){
            if (!isset($inf[$v]))
                continue;
            $info  = $inf[$v];
            $r = ["type"=>"text", "value"=>$model->$v];
            $type = !empty($info->clInputType) ? form_input_type($info->clInputType) : $info->clType;
          
            $attribs = [];
            if ($info->clLinkType){
                $r["type"] = "select";
                if (!$binf = getv($binfo, $info->clLinkType)){
                    $binf = igk_db_get_table_info($info->clLinkType);
                    $binfo[$info->clLinkType] = $binf;
                }
                if ($v_cl = igk_db_get_model_class_name($info->clLinkType)){
                    // class defined :
                    $stb = [];
                    foreach($v_cl::select_all() as $m){
                        $stb[] = ["i"=>$m->{$m->getPrimaryKey()},"t"=>$m->display()];
                    }
                    $r["data"] = $stb;
                }
                $r["selected"] = $model->$v;
             
            }else{
                switch(strtolower($type)){
                    case "enum": 
                        $attribs["maxlength"] = $info->clTypeLength;
                        $attribs["list"] = strtolower($v."-datalist");
                        if (!empty($info->clDescription)){
                            $attribs["placeholder"] = __($info->clDescription);
                        }
                        if (!$edit && !empty($info->clDefault)){
                            $r["value"] = $info->clDefault;
                        }
                        if ($info->clNotNull){
                            $attribs["required"] = "required";
                        }                        
                        $r["type"]="text";
                        $r["attribs"] = $attribs;
                        $t[$v] = $r;
                        $r = [];
                        $r["type"] = "datalist";
                        $stb = [];
                        foreach(explode(",", $info->clEnumValues) as $g){
                            $stb[] = ["i"=>$g,"t"=>$g];
                        }
                        $r["data"] = $stb;
                        $r["id"]= strtolower($v."-datalist");
                        $attribs["maxlength"] = $info->clTypeLength;
                        $t[$v."-datalist"] = $r; 
                        continue 2; 
                    case "bool":
                            $r["type"]="checkbox";
                        break;
                    case "text":
                            $r["type"] = "textarea";
                        break;
                    case "date":
                            $r["type"] = "date";
                        break;
                    case "datetime":
                    case "timespan":
                            $r["type"] = "datetime-local";
                            if (!$edit && environment()->is("DEV")){
                                $r["value"] = "1986-01-28T11:38:00.01";
                            }
                        break;
                    case "int":
                            $r["type"] = "int";
                            $attribs["maxlength"] = 9;
                            $attribs["pattern"] = "[0-9]+";
                        break;
                    case "float":
                            $r["type"] = "float";
                            $attribs["maxlength"] = 9;
                            $attribs["pattern"] = "[0-9]+(\.[0-9]+)?";
                        break;
                    case "password":
                        $r["type"] = "password";
                        $attribs["igk-validate-pwd"] = "1";
                        $attribs["autocomplete"]="off";
                        break;
                    case "varchar":                        
                    default:
                        $attribs["maxlength"] = $info->clTypeLength;
                        break;
                }
            }
            if (!empty($info->clDescription)){
                $attribs["placeholder"] = __($info->clDescription);
            }
            if (!$edit && !empty($info->clDefault)){
                $r["value"] = $info->clDefault;
            }
            if ($info->clRequire || $info->clNotNull){
                $attribs["required"] = "required";
            }
            $attribs["autocomplete"]="off";
            $r["attribs"] = $attribs;
            $t[$v] = $r;
        } 
        $t["::model"]=["type"=>"hidden", "value"=>base64_encode(get_class($model))];
        if ($ctrl)
        $t["::ctrl"]=["type"=>"hidden", "value"=>base64_encode(get_class($ctrl))];
        return $t;
    }
    ///<summary>return the model table name</summary>
    /**
     * return the model table name
     * @return void 
     */
    public static function table(ModelBase $model,$column=null){
        if (!empty($column))
            $column = ".".$column;
        return $model->getTable().$column;
    }
    /**
     * build column name
     * @param ModelBase $model 
     * @param mixed $column 
     * @return void 
     */
    public static function column(ModelBase $model,$column){
        return static::table($model, $column);
    }
    public static function primaryKey(ModelBase $model){
        return $model->getPrimaryKey();
    }
    /**
     * get the cached row
     * @param ModelBase $model 
     * @param mixed $primaryKeyIdentifier 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException if row not found
     */
    public static function cacheRow(ModelBase $model, $primaryKeyIdentifier, $throw=true){
        static $states;
		if ($states===null){
			$states = [];
		}
        $id = $primaryKeyIdentifier;
        if (is_array($id))
            $id = json_encode($primaryKeyIdentifier);
        $cl  = get_class($model);
        $key = $cl."/".$id;
		if ($row = getv($states, $key)){
			return $row;
		}
        $g = null;
        if (is_array($primaryKeyIdentifier))
            $g = $primaryKeyIdentifier;
        else 
		    $g = [$model->getPrimaryKey()=>$primaryKeyIdentifier];
		$row = $cl::select_row($g);
		if (!$row){
            if ($throw)
			    throw new IGKException("Row not found ".$model->getTable().":".json_encode($g));
            return null;
		}
		$states[$key] = $row;
		return $row;

    }
    /**
     * get cache row
     * @param ModelBase $model 
     * @param mixed $primaryKeyIdentifier 
     * @return ?ModelBase object 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function cacheIsRow(ModelBase $model, $primaryKeyIdentifier){
        return static::cacheRow($model, $primaryKeyIdentifier, false);
    }

    /**
     * 
     * @param ModelBase $model 
     * @param null|callable $filter 
     * @param mixed $condition 
     * @param mixed $options 
     * @return array 
     */
    public static function form_select_all(ModelBase $model, ?callable $filter=null, $condition=null, $options=null){
        if ($filter === null){
            $filter = function($r)use($model){
                return ["i"=>$r->{$model->getPrimaryKey()}, "t"=>$r->{$model->getDisplay()} ];
            };
        }
      
        if ($options==null){
            $options = [];
        }
        $tab = [];
        $options[DbQueryResult::CALLBACK_OPTS] = 
        function($row)use($filter, & $tab){
            if ($g = $filter($row)){
                $tab[] = $g;
            }
            return false; 
        };
        $model::select_all($condition, $options);
        return $tab;
    }
    /**
     * 
     * @param ModelBase $model 
     * @param mixed $expression 
     * @return mixed 
     * @throws IGKException 
     */
    public static function linkCondition(ModelBase $model, $expression){
        $express = explode(".", $expression);
        if (fcount($express)!=2){
            throw new IGKException("expression not valid");
        }
        return $model->getDataAdapter()->createLinkExpression($model::table(), $express[0], $express[1], $model->getPrimaryKey());  
    }
    /**
     * udpate with request
     * @param ModelBase $model 
     * @return mixed 
     */
    public static function requestUpdate(ModelBase $model){
        $b = get_robjs($model->getFormFields());
		return $model::update((array)$b, $model->{$model->getPrimaryKey()});	
    }

    public static function requestAdd(ModelBase $model){
        $b = get_robjs($model->getFormFields());
		return $model::createIfNotExists((array)$b); 
    }

    /**
     * return primary key id
     * @param ModelBase $model 
     * @param mixed $condition 
     * @return mixed 
     */
    public static function id(ModelBase $model, $condition=null){
        if ($model->is_mock()){            
            if (!empty($condition) && ($trow = $model->cacheIsRow($condition))){
                return $trow->{$model->getPrimaryKey()};
            }
            return 0;
        }
        return $model->{$model->getPrimaryKey()};
    }
    /**
     * get prepare query builder
     * @param ModelBase $model 
     * @return QueryBuilder 
     */
    public static function prepare(ModelBase $model){
        return new QueryBuilder($model);
    }

    public static function modelTableInfo(ModelBase $model){
        return $model->getTableInfo();
    }
    /**
     * retrieve query column
     * @param ModelBase $model 
     * @return array
     */
    public static function queryColumns(ModelBase $model, ?array $filter=null, bool $useall = false){
        $tab = array_map(function($a)use($model){
            return $model::column($a);
        }, array_keys(self::modelTableInfo($model)));
        $tab = array_combine($tab, $tab);
        if ($filter){
            $ctab = [];
            foreach($filter as $k=>$v){
                if (is_numeric($k) && is_string($v)){
                    $k = $v;
                    $v = $model::column($k);
                }
                if (isset($tab[$k]) || (($k = $model::column($k)) && isset($tab[$k])) ){
                    $ctab[$k] = $v;
                    if ($useall){
                        $tab[$k] = $v;
                    }
                }  
            }
            if (!$useall){
                $tab = $ctab; 
            }
        }
        return $tab;
    }
    /**
     * 
     * @param ModelBase $model 
     * @return void 
     */
    public static function select_fetch(ModelBase $model, ?array $condition=null, ?array $options=null){
        $driver = $model->getDataAdapter(); 
        $query = $driver->getGrammar()->createSelectQuery($model->table(), $condition, $options);
        $res = $driver->createFetchResult($query, $model); 
        $driver->sendQuery($query, false, array_merge($options ?? [], [
            IGKQueryResult::RESULTHANDLER => $res
        ]));
        return $res;
    }



   
}