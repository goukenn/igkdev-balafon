<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerExtension.php
// @date: 20220728 17:08:32
// @desc: controller macro extension

namespace IGK\Controllers;

use Exception; 
use IGK\Database\DbLinkExpression;
use IGK\Models\Migrations;
use IGK\Models\ModelBase;
use IGK\System\Console\Logger;
use IGK\System\Http\Route;
use IGK\System\Http\RouteActionHandler;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKException;
use IGK\Database\DbSchemas; 
use IGK\Helper\StringUtility;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups; 
use IGK\System\Database\ColumnMigrationInjector; 
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Http\ControllerRequestNotFoundRequestResponse;
use IGK\System\Http\RequestResponse;
use IGK\System\Http\WebResponse; 
use IGKApplicationLoader;
use IGKEnvironment;
use IGKResourceUriResolver;
use IGKSysUtil;
use ReflectionMethod;
use SQLQueryUtils;
use Throwable;

///<summary>controller macros extension</summary>
/**
 * controller macros extension
 */
abstract class ControllerExtension
{
    /**
     * extends to get the base controller from class
     * @param BaseController $ctrl 
     * @return BaseController 
     */
    public static function ctrl(BaseController $ctrl)
    {
        return $ctrl;
    }

    public static function getDBConfigFile(BaseController $ctrl){
        
        igk_trace();
        exit;
    }
    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="fname"></param>
    ///<param name="css_def" default="null"></param>
    /**
     * 
     * @param mixed $t
     * @param mixed $fname
     * @param mixed $css_def the default value is null
     */
    public static function bindNodeClass(BaseController $ctrl, $t, $fname, $css_def = null)
    {

        $classdef = igk_css_str2class_name($fname) . ($css_def ? " " . $css_def : "");
        if ($ctrl->getEnvParam(IGK_KEY_CSS_NOCLEAR) == 1)
            return;

        $c = $t["class"];
        if ($c) {
            $c->Clear();
        }
        igk_ctrl_bind_css($ctrl, $t, $classdef);
    }
    /**
     * resolv asset path
     * @param BaseController $ctrl 
     * @param mixed $path 
     * @return mixed 
     */
    public static function asset(BaseController $ctrl, $path)
    {
        $f = implode("/", [$ctrl->getDataDir(), IGK_RES_FOLDER, $path]);
        if (!file_exists($f))
            return null;
        $t = IGKResourceUriResolver::getInstance()->resolve($f);
        if (empty($t)) {
            igk_ilog("Can't resolv file " . $f . " " . $t);
        }
        return $t;
    }
    public static function baseUri(BaseController $ctrl)
    {
        return igk_io_baseuri() === $ctrl->getAppUri() ?
            $ctrl->getAppUri() : igk_io_baseuri();
    }
    /**
     * get error files
     * @param BaseController $controller 
     * @param mixed $code 
     * @return string|null 
     */
    public static function getErrorViewFile(BaseController $controller, $code)
    {
        if (file_exists($f = $controller->getViewDir() . "/.error/" . $code . ".phtml")) {
            return $f;
        }
        // igk_wln_e(
        //     __FILE__.":".__LINE__, 
        //     "file: ".$f, 
        //     igk_io_collapse_path($f),
        //     igk_io_expand_path(igk_io_collapse_path($f))
        // );
        return null;
    }
    /**
     * return asset content if exists
     * @param BaseController $ctrl 
     * @param mixed $path 
     * @return string|false|void 
     */
    public static function asset_content(BaseController $ctrl, $path)
    {
        $f = implode("/", [$ctrl->getDataDir(), IGK_RES_FOLDER, $path]);
        if (file_exists($f)) {
            return file_get_contents($f);
        }
    }
    public static function configFile(BaseController $ctrl, $name)
    {
        return self::configDir($ctrl) . "/{$name}.php";
    }
    public static function configDir(BaseController $ctrl)
    {
        return $ctrl->getDeclaredDir() . "/Configs";
    }
    ///<summary>check that the controller can't be uses as entry controller</summary>
    ///<param name="ctrl">controller to check</param>
    /**
     * check that the controller can't be uses as entry controller
     * @param BaseController $ctrl controller to check
     */
    public static function IsEntryController(BaseController $ctrl)
    {


        return (\IGK\Helper\SysUtils::GetSubDomainCtrl() === $ctrl) || (igk_get_defaultwebpagectrl() === $ctrl);
    }
    public static function uri(BaseController $ctrl, string $name="")
    {
        return $ctrl->getAppUri($name);
    }
    public static function guid_name(BaseController $ctrl){
        static $guid ;
        if ($guid === null ){
            if (!file_exists($file = $ctrl->getDataDir()."/.id")){
                $guid = igk_create_guid();
                igk_io_w2file($file,$guid );            
            } else {
                $guid = file_get_contents($file);
            }
        }
        return $guid;
    }
    ///<summary>retrieve current view uri</summary>
    public static function furi(BaseController $ctrl, string $name="")
    {
        $fname = igk_getv(igk_get_view_args(), "fname");
        return $ctrl->getAppUri($fname . rtrim($name, '/'));
    }
    ///<summary>retrieve root base uri</summary>
    public static function buri(BaseController $ctrl, ?string $name="")
    {
        if(is_null($name)){
            if (igk_environment()->isDev()){
                igk_trace();
                igk_dev_wln_e("not handle");
            }
        }
        $uri = self::furi($ctrl, $name);
        $buri = igk_io_baseuri();
        if (strpos($uri, $buri) === 0) {
            $uri = substr($uri, strlen($buri));
        }
        return $uri;
    }
    /**
     * retrieve tablename
     * @param BaseController $ctrl 
     * @param mixed $tablename 
     * @return string|string[]|null 
     */
    public static function db_getTableName(BaseController $ctrl, $tablename){
        return IGKSysUtil::DBGetTableName($tablename, $ctrl);
    }
    public static function db_query(BaseController $ctrl, $query)
    {
        $ad = self::getDataAdapter($ctrl);
        return $ad->sendQuery($query);
    }

    public static function db_add_column(BaseController $ctrl, $table, $info, $after = null)
    {
        $ad = self::getDataAdapter($ctrl);
        ColumnMigrationInjector::Inject($ad, $table, [new ColumnMigrationInjector($info), "add"]);

        if (!$ad->grammar->exist_column($table, $info->clName)) {
            if ($query = $ad->grammar->add_column($table, $info, $after)) {
                if ($ad->sendQuery($query)) {
                    if ($info->clLinkType) {
                        $query_link = $ad->grammar->add_foreign_key($table, $info);
                        $ad->sendQuery($query_link);
                    }
                    //
                    return true;
                }
            }
        }
    }
    public static function db_rm_column(BaseController $ctrl, $table, $info)
    {
        $ad = self::getDataAdapter($ctrl);
        $is_obj = is_object($info);
        if ($is_obj) {
            $name = $info->clName;
        } else {
            $name = $info;
        }
        if ($ad->grammar->exist_column($table, $name)) {
            if (
                $is_obj && $info->clLinkType &&
                ($query = $ad->grammar->remove_foreign($table, $name))
            ) {
                $ad->sendQuery($query);
            }
            $query = $ad->grammar->rm_column($table, $name);
            return $ad->sendQuery($query);
        }
        return false;
    }

