<?php

namespace IGK\System\Installers;

use IGK\Helper\StringUtility;
use IGK\System\Console\AppConfigs;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlContext;
use IGK\System\Installers\InstallerUtils;
use IGKAppSystem;
use IGKEvents;
use IGKException;
use IO;

class InstallSite
{
    /**
     * 
     * @param mixed $folder 
     * @param mixed|null $packagefolder 
     * @param int $listen 
     * @param string $environment 
     * @param array $options extra options
     * @return false|void 
     * @throws IGKException 
     */
    public static function Install($folder, $listen = 80, $environment = "development", $options = [])
    {
        $installer = new self();
        return $installer->installSite($folder, $listen, $environment, $options);
    }
    /**
     * 
     * @param string $folder working folder 
     * @param int $listen default listen por 
     * @param string $environment defaut environment
     * @param array $options parameter 
     * @return bool
     * @throws IGKException 
     */
    public function installSite($folder,  $listen = 80, $environment = "development", array $options = [])
    {
        $core = IGK_LIB_FILE;
        $src = rtrim($folder, "/");

        if (file_exists($src) && !igk_getv($options, "force")) {
            Logger::danger("directory exists.");
            return false;
        }

        if (!igk_io_createdir($src)) {
            return false;
        }
        $wdir = getcwd();
        $c_root = StringUtility::Uri(implode("/", array_filter([$src, ltrim(igk_io_get_relativepath($src, $options["rootdir"]), './')])));
        $is_primary = $src == $c_root;
        // $c_app = ltrim(igk_io_get_relativepath($folder, igk_getv($options, "appdir")), "./");
        // $c_public = ltrim(igk_io_get_relativepath($folder, IGK_BASE_DIR), "./");
        // $is_primary = $src == $c_root;
        // $src = realpath($src);
        // igk_wln_e("c_root ", $c_root , $is_primary ,$src, $folder);
        $c_public = "";
        $c_app = "";
        if (!$is_primary) {
            $src = dirname($c_root);
            $c_app = ltrim(igk_io_get_relativepath($folder, $src . "/application"), "./");
            $c_base = ltrim(igk_io_get_relativepath($folder, $src), "./");
            $c_public = ltrim(igk_io_get_relativepath($folder, $c_root), "./");




            igk_io_createdir($src . "/application");
            igk_io_createdir($src . "/public");
            igk_io_createdir($src . "/temp");
            igk_io_createdir($src . "/logs");
            igk_io_createdir($src . "/crons");
            igk_io_createdir($src . "/test");
            $configs = [
                "**/.vscode/**",
                "**/node_modules/**",
                "**/.DS_Store",
                "**/.gitignore",
                ".gitignore",
                "vhost.conf",
                "balafon.config.xml",
                "phpunit.xml.dist",
                "phpunit-watcher.yml",
                "{$c_app}/**",
                "{$c_app}/Projects/**/Data/**",
                "{$c_base}/sesstemp/**",
                "{$c_public}/**",
                "releases/"
            ];

            // generate git ignore
            igk_io_w2file($folder . "/.gitignore", implode("\n", $configs));
        }

        // generate phpunit-watcher file
        if (!$is_primary) {
            // + | -----------------------------------------------------------
            // + | for phpunit-watcher
            igk_io_w2file($folder . "/phpunit-watcher.yml", implode("\n", [
                "hideManual: true",
                "watch:",
                "   directories:",
                "       - src/application/Projects",
                "       - src/tests",
                "notifications:",
                "   passingTests: false",
                "   failingTests: false",
                "phpunit:",
                "   binaryPath: " . (file_exists($tf = igk_io_packagesdir() . "/vendor/bin/phpunit") ? $tf : ""),
                "   arguments: --stop-on-failure --colors=always --testdox --bootstrap {$c_app}/Lib/igk/Lib/tests/autoload.php {$c_app}/Projects/",
            ]));

            // + | -------------------------------------------------------------------------------------------------
            // generate phpunit.xml.dist distribution
            $php_xml = igk_create_xmlnode("phpunit");
            $php_xml["xmlns:xsi"] = "http://www.w3.org/2001/XMLSchema-instance";
            $php_xml["xsi:noNamespaceSchemaLocation"] = "./{$c_app}/Packages/vendor/phpunit/phpunit/phpunit.xsd";
            $php_xml["bootstrap"] = "./{$c_app}/Lib/igk/Lib/tests/autoload.php";
            $php_xml["colors"] = "true";
            $suites = $php_xml->add("testsuites");
            $ts =  $suites->add("testsuite");
            $ts["name"] = "projects";
            $ts->add("directory")->Content = "{$c_app}/Projects";
            $penv = $php_xml->add("php");
            $penv->add("env")->setAttributes(["name" => "IGK_BASE_DIR", "value" => $c_public]);
            $penv->add("env")->setAttributes(["name" => "IGK_APP_DIR", "value" => $c_app]);
            ob_start();
            $xml_options =  (object)["xmldefinition" => 1, "noheader" => 1, "Indent" => 1, "Context" => HtmlContext::XML];
            $php_xml->renderAJX($xml_options);
            $ob = ob_get_clean();
            igk_io_w2file($folder . "/phpunit.xml.dist", $ob);
        }
        // + | -------------------------------------------------------------------------------------------------
        $public_folder = $folder . "/" . $c_public;
        $app_folder = rtrim($folder . "/" . $c_app, "/");

        if (!is_link($lnk = $app_folder . "/Lib/igk") && !file_exists($lnk)) {
            igk_io_createdir(dirname($lnk));
            symlink(dirname($core), $lnk);
        }
        // + | package folder
        $lnk = $app_folder . "/" . IGK_PACKAGES_FOLDER;

        if (!empty($packagefolder = igk_getv($options, "packagedir")) && !is_link($lnk)) {
            symlink($packagefolder, $lnk);
        } else {
            igk_io_createdir($lnk);
            InstallerUtils::NoAccessDir($lnk);
        }
        $lnk = $app_folder . "/" . IGK_MODULE_FOLDER;
        if (!empty($moduledir = igk_getv($options, "moduledir")) && !is_link(
            $lnk
        )) {
            symlink($moduledir, $lnk);
        } else {
            igk_io_createdir($lnk);
            InstallerUtils::NoAccessDir($lnk);
        }
        // install vendor dir
        $lnk = $app_folder . "/" . IGK_PACKAGES_FOLDER."/". IGK_VENDOR_FOLDER;
        if (!empty($moduledir = igk_getv($options, "vendordir")) && !is_link(
            $lnk
        )) {
            symlink($moduledir, $lnk);
        } else {
            igk_io_createdir($lnk);
            InstallerUtils::NoAccessDir($lnk);
            $base = dirname($lnk);
            // generate composer instruction 
            $this->_generateComposer($base, ["name"=>strtolower(IGK_PLATEFORM_NAME."/site-packages")]);
        }

        $lnk = $app_folder . "/" . IGK_PROJECTS_FOLDER;
        if (!empty($projectdir = igk_getv($options, "projectdir")) && !is_link(
            $lnk
        )) {
            symlink($projectdir, $lnk);
        } else {
            igk_io_createdir($lnk);
            InstallerUtils::NoAccessDir($lnk, 1);
        }

        if (!empty($sessiondir = igk_getv($options, "sessiondir")) && !is_link(
            $lnk = $src . "/sesstemp/"
        )) {
            symlink($sessiondir, $lnk);
        }
        $index = $public_folder . "/index.php";
        igk_io_w2file(
            $index,
            InstallerUtils::GetEntryPointSource([
                "is_primary" => $is_primary,
                "app_dir" => $is_primary ? '$appdir' : '$appdir."/application"',
                "project_dir" => 'IGK_APP_DIR."/Projects"'
            ])
        );
 

        IGKAppSystem::InstallDir($index, 
        $app_folder, 
        dirname($index), 
        $app_folder."/".IGK_PROJECTS_FOLDER,
        $app_folder."/".IGK_DATA_FOLDER,
        $app_folder."/Lib/igk/".IGK_DATA_FOLDER,
        ["domain_name"=>"localhost"]
        );
        

        if (!$is_primary) {
            // + | ----------------------------------------------------------------
            // + | generate vhost

            if (empty($environment)) {
                $environment = "development";
            }
            $tport = "80";
            if (is_numeric($listen) && (strlen($listen) >= 4)) {
                $tport = $listen;
                $listen = "Listen " . $tport . "\n";
            } else
                $listen = "";

            $servername = igk_getv($options, "ServerName", "localhost");
            $t_conf_file = $folder . "/vhost.conf";
            igk_io_w2file(
                $t_conf_file,
                <<<EOF
{$listen}<IfDefine !ServerName>
ServerName ${servername}
</IfDefine>
<VirtualHost *:$tport>
SetEnv ENVIRONMENT {$environment}
SetEnv IGK_LIB_DIR {$src}/application/Lib/igk
DocumentRoot {$c_root}
<Directory {$c_root}>
ErrorDocument 401 '/index.php/Lib/igk/igk_redirection.php?__code=401'
ErrorDocument 402 '/index.php/Lib/igk/igk_redirection.php?__code=402'
ErrorDocument 403 '/index.php/Lib/igk/igk_redirection.php?__code=403'
ErrorDocument 413 '/index.php/Lib/igk/igk_redirection.php?__code=413'
ErrorDocument 500 '/Lib/igk/igk_redirection.php?__code=500'
Options +FollowSymLinks -MultiViews -Indexes
Order deny,allow
AllowOverride none
Allow from all
Require all granted

<IfModule rewrite_module>
RewriteEngine on
RewriteCond "%{REQUEST_FILENAME}" !-d
RewriteCond "%{REQUEST_FILENAME}" !-f
RewriteRule ^(.)+$ "/index.php?rwc=1" [QSA,L]
</IfModule>

</Directory>
<Directory {$src}/public/assets/_chs_/dist/js>
AddType text/javascripit js
AddEncoding deflate js
#<IfModule mod_headers.c>
#Header set Cache-Control "max-age=31536000"
#</IfModule>
</Directory>
</VirtualHost>
EOF
            );
            if (in_array(PHP_OS, ["UNIX","DARWING"])){
                // create vhost link on apache
                $vhost_dir = rtrim(igk_getv($options, "apachedir", "/private/etc/apache2/other"), "/");
                // igk_wln_e("vshot", $vhost_dir, $options);                
                if (is_dir($vhost_dir)) {
                    $conf_file = $vhost_dir . "/vhost-" . sha1($folder) . ".conf";
                    @symlink($t_conf_file, $conf_file);
                }
            }
        }


        if (!$is_primary) {
            if (!file_exists($file = $folder."/".AppConfigs::ConfigurationFileName)){
                // generate configuration file  
                $c = \IGK\System\Console\Utils::GenerateConfiguration($c_public, $c_app);
                $opts = igk_xml_create_render_option();
                $opts->Indent = true;
                igk_io_w2file($file, $c->render($opts));
            }
        }
        // + | ------------------------------------------------------------------------------------------
        // + | post install site 
        // + |
        igk_hook(IGKEvents::HOOK_INSTALL_SITE, ["sender"=>$this, "folder"=>$folder, "options"=>$options]);
        // + | ------------------------------------------------------------------------------------------
        // + | after install change if possible the user group
        // + |
        if (igk_environment()->isUnix() && (get_current_user() == "root")){
            if ($ug = igk_getv($options, "user:group", "www-data:www-data")) {
                `chown -R ${ug} ${folder}`;
                `chmod -R 775 ${folder}`;
            }
        } 
        return true;
    }


    /**
     * generate composer file
     * @param string $folder 
     * @param null|array $options 
     * @return void 
     * @throws IGKException 
     */
    private function _generateComposer(string $folder, ?array $options=null){
        $com = [];
        $com["name"] = igk_getv($options, "name", "");
        $com["description"] = igk_getv($options, "description", "");
        $com["license"] = igk_getv($options, "license", "MIT License");
        $com["autoload"] = [
            "psr-4"=> [
                "IGK\\Packages\\Vendor\\"=>"src/"
            ]
        ];
        $com["require"] = (object)[];
        igk_io_w2file($folder."/composer.json", json_encode($com, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
