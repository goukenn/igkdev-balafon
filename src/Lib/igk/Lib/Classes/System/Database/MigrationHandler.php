<?php
// @author: C.A.D. BONDJE DOUE
// @file: MigrationHandler.php
// @date: 20221112 08:36:25
namespace IGK\System\Database;

use Error;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\ArrayUtils;
use IGK\Helper\IO;
use IGK\Models\Migrations;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlReader;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
* use to run project migration
* @package IGK\System\Database
*/
class MigrationHandler{
    private $m_controller;
    protected const match = '/\/migration_[0-9]+_(?P<name>.+)\.php$/';
    public function __construct(BaseController $controller)
    {
        $this->m_controller = $controller;
    }
    static function _GetRealClassName(& $name, & $tabc){
        $new = array_slice(get_declared_classes(), $tabc);
        if ($new && (strtolower($new[0]) ==  $name)){
            $tabc += count($new);
            // real name
            $name = $new[0];
        }
    }
    /**
     * get order file 
     * @return null|string[] 
     * @throws IGKException 
     */
    protected function getfiles(string $order='up'){
        $ctrl = $this->m_controller;
        $dir = $ctrl->getClassesDir()."/Database/Migrations";
        $match = '/\/migration_[0-9]+_(?P<name>.+)\.php$/';
        if ($files = IO::GetFiles($dir, $match)){
            if ($order =='up')
                sort($files);
            else 
                rsort($files);
        }
        return $files;
    }
    public function remove(string $name){
        $g = $this->getfiles();
        $ctrl = $this->m_controller;
       
        $migrations = Migrations::select_all(['migration_controller'=>$ctrl->getName()]);
        ArrayUtils::FillKeyWithProperty($migrations, 'migration_name'); 

        while(count($g)>0){
            $c = array_shift($g);
            $migration_name = igk_io_basenamewithoutext($c);
            preg_match(self::match, $c, $tab);
            $nname = $tab['name'];
            if (($name == $nname) || ($name== $migration_name)){

                if (isset($migrations[$migration_name])){

                    if ($migrations[$migration_name]->migration_batch){
                        $builder = new SchemaBuilder;
                        $schema = $builder->migrations();
                        self::MigrateFile($c, $ctrl, 'down', $schema);
                        self::_MigrateSchemaBuilder($builder, $this->m_controller);

                       
                    }

                    if (($mig = $migrations[$migration_name]) instanceof Migrations){
                        Migrations::delete($mig->clId);
                    }
                }
                unlink($c);
                break;
            }
        }
    }
    private static function _MigrateSchemaBuilder(SchemaBuilder $builder, BaseController $ctrl){
        $node = HtmlReader::Load($builder->render(), "xml"); 
        $tab = igk_db_load_data_schemas_node($node, $ctrl); 
        if ($tab){
            SchemaBuilderHelper::Migrate($tab);
        }
    }
    private static function MigrateFile(string $file, BaseController $ctrl, string $method, SchemaMigrationBuilder $schema){   
        $ns = $ctrl::ns(\Database\Migrations::class);
        $tabcl = get_declared_classes();
        $tabc = count($tabcl);  

        preg_match(self::match, $file, $tab);
        $name = $tab['name'];
        self::_GetRealClassName($name, $tabc);          
        include_once $file;
        Logger::info('migration file: '.$file);
        Logger::info('migrate: '.$name);
        $cl = igk_ns_name($ns."/".$name);            
        if (class_exists($cl, false)){
            $cl = new $cl();
            $cl->$method($schema);
        }else {
            igk_die('include file no class found .'.$name);
        }
    }
    /**
     * migrate up
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws Error 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function up(bool $fist_only=true){
        return $this->migrate(__FUNCTION__, $fist_only);         
    }
    /**
     * migrate down
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws Error 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function down(bool $fist_only=true){
        return $this->migrate(__FUNCTION__, $fist_only);
    }
    /**
     * migrate operation 
     * @param string $method 
     * @param bool $first_only 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws Error 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function migrate(string $method, bool $first_only=true){
        $files = $this->getfiles($method);
        if (empty($files)){
            return;
        }
        if (!Migrations::model()->getDataAdapter()->getIsConnect()){
            return;
        }
        $ctrl = $this->m_controller;
        $ns = $ctrl::ns(\Database\Migrations::class);
        $tabcl = get_declared_classes();
        $tabc = count($tabcl);    

        $match = self::match;
        $builder = new SchemaBuilder;        
        $schema =  $builder->migrations();
        $migrations = null;
        $status = $method == 'down'? 0: 1;
        $order = $method == 'down' ? 'DESC':'ASC';
        try{
            if (Migrations::model()->tableExists()){
                $migrations = Migrations::select_all(['migration_controller'=>$ctrl->getName()], [
                    'OrderBy'=>['clId|'.$order],
                ]);
            }
            else{
                // + | --------------------------------------------------------------------
                // + | no migration available
                // + |                
                return;
            }
        } catch(\Exception $ex){
            return;
        }
        ArrayUtils::FillKeyWithProperty($migrations, 'migration_name'); 
        $updates = [];
        $insert = [];
        $v_handle_db = false;
        $v_execute = false;
        while (($tc = count($files))>0){
            $c = array_shift($files);
            $migration_name = igk_io_basenamewithoutext($c);
            if (isset($migrations[$migration_name])){
                if ($v_handle_db){
                    continue;
                }
                if ($method=='down'){
                    //+ for down case check status 0 
                    if (!$migrations[$migration_name]->migration_batch){
                        continue;
                    }
                }else {
                    if ($migrations[$migration_name]->migration_batch){
                        continue;
                    }
                }
                $migrations[$migration_name]->migration_batch = $status;
                $updates[] = $migrations[$migration_name];
                //+ if ctrl
                if ($ctrl && $first_only ){
                    $v_handle_db = true;
                }
                $v_execute = true;
            }else{
                //+ register new migration files - with status
                $row = \IGK\Models\Migrations::createEmptyRow();
                $row->migration_name = $migration_name;
                $row->migration_batch = $status;
                $row->migration_controller = $ctrl->getName();
                $insert[] = $row;
            }
            if (!$v_execute){
                continue;
            }


            preg_match($match, $c, $tab);
            $name = $tab['name'];
            self::_GetRealClassName($name, $tabc);          
            include_once $c;
            Logger::info('migration file: '.$c);
            Logger::info('migrate: '.$name);
            $cl = igk_ns_name($ns."/".$name);            
            if (class_exists($cl, false)){
                $cl = new $cl();
                $cl->$method($schema);
            }else {
                igk_die('include file no class found .'.$name);
            }
            if ($first_only && $v_handle_db){
                //+ stop - register register new migration 
                $v_execute = false;
                $status = 0;
            
            }
        }         
        $node = HtmlReader::Load($builder->render(), "xml"); 
        $tab = igk_db_load_data_schemas_node($node, $this->m_controller); 
        if ($tab){
            SchemaBuilderHelper::Migrate($tab);
        }
        foreach ($insert as $value) {
            Migrations::insert($value);
        }
        foreach ($updates as $value) {
            Migrations::update($value);
        }
    }
}