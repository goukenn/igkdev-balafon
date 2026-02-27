<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKEnvironmentConstants.php
// @date: 20220803 13:48:54
// @desc: 
/**
 * represent environment constants
 * @package 
 */
abstract class IGKEnvironmentConstants{
     // | define environment reserver key constant

    /**
    * Constant: init app.
    * @var mixed
    */
    const INIT_APP = 'INIT_APP';

    /**
    * Constant: debug.
    * @var mixed
    */
    const DEBUG = 'DEBUG';
     // KEY to environement css utils args

    /**
    * Constant: css util args.
    * @var mixed
    */
    const CSS_UTIL_ARGS = "cssutils:/args";

    /**
    * Constant: ctrl context source view args.
    * @var mixed
    */
    const CTRL_CONTEXT_SOURCE_VIEW_ARGS=self::CURRENT_CTRL + 2;

    /**
    * Constant: ctrl context view args.
    * @var mixed
    */
    const CTRL_CONTEXT_VIEW_ARGS=self::CURRENT_CTRL + 1;

    /**
    * Constant: current ctrl.
    * @var mixed
    */
    const CURRENT_CTRL=0xE0;

    /**
    * Constant: view current action.
    * @var mixed
    */
    const VIEW_CURRENT_ACTION=self::CURRENT_CTRL+3;

    /**
    * Constant: view handle actions.
    * @var mixed
    */
    const VIEW_HANDLE_ACTIONS=self::CURRENT_CTRL+4;

    /**
    * Constant: view inc view.
    * @var mixed
    */
    const VIEW_INC_VIEW= self::CURRENT_CTRL+5;

    /**
    * auto generate doc.
    */     const VIEW_CURRENT_VIEW_NAME= self::CURRENT_CTRL+6;
     /**
      * store action parameter
      */
     const VIEW_ACTION_PARAMS = self::CURRENT_CTRL+7;
     /**
      * store instance
      */
     const INSTANCES = self::CURRENT_CTRL+8;

    /**
    * Constant: ignore lib dir.
    * @var mixed
    */
    const IGNORE_LIB_DIR =self::CURRENT_CTRL+9;

    /**
    * Constant: not visible ctrl.
    * @var mixed
    */
    const NOT_VISIBLE_CTRL = self::CURRENT_CTRL+10; // "sys://ctrl/notvisible";

    /**
    * Constant: article chain context.
    * @var mixed
    */
    const ARTICLE_CHAIN_CONTEXT = self::CURRENT_CTRL+11;// "sys://article_chain";

    /**
    * Constant: modules.
    * @var mixed
    */
    const MODULES =  "sys://module";

    /**
    * Constant: require modules.
    * @var mixed
    */
    const REQUIRE_MODULES = "sys://require_mods";
     /**
      * auto load classes
      */
     const AUTO_LOAD_CLASS = self::CURRENT_CTRL+12; // "auto_load_class";

    /**
    * Constant: view file caches.
    * @var mixed
    */
    const VIEW_FILE_CACHES = "viewFileCaches";

    /**
    * Constant: ignore js dir.
    * @var mixed
    */
    const IGNORE_JS_DIR = self::CURRENT_CTRL+13; // "sys://lib/ignorejsdir";
     // store component initiators environment - for speed up node creation

    /**
    * Constant: component initiators.
    * @var mixed
    */
    const COMPONENT_INITIATORS = "component_initiators";

    /**
    * Constant: current user.
    * @var mixed
    */
    const CURRENT_USER= self::CURRENT_CTRL+30;
     // environment list

    /**
    * Constant: dev env.
    * @var mixed
    */
    const DEV_ENV = "DEV";

    /**
    * Constant: ops env.
    * @var mixed
    */
    const OPS_ENV = "OPS";

    /**
    * Constant: tst env.
    * @var mixed
    */
    const TST_ENV = "TST";

    /**
    * Constant: acc env.
    * @var mixed
    */
    const ACC_ENV = "ACC";

    /**
    * Constant: mp1 env.
    * @var mixed
    */
    const MP1_ENV = "MP1";

    /**
    * Constant: mp2 env.
    * @var mixed
    */
    const MP2_ENV = "MP2";

    /**
    * Constant: css env style key.
    * @var mixed
    */
    const CSS_ENV_STYLE_KEY = 'css/default/controlstyle';

    /**
    * Constant: ctrl env param modules.
    * @var mixed
    */
    const CtrlEnvParamModules = 'modules';

    /**
    * Constant: component package key.
    * @var mixed
    */
    const COMPONENT_PACKAGE_KEY = 'sys://components/packages';
}