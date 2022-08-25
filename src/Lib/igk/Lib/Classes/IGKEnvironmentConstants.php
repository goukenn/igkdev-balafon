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
     const INIT_APP = 'INIT_APP';
     const DEBUG = 'DEBUG';
     // KEY to environement css utils args
     const CSS_UTIL_ARGS = "cssutils:/args"; 
     const CTRL_CONTEXT_SOURCE_VIEW_ARGS=self::CURRENT_CTRL + 2;
     const CTRL_CONTEXT_VIEW_ARGS=self::CURRENT_CTRL + 1;
     const CURRENT_CTRL=0xE0;
     const VIEW_CURRENT_ACTION=self::CURRENT_CTRL+3;
     const VIEW_HANDLE_ACTIONS=self::CURRENT_CTRL+4;
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
     const IGNORE_LIB_DIR =self::CURRENT_CTRL+9;
     const NOT_VISIBLE_CTRL = self::CURRENT_CTRL+10; // "sys://ctrl/notvisible"; 
     const ARTICLE_CHAIN_CONTEXT = self::CURRENT_CTRL+11;// "sys://article_chain";
     /**
      * auto load classes
      */
     const AUTO_LOAD_CLASS = self::CURRENT_CTRL+12; // "auto_load_class";
     const VIEW_FILE_CACHES = "viewFileCaches";
     const IGNORE_JS_DIR = self::CURRENT_CTRL+13; // "sys://lib/ignorejsdir";
}