    public static function db_rename_column(BaseController $ctrl, $table, $column, $new_table)
    {
        $ad = self::getDataAdapter($ctrl);
        if ($v = $ad->grammar->exist_column($table, $column)) {
            if ($query = $ad->grammar->rename_column($table, $column, $new_table)) {
                return $ad->sendQuery($query);
            }
        }
        return false;
    }

    public static function db_change_column(BaseController $ctrl, $table, $info)
    {
        $ad = self::getDataAdapter($ctrl);
        if ($ad->grammar->exist_column($table, $info->clName)) {
            if ($query = $ad->grammar->change_column($table, $info)) {
                if ($r = $ad->sendQuery($query)) {
                    if ($info->clLinkType) {
                        $query_link = $ad->grammar->add_foreign_key($table, $info);
                        $ad->sendQuery($query_link);
                    }
                    return true;
                }
            }
        }
    }
    public static function cache_dir(BaseController $ctrl)
    {
        return implode(DIRECTORY_SEPARATOR, [igk_io_cachedir(), "projects", $ctrl->getName()]);
    }
    /**
     * resolv controller name key
     * @param BaseController $ctrl 
     * @param mixed $name array|string
     * @return mixed  array if name is array string name or null
     */
    public static function name(BaseController $ctrl, $name)
    {
        if (is_string($name)) {
            return implode("/", [get_class($ctrl), $name]);
        } else {
            if (is_array($name)) {
                $cl = get_class($ctrl);
                return array_map(function ($c) use ($cl) {
                    return implode("/", [$cl, $c]);
                }, $name);
            }
        }
    }

    /**
     * return notify key name
     */
    public static function notifyKey(BaseController $ctrl, $name = null)
    {
        return static::name($ctrl, "notify" . ($name ? "/" . $name : ""));
    }
    /**
     * return system controller hook name
     */
    public static function hookName(BaseController $ctrl, $name = null)
    {
        return static::name($ctrl, "hook" . ($name ? "/" . $name : ""));
    }

    public static function seed(BaseController $ctrl, $classname = null)
    {
        //get all seed class and run theme        
        if (igk_is_null_or_empty($classname)) {
            $classname = \Database\Seeds\DataBaseSeeder::class;
        } else {
            //try to resolv class 
            if (file_exists($ctrl->classdir() . "/Database/Seeds/" . $classname . ".php")) {
                $classname = "/Database/Seeds/" . $classname;
            } else {
                // + | seeder not found
                return false;
            }
        }

        $ctrl::register_autoload();
        $g = self::ns($ctrl, $classname);
        if (class_exists($g)) {
            Logger::info("run seed : " . $g);
            $o = new $g();
            return $o->run();
        } else {
            Logger::danger("class not found- : " . $g);
        }
    }
    public static function migrate(BaseController $ctrl, $classname = null)
    {

        if ($ctrl->getUseDataSchema()) {
            $file = $ctrl::getDataSchemaFile();
            $f = igk_db_load_data_schemas($file, $ctrl);
            if ($m = igk_getv($f, "migrations")) {
                try {
                    foreach ($m as $t) {
                        $t->upgrade();
                    }
                } catch (Exception $ex) {
                    Logger::danger(sprintf("some error : %s", $ex->getMessage()));
                    return false;
                }
            }
        }
        self::loadMigrationFile($ctrl);
        return true;
    }

