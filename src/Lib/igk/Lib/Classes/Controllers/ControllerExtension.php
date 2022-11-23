<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerExtension.php
// @date: 20220728 17:08:32
// @desc: controller macro extension

namespace IGK\Controllers;

use com\igkdev\projects\app_balafon\Database\InitDbSchemaBuilder;
use Exception;
use IGK\App\Bondje\Models\DbUtility;
use IGK\Database\DbLinkExpression;
use IGK\Models\Migrations;
use IGK\Models\ModelBase;
use IGK\System\Console\Logger;
use IGK\System\Http\Route;
use IGK\System\Http\RouteActionHandler;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKException;
use IGK\Database\DbSchemas;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;
use IGK\System\Database\ColumnMigrationInjector;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Http\ControllerRequestNotFoundRequestResponse;
use IGK\System\Http\RequestResponse;
use IGK\System\Http\WebResponse;
use IGK\ApplicationLoader;
use IGK\Controllers\Traits\ControllerAtricleManagerExtensionTrait;
use IGK\Database\DbColumnInfoPropertyConstants;
use IGK\Database\DbSchemasConstants;
use IGK\Database\SchemaBuilder\DiagramEntityAssociation;
use IGK\Database\SchemaBuilder\DiagramVisitor;
use IGK\Database\SchemaBuilder\SchemaDiagramVisitor;
use IGK\Helper\DbUtilityHelper;
use IGK\Helper\SysUtils;
use IGK\System\Caches\DBCaches;
use IGK\System\Database\IDatabaseHost;
use IGK\System\Database\MigrationHandler;
use IGK\System\Database\MySQL\Controllers\DbConfigController;
use IGKEnvironment;
use IGKEvents;
use IGKResourceUriResolver;
use IGKSysUtil as sysutil;
use IGKValidator;
use ReflectionException;
use ReflectionMethod;
use Throwable;

require_once IGK_LIB_CLASSES_DIR . '/Controllers/Traits/ControllerAtricleManagerExtensionTrait.php';

///<summary>controller macros extension</summary>
/**
 * controller macros extension
 */
abstract class ControllerExtension
{
    use ControllerAtricleManagerExtensionTrait;
    public static function getBaseDir(BaseController $ctrl)
    {
        return null;
    }
    /**
     * extends to get the base controller from class
     * @param BaseController $ctrl 
     * @return BaseController 
     */
    public static function ctrl(BaseController $ctrl)
    {
        return $ctrl;
    }

