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
    * auto generate doc.
    * @var mixed
    */
    const INIT_APP = 'INIT_APP';

    /**
    * auto generate doc.
    * @var mixed
    */
    const DEBUG = 'DEBUG';
     // KEY to environement css utils args

    /**
    * auto generate doc.
    * @var mixed
    */
    const CSS_UTIL_ARGS = "cssutils:/args";

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTRL_CONTEXT_SOURCE_VIEW_ARGS=self::CURRENT_CTRL + 2;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CTRL_CONTEXT_VIEW_ARGS=self::CURRENT_CTRL + 1;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CURRENT_CTRL=0xE0;

    /**
    * auto generate doc.
    * @var mixed
    */
    const VIEW_CURRENT_ACTION=self::CURRENT_CTRL+3;

    /**
    * auto generate doc.
    * @var mixed
    */
    const VIEW_HANDLE_ACTIONS=self::CURRENT_CTRL+4;

    /**
    * auto generate doc.
    * @var mixed
    */
    const VIEW_INC_VIEW= self::CURRENT_CTRL+5;
     /**
      * 
      */
     const VIEW_CURRENT_VIEW_NAME= self::CURRENT_CTRL+6;
     /**
      * store action parameter
      */
     const VIEW_ACTION_PARAMS = self::CURRENT_CTRL+7;
     /**
      * store instance
      */
     const INSTANCES = self::CURRENT_CTRL+8;

    /**
    * auto generate doc.
    * @var mixed
    */
    const IGNORE_LIB_DIR =self::CURRENT_CTRL+9;

    /**
    * auto generate doc.
    * @var mixed
    */
    const NOT_VISIBLE_CTRL = self::CURRENT_CTRL+10; // "sys://ctrl/notvisible";

    /**
    * auto generate doc.
    * @var mixed
    */
    const ARTICLE_CHAIN_CONTEXT = self::CURRENT_CTRL+11;// "sys://article_chain";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MODULES =  "sys://module";

    /**
    * auto generate doc.
    * @var mixed
    */
    const REQUIRE_MODULES = "sys://require_mods";
     /**
      * auto load classes
      */
     const AUTO_LOAD_CLASS = self::CURRENT_CTRL+12; // "auto_load_class";

    /**
    * auto generate doc.
    * @var mixed
    */
    const VIEW_FILE_CACHES = "viewFileCaches";

    /**
    * auto generate doc.
    * @var mixed
    */
    const IGNORE_JS_DIR = self::CURRENT_CTRL+13; // "sys://lib/ignorejsdir";
     // store component initiators environment - for speed up node creation

    /**
    * auto generate doc.
    * @var mixed
    */
    const COMPONENT_INITIATORS = "component_initiators";

    /**
    * auto generate doc.
    * @var mixed
    */
    const CURRENT_USER= self::CURRENT_CTRL+30;
     // environment list

    /**
    * auto generate doc.
    * @var mixed
    */
    const DEV_ENV = "DEV";

    /**
    * auto generate doc.
    * @var mixed
    */
    const OPS_ENV = "OPS";

    /**
    * auto generate doc.
    * @var mixed
    */
    const TST_ENV = "TST";

    /**
    * auto generate doc.
    * @var mixed
    */
    const ACC_ENV = "ACC";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MP1_ENV = "MP1";

    /**
    * auto generate doc.
    * @var mixed
    */
    const MP2_ENV = "MP2";

    /**
    * auto generate doc.
    * @var mixed
    */
    const CSS_ENV_STYLE_KEY = 'css/default/controlstyle';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CtrlEnvParamModules = 'modules';

    /**
    * auto generate doc.
    * @var mixed
    */
    const COMPONENT_PACKAGE_KEY = 'sys://components/packages';
}