    /**
     * load migration file
     * @param BaseController $ctrl 
     * @return int|false|void 
     */
    public static function loadMigrationFile(BaseController $ctrl)
    {
        // + | ----------------------------------------------------------------------
        // + | Load migration files
        // + |
        $rgx = "/^[0-9]{8}_[0-9]{4}_(?P<name>(" . IGK_IDENTIFIER_PATTERN . "))/i";
        //get all seed class and run theme
        $dir = $ctrl->getClassesDir() . "/Database/Migrations";
        $runbatch = 1;
        if (!$tab = igk_io_getfiles($dir, "/\.php/")) {
            return 0;
        }
        sort($tab);
        if ($m = Migrations::select_query(null, [
            "Columns" => [
                ["Max(`migration_batch`) as max"]
            ]
        ])) {
            $m = $m->getRows()[0];
            $runbatch = $m->max + 1;
        }
        foreach ($tab as $file) {
            $t = igk_io_basenamewithoutext($file);
            if (preg_match_all($rgx, $t, $tinf)) {

                $name = $tinf["name"][0];
                $cb = $ctrl::ns("Database/Migrations/{$name}");
                include_once($file);
                try {
                    if (!($cr = Migrations::select_row([
                        "migration_name" => $t
                    ])) || ($cr->migration_batch == 0)) {
                        Logger::info("init:" . $t);
                        (new $cb())->up();
                        if (!$cr) {
                            ($r = Migrations::create([
                                "migration_name" => $t,
                                "migration_batch" => $runbatch
                            ])) ?
                                Logger::success("complete:" . $t) :
                                Logger::danger("Failed to migrate: " . $t);

                            if (!$r) {
                                return false;
                            }
                        } else {
                            $r->migration_batch = $runbatch;
                            $r->update();
                        }
                    }
                } catch (Throwable $tex) {
                    Logger::print($tex->getMessage());
                    Logger::danger("failed to init: " . $t . ":" . $tex->getMessage());
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * resolv table name
     * @param BaseController $ctrl 
     * @param string $table 
     * @return string 
     */
    public static function resolv_table_name(BaseController $ctrl, string $table)
    {
        $ns = igk_db_get_table_name("%prefix%", $ctrl);
        $k = $table;
        $gs = !empty($ns) && strpos($k, $ns) === 0;
        $t =  $gs ? str_replace($ns, "", $k) : $k;
        $name = preg_replace("/\\s/", "_", $t);
        $name = implode("", array_map("ucfirst", array_filter(explode("_", $name))));
        return $name;
    }
    /**
     * get type name
     * @param string $t 
     * @return string 
     */
    private static function _GetTypeName(string $t):string{
        $name = preg_replace("/\\s/", "_", $t);
        $name = implode("", array_map("ucfirst", array_filter(explode("_", $name))));
        return $name;
    }
    /**
     * initialize controller database models
     * @param BaseController $ctrl 
     * @param bool $force 
     * @return void 
     * @throws IGKException 
     */
    public static function InitDataBaseModel(BaseController $ctrl, $force = false)
    {
        $core_model_base = igk_html_uri(IGK_LIB_CLASSES_DIR . "/Models/ModelBase.php");
        $c = $ctrl->getClassesDir() . "/Models/";
        $tb = $ctrl->getDataTableInfo();
        $ns = igk_db_get_table_name("%prefix%", $ctrl);
        $base_f = igk_html_uri($c . "ModelBase.php");

        if (($core_model_base != $base_f) && (!file_exists($base_f) || $force)) {
            igk_io_w2file($base_f, self::GetDefaultModelBaseSource($ctrl));
        }
        if ($tb) {
            foreach ($tb as $k => $v) {
                //remove prefix
                $gs = !empty($ns) && strpos($k, $ns) === 0;
                $t =  $gs ? str_replace($ns, "", $k) : $k;
                $name = self::_GetTypeName($t);
                //generate class name 

                $file = $c . $name . ".php";
                if (!$force && file_exists($file)) {
                    continue;
                }
                $table = $k;
                if ($gs) {
                    $table = "%prefix%" . $t;
                }
                igk_io_w2file($file, self::GetModelDefaultSourceDeclaration($name, $table, $v, $ctrl));
            }
        }
        self::InitDataInitialization($ctrl, false);
        self::InitDataSeeder($ctrl, false);
        self::InitDataFactory($ctrl, false);
    }

    public static function InitDataInitialization(BaseController $ctrl, $force = false)
    {
        //init database models
        $c = $ctrl->getClassesDir() . "/Database/InitData.php";
        if ($force || !file_exists($c)) {
            $ns = $ctrl::ns("Database");
            $builder = new PHPScriptBuilder();
            $builder->type("class")
                ->name("InitData")
                ->namespace($ns)
                ->author(igk_sys_getconfig("script_author", IGK_AUTHOR))
                ->uses([$cl = get_class($ctrl)])
                ->extends(\IGK\System\Database\InitBase::class)
                ->defs(implode(
                    "\n",
                    [
                        "public static function Init(" . basename($cl) . " \$controller){",
                        "\t// + | itialize your data base",
                        "}"
                    ]
                ));
            igk_io_w2file($c, $builder->render());
        }
    }
    public static function InitDataSeeder(BaseController $ctrl, $force = false)
    {
        //init database models
        // $force = 1;
        $c = $ctrl->getClassesDir() . "/Database/Seeds/DataBaseSeeder.php";
        if ($force || !file_exists($c)) {

            $ns = $ctrl::ns("Database/Seeds");


            $builder = new PHPScriptBuilder();
            $builder->type("class")
                ->name("DataBaseSeeder")
                ->namespace($ns)
                ->author(igk_sys_getconfig("script_author", IGK_AUTHOR))
                ->uses([$cl = get_class($ctrl)])
                ->desc("database seeder")
                ->extends(\IGK\System\Database\Seeds\SeederBase::class)
                ->defs(implode(
                    "\n",
                    [
                        "public function run(){",
                        "}"
                    ]
                ));
            igk_io_w2file($c, $builder->render());
        }
    }
    public static function InitDataFactory(BaseController $ctrl, $force = false)
    {
        //init database models

        $c = $ctrl->getClassesDir() . "/Database/Factories/FactoryBase.php";
        if ($force || !file_exists($c)) {

            $ns = $ctrl::ns("Database/Factories");


            $builder = new PHPScriptBuilder();
            $builder->type("class")
                ->name("FactoryBase")
                ->namespace($ns)
                ->author(igk_sys_getconfig("script_author", IGK_AUTHOR))
                ->uses([
                    get_class($ctrl),
                    \IGK\System\Database\Factories\FactoryBase::class => "Factory"
                ])
                ->desc("factory base")
                ->doc("Factory base")
                ->class_modifier("abstract")
                ->extends(\IGK\System\Database\Factories\FactoryBase::class)
                ->defs(implode(
                    "\n",
                    []
                ));
            igk_io_w2file($c, $builder->render());
        }
    }
    public static function register_autoload(BaseController $ctrl)
    {
        // die(__METHOD__ . " - not implement - ");
        $ns = $ctrl->getEntryNameSpace();
        $cldir = $ctrl->getClassesDir();
        $loader =  IGKApplicationLoader::getInstance();
        $loader->registerLoading($ns, $cldir);
        if (defined('IGK_TEST_INIT')) {
            $cldir = $ctrl->getTestClassesDir();
            $loader->registerLoading($ns . "\\Tests", $cldir);
        }
        if (file_exists($_auto_file = realpath($cldir . "/../autoload.php"))) {
            require_once($_auto_file);
        }
        igk_hook($ctrl::hookName("register_autoload"), [$ctrl]);
    }
    public static function ns(BaseController $ctrl, $path)
    {
        $cl = $path;
        if ($ns = $ctrl->getEntryNamespace()) {
            $cl = implode("/", array_filter([$ns, $cl]));
        }
        $cl = str_replace("/", "\\", $cl);
        return $cl;
    }
    /**
     * resolv class from controller entry namespace
     * @param BaseController $ctrl 
     * @param mixed $path 
     * @return string|null return the resolved class path
     */
    public static function resolvClass(BaseController $ctrl, $path)
    {
        $cl = $ctrl::ns($path);
        $ctrl::register_autoload();
        // igk_debug_wln("class ", $cl);
        if (class_exists($cl, false) || IGKApplicationLoader::TryLoad($cl)) {
            return $cl;
        }
        return null;
    }
    public static function getAutoresetParam(BaseController $ctrl, $name, $default = null)
    {
        $d = $ctrl->getParam($name, $default);
        $ctrl->setParam($name, null);
        return $d;
    }
    public static function getDisplayName(BaseController $ctrl)
    {
        return $ctrl->getConfigs()->get("clDisplayName", get_class($ctrl));
    }

    /**
     * 
     * @param mixed $cinfo 
     * @return string 
     */
    private static function _GetTypeFromInfo($cinfo){
        $p = ["mixed"];
        if ($cinfo->clLinkType){
            $p[] = self::_GetTypeName($cinfo->clLinkType);
        } 
        $p[] = strtolower($cinfo->clType);
        return implode("|", $p);
    }
    private static function GetModelDefaultSourceDeclaration($name, $table, $columnInfo, $ctrl)
    {
        $ns =  self::ns($ctrl, "");
        $uses = [];
        $gc = 0;
        $extends = implode("\\", array_filter([$ns, "Models\\ModelBase"]));
        $c = $ctrl->getClassesDir() . "/Models/";
        if (($name != "ModelBase") && file_exists($c . "/ModelBase.php")) {
            $uses[] =  implode("\\", array_filter([$ns, "Models\\ModelBase"]));
            $gc = 1;
        } else {
            $uses[] = ModelBase::class;
        }
        $o = "/**\n* table's name\n*/\n";
        $o .= "protected \$table = \"{$table}\"; " . PHP_EOL;

        if (!$gc && $ctrl) {
            $cl = get_class($ctrl);
            $uses[] = "$cl::class";
            $o .= "\t/**\n\t */\n";
            $o .= "\tprotected \$controller = {$cl}::class; " . PHP_EOL;
        }
        if (($columnInfo instanceof  \IGK\Database\DbColumnInfo)){
            $columnInfo = ["ColumnInfo"=> [$columnInfo]];
        }
        $key = "";
        $refkey = "";
        $php_doc = ""; 
        foreach ($columnInfo["ColumnInfo"] as $cinfo) {
            if ($cinfo->clIsPrimary) {
                if (!empty($key)) {
                    if (!is_array($key)) {
                        $key = [$key => $key];
                    }
                    $key[$cinfo->clName] =  $cinfo->clName;             
                } else {
                    $key = $cinfo->clName;
                }
            }
            if ($cinfo->getIsRefID()) {
                $refkey = $cinfo->clName;
            }

            // + get property type
            $pr_type =  self::_GetTypeFromInfo($cinfo); 

            $php_doc .= "@property ".$pr_type." $" . $cinfo->clName . "\n";
        }
        if ($key != "clId") {
            if (is_array($key)) {
                $key = "['" . implode("','", array_keys($key)) . "']";
            } else {
                $key = "\"{$key}\"";
            }
            $o .= "/**\n*override primary key \n*/\n";
            $o .= "protected \$primaryKey = $key;" . PHP_EOL;
        }
        if (!empty($refkey) && ($refkey != "clId")) {
            $o .= "/**\n*override refid key \n*/\n";
            $o .= "protected \$refId = \"{$refkey}\"; " . PHP_EOL;
        }
        $base_ns = implode("\\", array_filter([$ns, "Models"]));
        $builder = new PHPScriptBuilder();
        $builder->type("class")
            ->author(IGK_AUTHOR)
            ->extends($extends)
            ->name($name)
            ->namespace($base_ns)
            ->defs($o)
            ->file($name . ".php")
            ->desc("model file")
            ->phpdoc(rtrim($php_doc))
            ->uses($uses);

        $cf = $builder->render();      
        return $cf;
    }
    private static function GetDefaultModelBaseSource(BaseController $ctrl)
    {
        $o = "";
        $cl = "";
        if ($ctrl) {
            $cl = get_class($ctrl);
        }
        $ns = implode("\\", array_filter([self::ns($ctrl, ""), "Models"]));
        $o = "<?php " . PHP_EOL;
        $o .= implode("\n", array_filter([
            "// @author: " . IGK_AUTHOR,
            "// @date: " . date("Ymd H:i:s")
        ])) . "\n";

        if ($ns) {
            $o .= "namespace $ns; " . PHP_EOL;
        }
        $o .=  "use " . ModelBase::class . " as Model;" . PHP_EOL;
        if ($cl) {
            $o .=  "use {$cl};" . PHP_EOL;
        }

        $o .= "\n\n/** \n */\n";
        $o .= "abstract class ModelBase extends Model {" . PHP_EOL;

        if ($cl) {
            $o .= "\t/**\n\t * source controller \n\t */\n";
            $o .= "\tprotected \$controller = \\{$cl}::class; " . PHP_EOL;
        }
        $o .= "}" . PHP_EOL;
        return $o;
    }

    public static function getCacheInfo(BaseController $ctrl)
    {
        return implode("|", [
            get_class($ctrl),
            $ctrl->getConfigs()->clRegisterName,
            $ctrl->getName()
        ]);
    }

    /**
     * get array ove view argument
     */
    public static function getViewArgs(BaseController $ctrl)
    {
        $view_args = [];
        foreach ($ctrl->getSystemVars() as $k => $v) {
            $view_args[$k] = $v;
        }
        return $view_args;
    }
    /**
     * set extra argument. that live on controller view context
     * @param BaseController $ctrl 
     * @param null|array $args 
     * @return void 
     */
    public static function setExtraArgs(BaseController $ctrl, ?array $args=null){
        if (is_null($args )){
            $ctrl->setEnvParam(BaseController::VIEW_EXTRA_ARGS, null);
            return;
        }
        $v = $ctrl->getEnvParam(BaseController::VIEW_EXTRA_ARGS);
        if (!$v){
            $v = $args;
        }else{
            $v = array_merge($v, $args);
        }
        $ctrl->setEnvParam(BaseController::VIEW_EXTRA_ARGS, $v);
    }
    /**
     * get extra args
     * @param null|string $name 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public static function getExtraArgs(BaseController $ctrl, ?string $name=null, $default=null){
        if ($g = $ctrl->getEnvParam(BaseController::VIEW_EXTRA_ARGS)){
            return is_null($name) ? $g : igk_getv($g, $name, $default);
        }
        return $default;
    }
    /**
     * login a selected user 
     * @param BaseController $ctrl 
     * @param mixed $user login or object
     * @param mixed $pwd password to check
     * @param bool $nav navigate
     * @return bool 
     * @throws IGKException 
     */
    public static function login(BaseController $ctrl, $user = null, $pwd = null, $nav = true)
    {
        $u = $user;
        // igk_trace();
        // igk_wln_e(__FILE__.":".__LINE__,  "login, $user, $pwd" , $ctrl->User, "uri?".igk_app_is_uri_demand($ctrl, __FUNCTION__));

        if (!igk_environment()->viewfile && igk_app_is_uri_demand($ctrl, __FUNCTION__) && file_exists($file = $ctrl->getViewFile(__FUNCTION__, false))) {
            $ctrl->loader->view($file, compact("u", "pwd", "nav"));
            return false;
        }
        $c = igk_getctrl(IGK_USER_CTRL);
        $sysuser = $c->getUser();
        $f = 0; // update last connection 
        if ($ctrl->User === null) {
            if (is_object($u)) {
                if (($u instanceof \IGK\Models\Users) && !$u->is_mock()) {
                    $u = $u->to_array();
                }
                if (igk_is_array_key_present($u, array("clLogin", "clPwd"))) {
                    $c->setUser((object)$u);
                    $ctrl->checkUser(false);
                    $f = !$sysuser ||  ($sysuser->clId != $u["clId"]);
                }
            } else {
                if ($c->connect($u, $pwd)) {
                    $ctrl->checkUser(false);
                    $f = 1;
                }
            }
        }
        if ($f) {
            $uid = $ctrl->User->clId;
            $ctrl->User->bclLastLogin = $ctrl->User->clLastLogin;
            $ctrl->User->clLastLogin = date(\IGKConstants::MYSQL_DATETIME_FORMAT);
     
            \IGK\Models\Users::update(
                [
                    "clLastLogin" => $ctrl->User->clLastLogin,                  
                ], 
                ["clId" => $uid]
            ); 
        } else {
            igk_notifyctrl("notify/app/login")->addErrorr("e.loginfailed");
        }

        if ($nav) {
            if ($f) {
                ($b = igk_getr("goodUri")) || ($b = $ctrl->getAppUri());
                igk_navto($b);
            } else {
                $b = igk_getr("badUri") ?? $ctrl->getAppUri();
                if ($b) {
                    igk_navto($b);
                    igk_exit();
                }
            }
        }
        return $ctrl->User !== null;
    }
    public static function classdir(BaseController $controller)
    {
        return BaseController::Invoke($controller, "getClassesDir");
    }
    public static function libdir(BaseController $controller, $path = null)
    {
        return implode("/", array_filter([BaseController::Invoke($controller, "getLibDir"), $path]));
    }
    ///<summary>check user auth demand level</summary>
    /**
     * check user auth demand level
     */
    public static function IsUserAllowedTo(BaseController $controller, $authDemand = null)
    {
        $user = $controller->getUser();
        if ($user === null) {
            return false;
        }
        if ($user->clLevel == -1)
            return true;
        return $user->auth($authDemand);
    }

    ///<summary>View Error</summary>
    ///<param name="ctrl"></param>
    ///<param name="code"></param>
    /**
     * 
     * @param mixed $ctrl
     * @param mixed $code
     */
    public static function GetErrorView(BaseController $ctrl, $code)
    {
        return self::getErrorViewFile($ctrl, $code);
    }

    public static function getUser(BaseController $controller, $uid = null)
    {
        $u = $uid === null ? igk_app()->session->getUser() :
            igk_get_user($uid);
        return $u;
    }
    /**
     * get uri from base uri access
     * @param BaseController $controller 
     * @param mixed|null $function 
     * @return string 
     * @throws IGKException 
     */
    public static function getBaseFullUri(BaseController $controller, $function = null)
    {
        return igk_io_baseuri() . "/" . $controller->getUri($function);
    }
    public static function checkUser(BaseController $controller, $nav = true, $uri = null)
    {
        $r = true;
        $u = igk_app()->session->getUser();
        $ku = $controller->getUser();

        if ($ku == null) {
            if ($u != null) {
                $controller->User = $controller->initUserFromSysUser($u);
            } else
                $r = false;
        }
        if ($nav && !$r) {
            $m = igk_io_base_request_uri();
            $s = "";
            $u = ($uri == null ? $controller::uri("") : $uri);
            if (!empty($m)) {
                $s = "q=" . base64_encode($m);
                $u .= ((strpos($u, "?") === false) ? "?" : "&") . $s;
            }
            igk_navto($u);
        }
        return $r;
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function dropDb(BaseController $controller, $navigate = 1, $force = false)
    {
    
        $ctrl = $controller;

        $func = function () use ($ctrl) {
            $rdb = $ctrl->getDb();
            if ($rdb && method_exists($rdb, "onStartDropTable")) {
                $rdb->onStartDropTable();
            }
            igk_hook("sys://db/startdroptable", $ctrl);
        };

        $_vinit = 0;
        if ($force || $ctrl->getCanInitDb()) {


            if (!$ctrl->getConfigs()->clDataSchema) {
                $db = self::getDataAdapter($ctrl);
                if (
                    !empty($table = $ctrl->getDataTableName()) &&
                    $ctrl->getDataTableInfo() &&
                    $db && $db->connect()
                ) {
                    $table = igk_db_get_table_name($table, $ctrl);
                    $func();
                    $db->dropTable($table); // ctrl->getDataTableName());
                    $db->close();
                }
            } else {
                $tb = $ctrl::loadDataFromSchemas();
                $db = self::getDataAdapter($ctrl);             
                if ($db) {
                    if ($db->connect()) {
                        $v_tblist = [];
                        if ($tables = igk_getv($tb, "tables")) {
                            foreach (array_keys($tables) as $k) {
                                $v_tblist[$k] = $k;
                            }
                        }
                        $func();
                        $db->dropTable($v_tblist);                     
                        $db->close();
                        $_vinit = 1;
                    }
                } 

            }
        } 
        if ($navigate) {
            $controller->View();
            igk_navtocurrent();
        }
        return $_vinit;
    }


    public static function logout(BaseController $ctrl, $navigate = 1)
    {
        igk_app_is_uri_demand($ctrl, __FUNCTION__);
        $ctrl->setUser(null);
        igk_getctrl(IGK_USER_CTRL)->logout();

        if ($navigate) {
            igk_navto($ctrl->getAppUri());
        }
    }

    ///<summary>get authorisation key</summary>
    /**
     * get authorisation key
     */
    public static function getAuthKey(BaseController $controller, $k = null)
    {
        return igk_ctrl_auth_key($controller, $k);
    }

    /**
     * retrieve the configs
     * @param BaseController $controller 
     * @param string $name 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public static function getConfig(BaseController $controller, string $name, $default = null)
    {
        return $controller->getConfigs()->get($name, $default);
    }

    /**
     * init controller from function definition
     */
    public static function initDbFromFunctions(BaseController $controller)
    {
        $func_table = $controller->getDataTableName();
        if ($func_table === null) {
            return false;
        }
        $tbname = igk_db_get_table_name($func_table, $controller);
        if (empty($tbname)) {
            return false;
        }
        $v_tab = $controller->getDataTableInfo(); // ?? igk_db_get_table_info($tbname);

        if (!$v_tab)
            return;
        $db = self::getDataAdapter($controller);
        igk_hook(IGK_NOTIFICATION_INITTABLE, [$controller, $tbname, $v_tab]);
        try {
            $s = $db->createTable($tbname, $v_tab, null, null, $db->DbName);
            $controller::Invoke($controller, "initDataEntry", [$db, $tbname]);
        } catch (Exception $ex) {
            $db->close();
            igk_wln($ex->xdebug_message ?? $ex->getMessage());
            igk_wln_e("failed to create dbtable. " . get_class($controller) . " : " . $controller->getDeclaredFileName());
        }
    }


    public static function getInitDbConstraintKey(BaseController $controller)
    {
        $cl = str_replace("_", "",  str_replace("\\", "_", get_class($controller)));
        return $cl . "_ck_"; // constraint key
    }

    public static function getComponentsDir(BaseController $controller)
    {
        return $controller::classdir() . "/Components";
    }
    public static function getTestClassesDir(BaseController $controller)
    {
        return dirname($controller::classdir()) . "/" . IGK_TESTS_FOLDER;
    }

    public static function getEnvParamKey(BaseController $controller)
    {
        return "sys://ctrl/" . sha1(get_class($controller));
    }
    ///<summary>set environment parameter for this controller</summary>
    /**
     * set environment parameter for this controller
     */
    public static function setEnvParam(BaseController $controller, $key, $value)
    {
        return igk_environment()->setArray(self::getEnvParamKey($controller), $key, $value);
    }
    ///<summary>get environment parameter for this controller</summary>
    /**
     * get environment parameter for this controller
     */
    public static function getEnvParam(BaseController $controller, $key, $default = null)
    {
        return igk_environment()->getArray(self::getEnvParamKey($controller), $key, $default);
    }
    /**
     * @return string controller's environment key
     */
    public static function getEnvKey(BaseController $controller, $key)
    {
        return self::getEnvParamKey($controller) . "/" . $key;
    }

    /**
     * Routing macros
     * @param BaseController $controller 
     * @param mixed $routename 
     * @param mixed|null $path 
     * @return mixed 
     */
    public static function getRouteUri(BaseController $controller, $routename, $path = null)
    {
        if ($route = Route::GetRouteByName($routename)) {
            return RouteActionHandler::GetRouteUri($route, $controller, $path);
        }
        return null;
    }

    ///<summary>Dispatch model utility</summary>
    /**
     *  Dispatch model utility 
     * @param BaseController $controller 
     * @param mixed $modelname 
     * @param mixed $funcName 
     * @param mixed $args 
     * @return mixed 
     */
    public static function dispatchToModelUtility(BaseController $controller, $modelname, $funcName, ...$args)
    {
        if ($mod = $controller->loader->model($modelname)) {
            $func = $funcName;
            return $mod->$func(...$args);
        }
        return false;
    }

    ///<summary>get model utility</summary>
    /**
     * get model utility
     * @param BaseController $controller 
     * @param mixed $modelname 
     * @return mixed 
     */
    public function modelUtility(BaseController $controller, $modelname)
    {
        return $controller->loader->model($modelname);
    }

    /**
     * get model
     * @param BaseController $controller 
     * @param string $model 
     * @return ?ModelBase 
     */
    public static function model(BaseController $controller, string $model): ?ModelBase{
        $cl = $model;
        if (!class_exists($model) || !is_subclass_of($model, ModelBase::class )){
            if (!($cl = self::resolvClass($controller, "Models/$model"))){
                return null;
            }
        }
        return $cl::model();
    }

    ///<summary> initialize db from data schemas </summary>
    /**
     *  initialize db from data schemas
     */
    public static function initDbFromSchemas(BaseController $controller)
    {

        $r = $controller->loadDataAndNewEntriesFromSchemas();
        if (!$r)
            return;
        $tb = $r->Data;
        $db = self::getDataAdapter($controller);
        if ($db) {
            if ($db->connect()) {
                igk_db_init_dataschema($controller, $r, $db);
                $db->close();
            } else {
                igk_ilog("/!\\ connexion failed ");
            }
        } else {
            igk_log_write_i(__FUNCTION__, "no adapter found");
        }
        return $tb;
    }

    public static function getDataAdapter(BaseController $controller, $throw = true)
    {
        $db = igk_get_data_adapter($controller, $throw);
        return $db;
    }
    /**
     * init database constant file
     */
    public static function initDbConstantFiles(BaseController $controller)
    {
        $table = null;
        if (!$controller->getConfigs()->clDataSchema ){

           if (is_null($table = $controller->getDataTableName()))
                return;
        }


        $f = $controller->getDbConstantFile();
        $tb = $controller->getDataTableInfo();


        $s = "<?php" . IGK_LF;
        $s .= "// Balafon : generated db constants file" . IGK_LF;
        $s .= "// date: " . date(\IGKConstants::MYSQL_DATETIME_FORMAT) . IGK_LF;
        // generate class constants definition
        $cl = igk_html_uri(get_class($controller));
        $ns = dirname($cl);

        if (!empty($ns) && ($ns != ".")) {
            $s .= "namespace " . str_replace("/", "\\", $ns) . "; " . IGK_LF;
        }
        $s .= "abstract class " . basename($cl) . "DbConstants{" . IGK_LF;
        if (!is_null($table)){
            $tb = [$table=>$tb];
        }
        if ($tb != null) {
            ksort($tb);
            $prefix = igk_db_get_table_name("%prefix%", $controller);
            foreach (array_keys($tb) as $k) {
                $n = strtoupper($k);
                $n = preg_replace_callback(
                    "/^%prefix%/i",
                    function () {
                        return IGK_DB_PREFIX_TABLE_NAME;
                    },
                    $n
                );
                if ($prefix) {
                    $n = preg_replace("/^" . $prefix . "/i",  "TB_", $n);
                }
                if (empty($n)) {
                    continue;
                }
                $s .= "\tconst " . $n . " = \"" . $k . "\";" . IGK_LF;
            }
        } 
        $s .= "}" . IGK_LF;
        igk_io_w2file($f, $s, true);
        include_once($f);
    }

    public static function storeConfigSettings(BaseController $ctrl)
    {

        return $ctrl->getConfigs()->storeConfig();
    }

    /**
     * get database schema file
     * @param BaseController $ctrl 
     * @return string 
     */
    public static function getDataSchemaFile(BaseController $ctrl)
    {
       return $ctrl->getDataDir() . "/" . IGK_SCHEMA_FILENAME;       
    }

    /**
     * laod database from schema
     * @param BaseController $ctrl 
     * @return null|object 
     * @throws IGKException 
     */
    public static function loadDataFromSchemas(BaseController $ctrl)
    {
        return DbSchemas::LoadSchema(self::getDataSchemaFile($ctrl), $ctrl);
    }
    /**
     * get data table definition
     */
    public static function getDataTableDefinition(BaseController $ctrl, $tablename)
    { 
        if ($ctrl->getUseDataSchema()) {
            $info = null;
            if (!($info = &\IGK\Database\DbSchemaDefinitions::GetDataTableDefinition($ctrl->getDataAdapterName(), $tablename))) {
                if ($schema = self::loadDataFromSchemas($ctrl)) {
                    if (isset($schema->tables[$tablename])) {
                        $info = &$schema->tables[$tablename];
                        if (!isset($info["tableRowReference"])) {
                            //
                            // + | update data with table's row model reference info
                            //
                            $info["tableRowReference"] = igk_array_object_refkey(igk_getv($info, "ColumnInfo"), IGK_FD_NAME);
                        }
                        \IGK\Database\DbSchemaDefinitions::RegisterDataTableDefinition($ctrl->getDataAdapterName(), $tablename, $info);
                    }
                }
            }
            if ($info) {
                // $m =  & \IGK\Database\DbSchemaDefinitions::GetDataTableDefinition($ctrl->getDataAdapterName(), $tablename);
                igk_hook(\IGKEvents::FILTER_DB_SCHEMA_INFO, ["tablename" => $tablename, "info" => &$info]);
                // igk_wln_e("check???", 
                //  $info["ColumnInfo"] === $m["ColumnInfo"]
                // //  ,  \IGK\Database\DbSchemaDefinitions::GetDataTableDefinition($ctrl->getDataAdapterName(), $tablename)
                // );
            }
            return $info;
        } else {
            if ($ctrl->getDataTableName() == $tablename) {
                $clinfo = $ctrl->getDataTableInfo();
                $cinfo = [
                    "ColumnInfo" => $clinfo,
                    "tableRowReference" =>  igk_array_object_refkey($clinfo, IGK_FD_NAME)
                ];
                return $cinfo;
            }
        }
    }

    ///<summary></summary>
    /**
     * 
     */
    public static function getCanInitDb(BaseController $controller)
    {
        if (defined('IGK_DB_GRANT_CAN_INIT') || igk_is_cmd())
            return true;
        return igk_is_conf_connected();
    }

    public static function loadDataAndNewEntriesFromSchemas(BaseController $controller)
    {
        $obj = (object)array(
            "Data" => null,
            "Entries" => null,
            "Relations" => null,
            "RelationsDef" => null,
            "Migrations" => null,
            "Version" => 1
        );
        if ($data = self::loadDataFromSchemas($controller)) {
            if (count((array)$data) > 0) {
                $obj->Data = [];
                $obj->Relations = $data->tbrelations;
                $obj->RelationsDef = $data->relations;
                $obj->Migration = $data->migrations;

                foreach ($data->tables as $n => $t) {
                    if ($c = igk_getv($t, "Entries")) {
                        $obj->Entries[$n] =  $c;
                    }
                    $obj->Data[$n] = $t;
                }
            }
        }
        return $obj;
    }


    public static function getIsVisible(BaseController $controller)
    {
        return !igk_environment()->isInArray(IGKEnvironment::NOT_VISIBLE_CTRL, get_class($controller));
    }

    /**
     * check if a function is exposed
     * @param BaseController $controller 
     * @param string $function 
     * @return bool 
     */
    public static function IsFunctionExposed(BaseController $controller, string $function): bool
    {
        if (($function == __FUNCTION__) || !method_exists($controller, $function))
            return false;
        $refmethod = new ReflectionMethod($controller, $function);
        return $refmethod->isPublic() && ($refmethod->getDeclaringClass()->name == get_class($controller));
    }

    ///<summary>get the application current document</summary>
    /**
     * get the application current document
     */
    public static function getCurrentDoc(BaseController $controller)
    {
        return $controller->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY) ?? igk_app()->getDoc(); //  $controller->getAppDocument();
    }
    /**
     * bind controller style to document
     */
    public static function bindCssStyle(BaseController $controller, ?\IGKHtmlDoc $doc = null)
    {
        $doc = $doc ?? self::getCurrentDoc($controller);
        if ($doc && !empty($file = $controller->getPrimaryCssFile()))  
        {      
            // igk_ilog("try bind to bind primary files....".$file);
            return igk_ctrl_bind_css_file($controller, $doc, $file); 
        }
        //igk_ilog("failed to bind primary files....");
    }
    /**
     * project used controller's
     * @return void 
     */
    public static function getCanModify(BaseController $controller)
    {
        $pdir = igk_io_projectdir();
        $decdir = $controller->getDeclaredDir();      
        return $pdir && $decdir && !empty(strstr($decdir, $pdir));
    }


    /**
     * load controller route route config files
     * @param mixed $controller 
     * @return void 
     */
    public static function loadRoute(BaseController $controller)
    {
        if (file_exists($cf = $controller::configFile("routes"))) {
            $inc = function () {
                include_once(func_get_arg(0));
            };
            $inc($cf);
        }
    }

    public static function viewError(BaseController $controller, int $code, $params = [])
    {
        if (file_exists($f = $controller::getErrorViewFile($code))) {
            $node = igk_create_node("div");
            $controller->setEnvParam(BaseController::NO_ACTION_FLAG, 1);
            $controller->regSystemVars(null);
            $controller->setCurrentView($f, true, $node, array(
                "code" => $code,
                "params" => $params,
                "uri" => igk_io_request_uri()
            ));
            $n = new \IGK\System\Html\Dom\HtmlNode("html");
            $n->add("body")->add($node);
            igk_do_response(new \IGK\System\Http\WebResponse($n, $code));
        }
        return false;
    }

    /**
     * 
     * @param BaseController $controller 
     * @param string $file 
     * @param mixed $params 
     * @return void 
     * @throws IGKException 
     */
    public static function viewInContext(BaseController $controller, string $file, $params = null)
    {
        if (realpath($file) || file_exists($file = $controller->getViewFile($file))) {
            $bck = $controller->getSystemVars();
            if ($params) {
                $controller->regSystemVars($params);
                $key = igk_ctrl_env_view_arg_key($controller);

                $tparams = array_merge($bck, $params);
                igk_set_env($key, $tparams);
            }
            $controller->_include_file_on_context($file);
            $controller->regSystemVars($bck);
        }
    }

    public static function SaveDataSchemas(BaseController $controller)
    {
        $dom = HtmlNode::CreateWebNode(IGK_SCHEMA_TAGNAME);
        $dom["ControllerName"] = $controller->Name;
        $dom["Platform"] = IGK_PLATEFORM_NAME;
        $dom["PlatformVersion"] = IGK_WEBFRAMEWORK;
        $e = HtmlNode::CreateWebNode("Entries");
        $d = igk_getv($controller->loadDataFromSchemas(), "tables");
        if ($d) {
            $tabs = array();
            foreach ($d as $k => $v) {
                $b = $dom->add(DbSchemas::DATA_DEFINITION);
                $b["TableName"] = $k;
                $b["Description"] = $v["Description"];
                $tabs[] = $k;
                foreach ($v["ColumnInfo"] as $cinfo) {
                    $col = $b->add(IGK_COLUMN_TAGNAME);
                    $tb = (array)$cinfo;
                    $col->setAttributes($cinfo);
                }
            }
            $db = $controller::getDataAdapter();
            $r = null;
            if ($db) {
                $db->connect();
                foreach ($tabs as $tabname) {
                    try {
                        $r = $db->selectAll($tabname);
                        if ($r->RowCount > 0) {
                            $s = $e->add($tabname);
                            foreach ($r->Rows as $c => $cc) {
                                $irow = $s->addXMLNode(IGK_ROW_TAGNAME);
                                $irow->setAttributes($cc);
                            }
                        }
                    } catch (Exception $ex) {
                    }
                }
                $db->close();
            }
        }
        if ($e->HasChilds) {
            $dom->add($e);
        }
        return $dom;
    }
    /**
     * handle request 
     * @param string $uri 
     * @return null|RequestResponse 
     */
    public static function handle(BaseController $controller, string $uri, $method = 'GET', $args = null): ?RequestResponse
    {
        $tab = [];
        $tab = parse_url($uri);
        igk_server()->REQUEST_URI = $tab["path"];
        igk_server()->METHOD  = $method;
        igk_server()->REQUEST_QUERY  = $query = igk_getv($tab, "query");
        $response = null;
        //save state
        $state = [$_GET, $_POST, $_REQUEST];
        if ($query) {
            $tquery = [];
            parse_str($query, $tquery);
            $_REQUEST = $_GET = $tquery;
            if ($method == "POST") {
                $_POST = $tquery;
            }
        }
        try {
            $controller->setCurrentView('default/one/base/ok/', true, null, $args, ["Context" => "handle"]);
            $response = new WebResponse($controller->getTargetNode());
        } catch (Exception $ex) {
            $response = new ControllerRequestNotFoundRequestResponse($uri, $controller);
            $response->code = 500;
            $response->message = $ex->getMessage();
        } finally {
            // restore
            $_GET = $state[0];
            $_POST = $state[1];
            $_REQUEST = $state[2];
        }
        return $response;
    }
    /**
     * macros helper update controller database
     * @param BaseController $controller 
     * @return void 
     * @throws IGKException 
     */
    public static function updateDb(BaseController $controller)
    {
        $s = igk_is_conf_connected() || igk_user()->auth($controller->Name . ":" . __FUNCTION__);
        if (!$s) {
            igk_ilog("// not authorize to updateDb of " + $controller->getName());
            igk_navto($controller->getAppUri());
        }
        $schema = igk_db_backup_ctrl($controller);
        $dataxml = ($schema) ? $schema->render() : null;
        $controller->resetDb(0);
        if ($dataxml) {
            $error = [];
            igk_db_restore_backup_data($controller, $dataxml, $error);
        }
        $file = $controller->getDataDir() . "/dbbackup/" . date("YmdHis") . ".db.bck.xml";
        igk_io_w2file($file, $dataxml);
        $uri = $controller->getAppUri();
        igk_navto($uri);
        igk_exit();
    }


    
    /**
     * append tempory tyle file
     * @param BaseController $controller 
     * @param string|string[] $fname name or file
     * @param mixed $document 
     * @return never 
     */
    public static function pcss(BaseController $controller, $fname, $document=null){
        if (is_null($document)){
            //$document = igk_view_args() ?? die("document not found");
            $document = $controller->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY) ?? die("document not found");
        }
        if (is_string($fname)){
            $fname = explode("|", $fname);
        } 
        if (!is_array($fname)){
            igk_die("not a valid argument");
        }
        $q = $fname;
        $c = 0;
        while($fname = array_shift($q)){
            $f = $controller->getStylesDir()."/$fname";
            if (!preg_match("/\.(".IGK_DEFAULT_STYLE_EXT."|css)$/", $f)){
                $f .= ".".IGK_DEFAULT_STYLE_EXT;
            }
            if (file_exists($f)){
                $document->getTheme()->addTempStyle($controller, $f);
                $c++;
            }
        }
        return $c;
    }
    /**
    * load js script extension
    * @param BaseController $controler
    */
    public static function js(BaseController $controller, $fname, $document=null){
        if (is_null($document)){
            $document = $controller->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY) ?? die("document not found");
        }
        if (is_string($fname)){
            $fname = explode("|", $fname);
        } 
        if (!is_array($fname)){
            igk_die("not a valid argument");
        }

        $q = $fname;
        $c = 0;
        while($fname = array_shift($q)){
            $f = $controller->getScriptsDir()."/$fname";
            if (!preg_match("/\.(js)$/", $f)){
                $f .= ".js";
            }
            if (file_exists($f)){
                $document->addTempScript($f);
                $c++; 
            }
        }
        return $c;
    }

    /**
     * include in
     * @param BaseController $controller 
     * @param string $inc 
     * @return void 
     */
    public static function inc(BaseController $controller, string $inc, $args = null){
        $cf = \IGK\Helper\ViewHelper::Dir()."/".$inc;
        if (is_null($args)){
            $args = $controller->getViewArgs();
        } 
        foreach(["",".pinc"] as $ext){            
            if (file_exists($g = $cf.$ext)){
                return igk_include($g, $args);   
            }
        }            
    }
    /**
     * init groups for base controller
     * @param BaseController $controller 
     * @param array $groups 
     * @return void 
     */
    public static function initGroups(BaseController $controller, array $groups){
        $cl = $controller->getName() ?? "/".igk_html_uri(get_class($controller));
        foreach ($groups as $value) {
            $name = $controller::name($value);
            $group = Groups::createIfNotExists(["clName"=>$name, "clController"=>$cl]);   
            $c = Authorizations::createIfNotExists(["clName"=>$name]);
            if (!$c || !$group)
                continue;
            Groupauthorizations::createIfNotExists([
                "clAuth_Id"=>$c->clId, 
                "clGrant"=>1, 
                "clGroup_Id"=>new DbLinkExpression(
                    Groups::table(),
                    "clName", 
                    $name)
            ]);
        }
    }

    /**
     * get action handler 
     * @param BaseController $controller 
     * @return mixed 
     * @throws IGKException 
     */
    public static function getActionHandler(BaseController $controller, string $name){
     
        if (igk_env_count(__METHOD__.$name) > 1) {
            igk_trace();
            igk_die(__METHOD__." call twice ".$name);
        } 

        if (($name != IGK_DEFAULT_VIEW) && preg_match("/" . IGK_DEFAULT_VIEW . "$/", $name)) {
            $name = rtrim(substr($name, 0, -strlen(IGK_DEFAULT_VIEW)), "/");
        }
        $ns = $controller->getEntryNameSpace();
        $c = [];
        $t = [];
        if (!empty($ns)) {
            $c[] = $ns;
        }
        $m = "";
        $sep = "";
        foreach (explode("/", $name) as $r) {
            $m .= $sep . StringUtility::CamelClassName(ucfirst($r));
            array_unshift($t, implode("\\", array_filter(array_merge($c, ["Actions\\" . $m . "Action"]))));
            $sep = "\\";
        }

        if (($name != IGK_DEFAULT_VIEW) && !$controller->getConfig("no_fallback_to_default_action")) {
            $t[] = implode("\\", array_filter(array_merge([$ns], ["Actions\\" . ucfirst(IGK_DEFAULT_VIEW) . "Action"])));
        }
        $classdir = $controller->getClassesDir();
        $sublen = strlen($ns) + 1;

        while ($cl = array_shift($t)) {
            $fcl = $cl;
            if (!empty($ns) && (strpos($cl, $ns . "\\") === 0)) {
                $fcl = substr($cl, $sublen);
            }
            $f = igk_io_dir(implode("/", [$classdir, $fcl . ".php"]));
            // igk_wln("try : ".$f);
            if (file_exists($f) && class_exists($cl)) {
                return $cl;
            }
        }
        return null;
    
    }

    public static function showError(BaseController $controller, string $message, string $title, $code=400){
        $out = <<<HTML
<html>
    <head>
        <title>${title}</title>
    </head>
    <body>
        ${message}
    </body>
</html>
HTML;
        igk_do_response(new WebResponse($out, $code));        
    }
}
