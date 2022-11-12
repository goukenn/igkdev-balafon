<?php
// @author: C.A.D. BONDJE DOUE
// @file: MigrationHandler.php
// @date: 20221112 08:36:25
namespace IGK\System\Database;

use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlReader;

///<summary></summary>
/**
* 
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
    protected function getfiles(){
        $ctrl = $this->m_controller;
        $dir = $ctrl->getClassesDir()."/Database/Migrations";
        $match = '/\/migration_[0-9]+_(?P<name>.+)\.php$/';
        $files = IO::GetFiles($dir, $match);
        sort($files);      
        return $files;
    }
    public function up(){
        return $this->migrate(__FUNCTION__);         
    }
    public function down(){
        return $this->migrate(__FUNCTION__);
    }
    public function migrate(string $method){
        $files = $this->getfiles();
        $ctrl = $this->m_controller;
        $ns = $ctrl::ns('Database/Migrations');
        $tabcl = get_declared_classes();
        $tabc = count($tabcl);       
        $tabcl = get_declared_classes();
        $tabc = count($tabcl);
        $match = self::match;
        $builder = new SchemaBuilder;        
        $schema =   $builder->migrations();
        foreach($files as $c){
            preg_match($match, $c, $tab);
            $name = $tab['name'];
            include $c;
            self::_GetRealClassName($name, $tabc);          
            Logger::print('file: '.$c);
            $cl = igk_ns_name($ns."/".$name);            
            if (class_exists($cl, false)){
                $cl = new $cl();
                $cl->$method($schema);
            }else {
                igk_die('include file no class found .'.$name);
            }
        }
        Logger::info($builder->render());        
        // $builder->$method($this->controller);
        $node = HtmlReader::Load($builder->render(), "xml"); 
        $tab = igk_db_load_data_schemas_node($node, $this->m_controller); 
        if ($tab){
            SchemaBuilderHelper::Migrate($tab);
        }
    }
}