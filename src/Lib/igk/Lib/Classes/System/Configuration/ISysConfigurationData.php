<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ISysConfigurationData.php
// @date: 20220813 14:17:07
// @desc: configuration data


namespace IGK\System\Configuration;

/**
* @property bool $BootStrap
* @property bool $BootStrap.Enabled
* @property bool $JQuery.Enabled
* @property bool $admin_login
* @property bool $admin_pwd
* @property bool $allow_article_config
* @property bool $allow_auto_cache_page
* @property bool $allow_debugging
* @property bool $allow_log
* @property bool $allow_page_cache
* @property bool $app_default_controller_tag_name
* @property bool $cache_file_time
* @property bool $cache_loaded_file
* @property bool $company_name
* @property bool $configuration_port
* @property bool $copyright
* @property bool $date_time_zone default time zone 
* @property bool $datetime_format default time format
* @property bool $db_auto_create check to create database 
* @property bool $db_default_column_id default column identifier id
* @property bool $db_driver the db driver
* @property bool $db_name   the global database name
* @property bool $db_port   the global database port for mysql
* @property bool $db_prefix the global table prefix
* @property bool $db_pwd    the global database password
* @property bool $db_server the global database server for adapter that require server connection 
* @property bool $db_user   the global database server user for adapter that require server connection
* @property bool $default_author
* @property bool $default_controller
* @property bool $default_dataadapter
* @property bool $default_lang
* @property bool $display_errors
* @property bool $error_debug
* @property bool $error_reporting
* @property bool $force_secure_redirection
* @property bool $force_single_controller_app
* @property bool $globaltheme
* @property bool $help_uri
* @property bool $informAccessConnection
* @property bool $mail_admin
* @property bool $mail_authtype
* @property bool $mail_contact
* @property bool $mail_noreply
* @property bool $mail_password
* @property bool $mail_port
* @property bool $mail_portal
* @property bool $mail_server
* @property bool $mail_testmail
* @property bool $mail_useauth
* @property bool $mail_user
* @property bool $max_script_execution_time
* @property bool $menuHostCtl
* @property bool $menu_defaultPage
* @property bool $meta_copyright
* @property bool $meta_description
* @property bool $meta_enctype
* @property bool $meta_keywords
* @property bool $meta_title
* @property bool $ob_buffer_padding_length
* @property bool $ovh
* @property bool $php_run_scrit
* @property bool $phpmyadmin_uri
* @property bool $powered_messae
* @property bool $powered_message
* @property bool $powered_uri
* @property bool $python_run_scrit
* @property bool $secure_port
* @property bool $show_debug
* @property bool $show_powered
* @property bool $site_dir
* @property bool $sitemap_xsl
* @property bool $support_lang
* @property bool $website_adminmail
* @property bool $website_domain
* @property bool $website_prefix
* @property bool $website_title
*/
interface ISysConfigurationData{

}