<?php
/**
 * represent environment constants
 * @package 
 */
abstract class IGKEnvironmentConstants{
     // | define environment reserver key constant
     const INIT_APP = 'INIT_APP';
     const DEBUG = 'DEBUG';
     const CTRL_CONTEXT_SOURCE_VIEW_ARGS=self::CURRENT_CTRL + 2;
     const CTRL_CONTEXT_VIEW_ARGS=self::CURRENT_CTRL + 1;
     const CURRENT_CTRL=0xE0;
     const VIEW_CURRENT_ACTION=self::CURRENT_CTRL+3;
     const VIEW_HANDLE_ACTIONS=self::CURRENT_CTRL+4;
     const VIEW_INC_VIEW= self::CURRENT_CTRL+5;
     const VIEW_CURRENT_VIEW_NAME= self::CURRENT_CTRL+6;
     const VIEW_ACTION_PARAMS = self::CURRENT_CTRL+7;
     const IGNORE_LIB_DIR = "sys://lib/ignoredir";
     const AUTO_LOAD_CLASS = "auto_load_class";
     const NOT_VISIBLE_CTRL = "sys://ctrl/notvisible"; 
     const ARTICLE_CHAIN_CONTEXT =  "sys://article_chain";
     const VIEW_FILE_CACHES = "viewFileCaches";
}