    public static function getDBConfigFile(BaseController $ctrl)
    {

        igk_trace();
        exit;
    }
    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="fname"></param>
    ///<param name="css_def" default="null"></param>
    /**
     * bind target node
     * @param HtmlNode $t
     * @param mixed $fname
     * @param mixed $css_def the default value is null
     */
    public static function bindNodeClass(BaseController $ctrl, HtmlNode $t, $fname, $css_def = null)
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
        $f = $ctrl->getAssetsDir(); 
        if (!file_exists($f))
            return null;
        $t = IGKResourceUriResolver::getInstance()->resolve($f);
        if (empty($t)) {
            igk_ilog("Can't resolv file " . $f . " " . $t);
        }
        return $t;
    }
    public static function getAssetsDir(BaseController $ctrl, ?string $path = null){
        return implode("/", array_filter([$ctrl->getDataDir(), IGK_RES_FOLDER, $path]));
    }
    /**
     * return an array of views
     * @param BaseController $ctrl 
     * @return array 
     */
    public static function getViews(BaseController $ctrl, bool $withHidden = false, bool $recursive = false): array
    {
        $gf = $ctrl->getViewDir();
        $tab = [];
        $ln = strlen($gf);
        foreach (IO::GetFiles($gf, "/\.phtml/i", $recursive) as $bf) {
            $n = substr($bf, $ln + 1);
            $p = dirname($n);
            $tn = igk_io_basenamewithoutext($n);
            if (empty($tn)) {
                continue;
            }
            if (!$withHidden) {
                if ($tn[0] == '.') // hidden view
                    continue;
                if (($p != ".") &&  ($p[0] == ".")) // hidden folder 
                    continue;
            }
            if ($p == ".")
                $p = "";
            else
                $p .= "/";
            $tab[] = $p . $tn;
        }
        return $tab;
    }
    public static function resolvAssetUri(BaseController $ctrl)
    {
        $f = implode("/", [$ctrl->getDataDir(), IGK_RES_FOLDER]);
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
        if (file_exists($f = $controller->getViewDir() . "/.error/" . $code . IGK_VIEW_FILE_EXT)) {
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
    public static function uri(BaseController $ctrl, ?string $name = "")
    {
        return $ctrl->getAppUri($name ?? '');
    }
    public static function guid_name(BaseController $ctrl)
    {
        static $guid;
        if ($guid === null) {
            if (!file_exists($file = $ctrl->getDataDir() . "/.id")) {
                $guid = igk_create_guid();
                igk_io_w2file($file, $guid);
            } else {
                $guid = file_get_contents($file);
            }
        }
        return $guid;
    }
    ///<summary>retrieve current view uri</summary>
    /**
     * retrieve current view uri
     * @param BaseController $ctrl 
     * @param string $name 
     * @return null|string 
     * @throws IGKException 
     */
    public static function furi(BaseController $ctrl, string $name = "")
    {
        $fname = igk_getv(igk_get_view_args(), "fname");
        return $ctrl->getAppUri($fname . rtrim($name, '/'));
    }
    /**
     * get resolve uri
     * @param BaseController $ctrl 
     * @param string $uri uri string
     * @return string 
     * 
     *   
     * [scheme://[domain]]PATH
     * in case PATH start with buri return $s
     */
    public static function ruri(BaseController $ctrl, ?string $uri = null): string
    {
        if ($ctrl && !empty($uri) && !IGKValidator::IsUri($uri)) {
            if (strpos($uri, $ctrl::buri("/")) === 0)
                return $uri;
            $uri = ltrim($uri, "/");
            $uri = $ctrl->getAppUri($uri);
        }
        return $uri;
    }
    ///<summary>retrieve root base uri</summary>
    /**
     * retrieve root base uri
     * @param BaseController $ctrl 
     * @param string $path 
     * @return null|string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function buri(BaseController $ctrl, string $path = ""): string
    {
        if (is_null($path)) {
            if (igk_environment()->isDev()) {
                igk_trace();
                igk_dev_wln_e(__FILE__ . ":" . __LINE__, "buri not handle");
            }
        }
        $uri = self::furi($ctrl, $path);
        $buri = igk_io_baseuri();
        if (strpos($uri, $buri) === 0) {
            $uri = substr($uri, strlen($buri));
            if ($path == "/") {
                $uri .= $path;
            }
        }
        return $uri;
    }
    /**
     * get controller uri name 
     * @param BaseController $ctrl 
     * @return string 
     */
    public static function uri_name(BaseController $ctrl)
    {
        $g = $ctrl->getName();
        return base64_encode($g);
    }
    /**
     * retrieve tablename
     * @param BaseController $ctrl 
     * @param mixed $tablename 
     * @return string|string[]|null 
     */
    public static function db_getTableName(BaseController $ctrl, $tablename)
    {
        return sysutil::DBGetTableName($tablename, $ctrl);
    }
    public static function db_query(BaseController $ctrl, $query, $throwException)
    {
        $ad = self::getDataAdapter($ctrl);
        return $ad->sendQuery($query, false);
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
     * get default autorization name
     * @param BaseController $ctrl 
     * @param string $name 
     * @return mixed 
     */
    public static function authName(BaseController $ctrl, string $name):string{
        return StringUtility::AuthorizationPath($name, get_class($ctrl));
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
    /**
     * migrate items loaded from schema
     * @param BaseController $ctrl 
     * @param mixed $classname 
     * @return bool 
     * @throws IGKException 
     */
    public static function migrate(BaseController $ctrl, $classname = null)
    {
        if (!$ctrl->getCanInitDb()) {
            return false;
        }
        if ($ctrl->getUseDataSchema()) {
            $file = $ctrl->getDataSchemaFile();
            $f = igk_db_load_data_schemas($file, $ctrl);
            if ($m = igk_getv($f, "migrations")) {
                $v_upgrade = false;
                try {
                    foreach ($m as $t) {
                        $v_upgrade = true;
                        $t->upgrade();
                    }
                } catch (Exception $ex) {
                    Logger::danger(sprintf("some error : %s", $ex->getMessage()));
                    return false;
                }
                if ($v_upgrade){
                    // upgrade model file definition
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
     * resolv table name to class model
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
        $name = $t;
        $name = preg_replace("/\\s/", "_", $t);
        $name = implode("", array_map("ucfirst", array_filter(explode("_", $name))));
        return $name;
    }
    private static function _GetEntryModelDirectory(BaseController $ctrl)
    {
        $cldir = $ctrl->getClassesDir();
        if ($ctrl instanceof DbConfigController) {
            $cldir = IGK_LIB_CLASSES_DIR;
        }
        $c  = $cldir . "/Models/";
        return $c;
    }
    /**
     * initialize controller database models
     * @param BaseController $ctrl 
     * @param array $array of table definitions
     * @param bool $force 
     * @return void 
     * @throws IGKException 
     */
    public static function InitDataBaseModel(BaseController $ctrl, array $definitions,  $force = false)
    {
        $c  = self::_GetEntryModelDirectory($ctrl);
        $core_model_base = igk_uri(IGK_LIB_CLASSES_DIR . "/Models/ModelBase.php");  
        $tb = $definitions; 
        $base_f = igk_uri($c . "ModelBase.php");
        if (($core_model_base != $base_f) && (!file_exists($base_f) || $force)) {
            Logger::info("generate : " . $base_f);
            igk_io_w2file($base_f, self::GetDefaultModelBaseSource($ctrl));
        }
        $factory = [];
        if ($tb) {
            foreach ($tb as $v) {
                // remove prefix
                $table = null; 
                $name = sysutil::GetModelTypeNameFromInfo($v, $table);
                if (!empty($name)) {                    
                    $file = $c . $name . ".php";
                    $factory[] = $name;
                    if (!$force && file_exists($file)) {
                        continue;
                    }
                    if ($definitionHandler = $v->definitionResolver){
                        Logger::info("generate model : " . $file);
                        igk_io_w2file($file,  $definitionHandler->getModelDefaultSourceDeclaration($name, $table, $v, $ctrl, $v->description));
                    }
                }
            }
        }
        self::InitDataInitialization($ctrl, $force);
        self::InitDataFactory($ctrl, $force, $factory);
        self::InitDataSeeder($ctrl, $force);
    }

    public static function InitDataInitialization(BaseController $ctrl, $force = false)
    {
        //init database models
        $c  = (!($ctrl instanceof DbConfigController) ? $ctrl->getClassesDir() : IGK_LIB_CLASSES_DIR)
            . "/Database/InitData.php";
        if (!file_exists($c)) {
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
        $c  = (!($ctrl instanceof DbConfigController) ? $ctrl->getClassesDir() : IGK_LIB_CLASSES_DIR)
            . "/Database/Seeds/DataBaseSeeder.php";
        // $c = $ctrl->getClassesDir() . "/Database/Seeds/DataBaseSeeder.php";
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
                        "// + | DATABASE Seeder",
                        "// + | [model]::factory(number)->create();",
                        "}"
                    ]
                ));
            igk_io_w2file($c, $builder->render());
        }
    }
    public static function InitDataFactory(BaseController $ctrl, bool $force = false, ?array $factories = null)
    {
        //init database models
        $c  = (!($ctrl instanceof DbConfigController) ? $ctrl->getClassesDir() : IGK_LIB_CLASSES_DIR)
            . "/Database/Factories/FactoryBase.php";
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

        if ($factories) {
        }
    }
    /**
     * register autoload class for controller
     * @param BaseController $ctrl 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function register_autoload(BaseController $ctrl)
    {
        $ns = $ctrl->getEntryNameSpace();
        $cldir = $ctrl->getClassesDir();
        $loader =  ApplicationLoader::getInstance();
        if ($loader->registerLoading($ns, $cldir)) {
            if (defined('IGK_TEST_INIT')) {
                $cldir = $ctrl->getTestClassesDir();
                $loader->registerLoading($ns . "\\Tests", $cldir);
            }
            $_auto_file = dirname($cldir) . "/autoload.php";
            if (file_exists($_auto_file)){
                if (!igk_environment()->NO_PROJECT_AUTOLOAD ){
                    require_once($_auto_file);
                } else {
                    $fc = function($e)use($ctrl, $_auto_file){
                        self::_InitDbComplete($ctrl, $_auto_file, $e);
                    };
                    igk_reg_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, $fc); 
                    $ctrl->setEnvParam("register_autoload_callback", $fc);
                }
            }
            igk_hook($ctrl::hookName("register_autoload"), [$ctrl]);
        }
    }
    private static function _InitDbComplete(BaseController $ctrl, string $file,  $e){
        require_once($file);
        $fc = $ctrl->getEnvParam("register_autoload_callback");
        $fc && igk_unreg_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, $fc); 
        $ctrl->setEnvParam("register_autoload_callback", null);
    }
    /**
     * get entry namespace
     * @param BaseController $ctrl 
     * @param mixed $path 
     * @return string 
     */
    public static function ns(BaseController $ctrl, $path)
    {
        $cl = ltrim($path, "/");
        if ($ns = $ctrl->getEntryNamespace()) {
            if (strpos($cl, $ns) !== 0) {
                //start with entry namespace
                $cl = implode("/", array_filter([$ns, $cl]));
            }
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
        $cl = self::ns($ctrl, $path);
        //$ctrl::register_autoload();
        if (class_exists($cl, false) || ApplicationLoader::TryLoad($cl)) {
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
     * get entry namespace
     * @param BaseController $ctrl 
     * @return void 
     */
    public static function getEntryNamespace(BaseController $ctrl)
    {
        if (BaseController::IsSystemController($ctrl)) {
            return \IGK::class;
        }
        $ns = dirname(igk_dir(get_class($ctrl)));
        if ($ns != ".") {
            return str_replace("/", "\\", $ns);
        }
        return SysUtils::GetProjectEntryNamespace($ctrl->getDeclaredDir());
    }

    /**
     * 
     * @param mixed $cinfo 
     * @return string 
     */
    private static function _GetTypeFromInfo($cinfo, $ctrl)
    {
        $p = ["mixed"];
        if ($cinfo->clLinkType) {
            $p[] = "\\" . sysutil::GetModelTypeName($cinfo->clLinkType, $ctrl);
        }
        $p[] = strtolower($cinfo->clType);
        return implode("|", $p);
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
     * get array view argument
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
    public static function setExtraArgs(BaseController $ctrl, ?array $args = null)
    {
        if (is_null($args)) {
            $ctrl->setEnvParam(BaseController::VIEW_EXTRA_ARGS, null);
            return;
        }
        $v = $ctrl->getEnvParam(BaseController::VIEW_EXTRA_ARGS);
        if (!$v) {
            $v = $args;
        } else {
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
    public static function getExtraArgs(BaseController $ctrl, ?string $name = null, $default = [])
    {
        if ($g = $ctrl->getEnvParam(BaseController::VIEW_EXTRA_ARGS)) {
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
        // + | --------------------------------------------------------------------
        // + | preserve application to send to file login view if exists
        // + |
        $v_view = __FUNCTION__;
        if (
            !igk_environment()->viewfile && igk_app_is_uri_demand($ctrl, $v_view)
            && file_exists($file = $ctrl->getViewFile($v_view, false))
            && (igk_io_basenamewithoutext($file) == $v_view)
        ) {
            $ctrl->loader->view($file, compact("u", "pwd", "nav"));
            return false;
        }
        $v_uctrl = igk_getctrl(IGK_USER_CTRL);
        $sysuser = $v_uctrl->getUser();
        $f = 0; // update last connection   
        if ($ctrl->User === null) {
            if (is_object($u)) {
                if (($u instanceof \IGK\Models\Users) && !$u->is_mock()) {
                    $u = $u->to_array();
                }
                if (igk_is_array_key_present($u, array("clLogin", "clPwd"))) {
                    $v_uctrl->setUser((object)$u);
                    $ctrl->checkUser(false);
                    $f = !$sysuser ||  ($sysuser->clId != $u["clId"]);
                }
            } else {
                // $ip = igk_server()->RemoteIp();
                // $agent = igk_server()->HTTP_USER_AGENT;
                // TODO : CONNECT
                // $v_uctrl->canConnect($ip, $u, $pwd, $agent)
                // igk_wln_e("connect with login and pwd", $ip, $agent,  $ip);
                
                if ($v_uctrl->connect($u, $pwd)) {
                    igk_ilog('login connect: > '.$u); 
                    // + | --------------------------------------------------------------------
                    // + | init controller user 
                    // + |                     
                    $ctrl->checkUser(false);
                    $f = 1;
                } else {
                    Logger::danger('connection failed');
                    igk_ilog('login failed: > '.$u);
                } 
            }
        }
        if ($f) { 
            $user = $ctrl->getUser();
            $uid = $user->clId;
            $user->bclLastLogin = $user->clLastLogin;
            $user->clLastLogin = date(\IGKConstants::MYSQL_DATETIME_FORMAT);
 
            $u = \IGK\Models\Users::update(
                [
                    "clLastLogin" => $user->clLastLogin,
                ],
                ["clId" => $uid]
            );
            $server = igk_server();
            igk_hook(IGKEvents::HOOK_USER_LOGIN, [
                "user" => $user,
                "ip" => $server->RemoteIp(),
                "agent" => $server->HTTP_USER_AGENT,
                "geox" => $server->GEOIP_LATITUDE,
                "geoy" => $server->GEOIP_LONGITUDE,
                "country_code" => $server->GEOIP_COUNTRY_CODE,
                "country_name" => $server->GEOIP_COUNTRY_NAME,
                "region" => $server->GEOIP_REGION,
                "city" => $server->GEOIP_CITY,
                "status" => 0,
                "description" => null,
            ]);

            // igk_wln_e("login ...... ");
        } else {
            igk_notifyctrl("notify/app/login")->addErrorr("e.loginfailed");
        }

        if ($nav) {
            igk_wln_e("no navigation");
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
        return $ctrl->getUser() !== null;
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
                // check existance of the user
                if (!is_null(\IGK\Models\Users::GetCache("clGuid", $u->clGuid))){                    
                    $controller->User = $controller->initUserFromSysUser($u);
                }else{
                    $r = false;
                    if ($u){
                        // provided user not exists in cache database 
                        // igk_app()->session->setUser(null);
                        $controller::logout();
                        igk_exit();
                    }
                }
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
            // + | --------------------------------------------------------------------
            // + | raise utility command
            // + |
            DbUtilityHelper::InvokeOnStartDropTable($ctrl, true);            
            igk_hook(IGKEvents::HOOK_DB_START_DROP_TABLE, $ctrl);
        };
        $_vinit = 0;

        if ($force || $ctrl->getCanInitDb()) {
            if (!$ctrl->getUseDataSchema()) {
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
                if ($db && $db->connect()) { 
                    $migHandle = new MigrationHandler($controller);
                    $migHandle->down(); 
                  
                    $v_tblist = [];
                    if ($tables = igk_getv($tb, "tables")) {
                        foreach (array_keys($tables) as $k) {
                            $v_tblist[$k] = $k;
                        }
                    }
                    $func();                     
                    $db->dropTable($v_tblist);
                    $_vinit = 1;
                    $db->close();
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
        $user = $ctrl->getUser(); 
        igk_getctrl(IGK_USER_CTRL)->logout();
        if ($user) {
            $server = igk_server();
            igk_hook(IGKEvents::HOOK_USER_LOGOUT, [
                "user" => $user,
                "ip" => $server->RemoteIp(),
                "agent" => $server->HTTP_USER_AGENT,
                "geox" => $server->GEOIP_LATITUDE,
                "geoy" => $server->GEOIP_LONGITUDE,
                "country_code" => $server->GEOIP_COUNTRY_CODE,
                "country_name" => $server->GEOIP_COUNTRY_NAME,
                "region" => $server->GEOIP_REGION,
                "city" => $server->GEOIP_CITY,
                "status" => 1,
                "description" => null,
            ]);
        }
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
     * retrieve cached the configs
     * @param BaseController $controller 
     * @param string $name 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public static function getConfig(BaseController $controller, string $name, $default = null)
    {
        if ($name == 'no_auto_cache_view'){
            return 1;
        }
        return \IGK\System\Configuration\CacheConfigs::GetCachedOption($controller, $name, $default);
    }

    public static function setConfig(BaseController $controller, string $name, $value)
    {
        return \IGK\System\Configuration\CacheConfigs::SetCachedOption($controller, $name, $value);
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
    public static function model(BaseController $controller, string $model): ?ModelBase
    {
        $cl = $model;
        if (!class_exists($model) || !is_subclass_of($model, ModelBase::class)) {
            if (!($cl = self::resolvClass($controller, "Models/$model"))) {
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
        $db = self::getDataAdapter($controller);
        if (!$db || !$db->connect()) {
            return false;
        }
        // init load a schema
        $r = $controller->loadDataAndNewEntriesFromSchemas();
        if (!$r) {
            $db->close();
            return false;
        }
        $tb = $r->Data;
        igk_db_init_dataschema($controller, $r, $db);
        $db->close();
        return $tb;
    }

    /**
     * 
     * @param BaseController $controller 
     * @param bool $throw 
     * @return null|IDataDriver 
     * @throws IGKException 
     */
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
        if (!$controller->getConfig(IGK_CTRL_CNF_USE_DATASCHEMA)) {
            if (is_null($table = $controller->getDataTableName()))
                return;
        }   
        $f = $controller->getDbConstantFile();
        $tb = $controller->getDataTableDefinition(null);
        if ($tb) {
            $tb = $tb->tables;
        }


        $s = "<?php" . IGK_LF;
        $s .= "// Balafon : generated db constants file" . IGK_LF;
        $s .= "// date: " . date(\IGKConstants::MYSQL_DATETIME_FORMAT) . IGK_LF;
        // generate class constants definition
        $cl = igk_uri(get_class($controller));
        $ns = dirname($cl);

        if (!empty($ns) && ($ns != ".")) {
            $s .= "namespace " . str_replace("/", "\\", $ns) . "; " . IGK_LF;
        }
        $s .= "abstract class " . basename($cl) . "DbConstants{" . IGK_LF;
        if (!is_null($table)) {
            $tb = [$table => $tb];
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
    public static function loadDataFromSchemas(BaseController $ctrl, $resolvName = true, $operation = DbSchemasConstants::Migrate)
    {
        return DbSchemas::LoadSchema(self::getDataSchemaFile($ctrl), $ctrl, true, $operation);
    }
    /**
     * controller get data table definition
     * @return ?stdClass { $tableRowReference, $columnInfo }
     */
    public static function getDataTableDefinition(BaseController $ctrl, ?string $tablename = null)
    {
        // + | --------------------------------------------------------------------
        // + | GET DB TABLE DEFINITION EXTENSION
        // + |
        
        if (!($ctrl instanceof IDatabaseHost)) {
            return null;
        }

        if ($ctrl->getUseDataSchema()) {
            $info = null;
            if (DBCaches::IsInitializing())
                return; 
            if (is_null($tablename) || !($info = \IGK\Database\DbSchemaDefinitions::GetDataTableDefinition($ctrl->getDataAdapterName(), $tablename))) {
                // load the actual state of the dataschema - everything up 
                if ($schema = self::loadDataFromSchemas($ctrl, true, DbSchemasConstants::None)) {
                    if (is_null($tablename)) {
                        return $schema;
                    }
                    if (isset($schema->tables[$tablename])) {
                        $info = &$schema->tables[$tablename];
                        if (!isset($info->tableRowReference)) {
                            //
                            // + | update data with table's row model reference info
                            //
                            $info->tableRowReference = igk_array_object_refkey($info->columnInfo, IGK_FD_NAME);
                        }
                        //\IGK\Database\DbSchemaDefinitions::RegisterDataTableDefinition($ctrl->getDataAdapterName(), $tablename, $schema);
                    }
                }
            }
            if ($info) {
                // $info->controller = $ctrl;
                igk_hook(\IGKEvents::FILTER_DB_SCHEMA_INFO, ["tablename" => $tablename, "info" => $info]);
            }
            return $info;
        } else {
            $v_single_table = $ctrl->getDataTableName();
            if (
                !is_null($v_single_table) && ($v_single_table == $tablename) &&
                ($clinfo = $ctrl->getDataTableInfo())
            ) {
                $cinfo = (object)[
                    "columnInfo" => $clinfo,
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
        return igk_is_conf_connected() || DBCaches::InitRequest();
    }

    /**
     * load data and entries 
     * @param BaseController $controller 
     * @return object 
     * @throws IGKException 
     */
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
                    if (isset($t->entries)) {

                        if ($c = $t->entries) {
                            $obj->Entries[$n] =  $c;
                        }
                    }
                    $obj->Data[$n] = $t;
                }
            }
        }
        return $obj;
    }

    /**
     * controller registrated in not visible list;
     * @param BaseController $controller 
     * @return bool 
     */
    public static function getIsVisible(BaseController $controller): bool
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
    public static function bindCssStyle(BaseController $controller, ?\IGK\System\Html\Dom\HtmlDocTheme $theme = null, bool $cssRendering = false)
    {
        $theme = $theme ?? self::getCurrentDoc($controller)->getTheme();
        if ($theme && !empty($file = $controller->getPrimaryCssFile()) && is_file($file)) {
            return igk_ctrl_bind_css_file($controller, $theme, $file, $cssRendering, 0);
        }
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
            $controller->_include_view($file);
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
                $b["Description"] = $v->description;
                $tabs[] = $k;
                foreach ($v->columnInfo as $cinfo) {
                    $col = $b->add(IGK_COLUMN_TAGNAME);
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
    public static function pcss(BaseController $controller, $fname, $document = null)
    {
        if (is_null($document)) {
            $document = $controller->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY) ?? die("document not found");
        }
        if (is_string($fname)) {
            $fname = explode("|", $fname);
        }
        if (!is_array($fname)) {
            igk_die("not a valid argument");
        }
        $q = $fname;
        $c = 0;
        while ($fname = array_shift($q)) {
            $f = $controller->getStylesDir() . "/$fname";
            if (!preg_match("/\.(" . IGK_DEFAULT_STYLE_EXT . "|css)$/", $f)) {
                $f .= "." . IGK_DEFAULT_STYLE_EXT;
            }
            if (file_exists($f)) {
                $document->getTheme()->addTempFile($controller, $f);
                $c++;
            }
        }
        return $c;
    }
    /**
     * load js script extension
     * @param BaseController $controler
     */
    public static function js(BaseController $controller, $fname, $document = null)
    {
        if (is_null($document)) {
            $document = $controller->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY) ?? die("document not found");
        }
        if (is_string($fname)) {
            $fname = explode("|", $fname);
        }
        if (!is_array($fname)) {
            igk_die("not a valid argument");
        }

        $q = $fname;
        $c = 0;
        while ($fname = array_shift($q)) {
            $f = $controller->getScriptsDir() . "/$fname";
            if (!preg_match("/\.(js)$/", $f)) {
                $f .= ".js";
            }
            if (file_exists($f)) {
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
    public static function inc(BaseController $controller, string $inc, $args = null)
    {
        $cf = \IGK\Helper\ViewHelper::Dir() . "/" . $inc;
        if (is_null($args)) {
            $args = $controller->getViewArgs();
        }
        foreach (["", ".pinc"] as $ext) {
            if (file_exists($g = $cf . $ext)) {
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
    public static function initGroups(BaseController $controller, array $groups)
    {
        $cl = $controller->getName() ?? "/" . igk_uri(get_class($controller));
        foreach ($groups as $value) {
            $name = $controller::name($value);
            $group = Groups::createIfNotExists(["clName" => $name, "clController" => $cl]);
            $c = Authorizations::createIfNotExists(["clName" => $name]);
            if (!$c || !$group)
                continue;
            Groupauthorizations::createIfNotExists([
                "clAuth_Id" => $c->clId,
                "clGrant" => 1,
                "clGroup_Id" => new DbLinkExpression(
                    Groups::table(),
                    "clName",
                    $name
                )
            ]);
        }
    }

    /**
     * get action handler 
     * @param BaseController $controller 
     * @return mixed 
     * @throws IGKException 
     */
    public static function getActionHandler(BaseController $controller, string $name)
    {
        if (property_exists($controller, ControllerEnvParams::NoActionHandle) && $controller->{ControllerEnvParams::NoActionHandle})
            return null;

        // if (igk_env_count(__METHOD__ . $name) > 1) {
        //     igk_trace();
        //     igk_die(__METHOD__ . " call twice " . $name);
        // }

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
        $sublen = 1;
        if (!empty($ns)) {
            $sublen += strlen($ns);
        }
        while ($cl = array_shift($t)) {
            $fcl = $cl;
            if (!empty($ns) && (strpos($cl, $ns . "\\") === 0)) {
                $fcl = substr($cl, $sublen);
            }
            $f = igk_dir(implode("/", [$classdir, $fcl . ".php"]));
            if (file_exists($f) && class_exists($cl)) {
                return $cl;
            }
        }
        return null;
    }

    public static function showError(BaseController $controller, string $message, string $title, $code = 400)
    {

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

    /**
     * handle execption
     * @param BaseController $controller 
     * @param Exception $ex 
     * @return void 
     * @throws IGKException 
     */
    public function handleException(BaseController $controller, Exception $ex, string $title)
    {
        self::showError($controller, $ex->getMessage(), $title, $ex->getCode());
    }

    public function referer(BaseController $controller)
    {
        return igk_server()->HTTP_REFERER ?? self::uri($controller, '');
    }
}
