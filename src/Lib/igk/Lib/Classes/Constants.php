<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Constants.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK;
use IGK\System\EntryClassResolution;
use IGKEnvironment;
/**
 * Represent Balafon's global constants
 */
final class Constants
{
    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    const ENVIRONMENT_VARS_PATTERN = '^IGK_';
    /**
     * Constant: namespace.
     * @var mixed
     */
    const NAMESPACE = "http://schema.igkdev.com";
    /**
     * Constant: base view uri.
     * @var mixed
     */
    const BASE_VIEW_URI = '@/';
    /**
     * Constant: str page title.
     * @var mixed
     */
    const STR_PAGE_TITLE = "{0} - [ {1} ]";
    /**
     * Constant: mysql datetime format.
     * @var mixed
     */
    const MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
    /**
     * Constant: mysql date format.
     * @var mixed
     */
    const MYSQL_DATE_FORMAT = "Y-m-d";
    /**
     * Constant: mysql time format.
     * @var mixed
     */
    const MYSQL_TIME_FORMAT = "H:i:s";
    /**
     * Constant: default time zone.
     * @var mixed
     */
    const DEFAULT_TIME_ZONE = 'Europe/Brussels';
    /**
     * Constant: model table regex.
     * @var mixed
     */
    const MODEL_TABLE_REGEX = "/%(?P<name>((sys)?prefix|year))%/i";
    /**
     * Constant: token expire.
     * @var mixed
     */
    const TOKEN_EXPIRE = 60 * 24 * 3;
    /**
     * Constant: file path hash algo.
     * @var mixed
     */
    const FILE_PATH_HASH_ALGO = 'crc32b';
    /**
     * Constant: baseclass command.
     * @var mixed
     */
    const BASECLASS_COMMAND = '\System\Console\Commands';
    /**
     * Constant: entry base model class.
     * @var mixed
     */
    const ENTRY_BASE_MODEL_CLASS = '\ModelBase';
    /**
     * Constant: sitemap validator.
     * @var mixed
     */
    const SITEMAP_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
    /**
     * Constant: sitemap index validator.
     * @var mixed
     */
    const SITEMAP_INDEX_VALIDATOR = "http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd";
    /**
     * Constant: sitemap ns.
     * @var mixed
     */
    const SITEMAP_NS = "http://www.sitemaps.org/schemas/sitemap/0.9";
    /**
     * Constant: module package list uri.
     * @var mixed
     */
    const MODULE_PACKAGE_LIST_URI = 'https://igkdev.com/balafon/get-module/';
    /**
     * Constant: guid length.
     * @var mixed
     */
    const GUID_LENGTH = 38;
    /**
     * Constant: path var detect model regex.
     * @var mixed
     */
    const PATH_VAR_DETECT_MODEL_REGEX = "/^%(?P<name>[^%]+)%/";
    /**
     * Constant: raw var.
     * @var mixed
     */
    const RAW_VAR = 'raw';
    /**
     * Constant: ctrl var.
     * @var mixed
     */
    const CTRL_VAR = 'ctrl';
    /**
     * Constant: log tag.
     * @var mixed
     */
    const LOG_TAG = 'BLF';
    /**
     * project configuration file
     */
    const PROJECT_CONF_FILE = 'balafon.config.json';
    /**
     * Constant: module conf file.
     * @var mixed
     */
    const MODULE_CONF_FILE = 'balafon.module.json';
    //+ | for project entry namespace definition
    /**
     * Constant: ns action entry.
     * @var mixed
     */
    const NS_ACTION_ENTRY = 'Actions';
    /**
     * Constant: ns model entry.
     * @var mixed
     */
    const NS_MODEL_ENTRY = 'Models';
    /**
     * Constant: ns database entry.
     * @var mixed
     */
    const NS_DATABASE_ENTRY = 'Database';
    // + to avoid inteliphense warning
    /**
     * Constant: ns macros class.
     * @var mixed
     */
    const NS_MACROS_CLASS = EntryClassResolution::DbMacros;
    /**
     * Constant: db init manager.
     * @var mixed
     */
    const DB_INIT_MANAGER = \IGK\Database\DbInitManager::class;
    // + | default view page limit
    /**
     * Constant: db view page limit.
     * @var mixed
     */
    const DB_VIEW_PAGE_LIMIT = 25;
    /**
     * Constant: default theme style.
     * @var mixed
     */
    const DEFAULT_THEME_STYLE = 'default.pcss';
    /**
     * Constant: annotation suffix.
     * @var mixed
     */
    const ANNOTATION_SUFFIX = 'Annotation';
    /**
     * Constant: sess living time.
     * @var mixed
     */
    const SESS_LIVING_TIME = 172800;
    /**
     * Constant: defautl page controller class.
     * @var mixed
     */
    const DEFAUTL_PAGE_CONTROLLER_CLASS = 'IGKDefaultPageController';
    // environment
    /**
     * Constant: component package key.
     * @var mixed
     */
    const COMPONENT_PACKAGE_KEY = IGKEnvironment::COMPONENT_PACKAGE_KEY;
    /**
     * Constant: sys default html package.
     * @var mixed
     */
    const SYS_DEFAULT_HTML_PACKAGE = 'igk';
    /**
     * Constant: bcss extension.
     * @var mixed
     */
    const BCSS_EXTENSION = '.bcss';
    /**
     * Constant: pcss extension.
     * @var mixed
     */
    const PCSS_EXTENSION = '.pcss';
    /**
     * Constant: db model field prefix.
     * @var mixed
     */
    const DB_MODEL_FIELD_PREFIX = 'FD_';
    /**
     * Constant: db model fullname field prefix.
     * @var mixed
     */
    const DB_MODEL_FULLNAME_FIELD_PREFIX = 'FN_';
    /**
     * error constant missing controller
    */
    const ERROR_MISSING_CONTROLLER = -7;
    /**
     * Constant: init command.
     * @var mixed
     */
    const INIT_COMMAND = '--init';
    /**
     * get the core version 
     * @return string 
     */
    public static function CorePHPVersion()
    {
        list($major, $minor) = explode(".", PHP_VERSION);
        return $major . "." . $minor;
    }
    /**
     * auto generate doc.
     * @return array
     */
    public static function EnvironmentConstants(): array
    {
        return [
            'IGK_ENV_NO_AUTOCACHEVIEW' // to disable global constant key 
        ];
    }
}