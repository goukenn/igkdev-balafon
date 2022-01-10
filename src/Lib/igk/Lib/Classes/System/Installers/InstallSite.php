<?php

namespace IGK\System\Installers;

use IGK\System\Installers\InstallerUtils;
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
        $installer = new InstallSite;
        return $installer->installSite($folder,$listen, $environment, $options);
    }
    /**
     * 
     * @param mixed $folder 
     * @param mixed|null $packagefolder 
     * @param int $listen 
     * @param string $environment 
     * @param array $options 
     * @return false|void 
     * @throws IGKException 
     */
    public function installSite($folder,  $listen = 80, $environment = "development", array $options = [])
    {
        $core = IGK_LIB_FILE;
        $src = rtrim($folder, "/") . "/src";
        /// DEBUG: IO::RmDir($src); 
        if (file_exists($src)) {
            return false;
        }

        if (!igk_io_createdir($src)) {
            return false;
        }
        $src = realpath($src);
        igk_io_createdir($src . "/application");
        igk_io_createdir($src . "/public");
        igk_io_createdir($src . "/temp");
        igk_io_createdir($src . "/logs");
        igk_io_createdir($src . "/crons");
        igk_io_createdir($src . "/test");
        // generate git ingore
        igk_io_w2file($folder . "/.gitignore", implode("\n", [
            "**/.vscode/**",
            "**/node_modules/**",
            "**/.DS_Store",
            "**/.gitignore",
            ".gitignore",
            "phpunit.xml.dist",
            "src/vhost.conf",
            "phpunit-watcher.yml",
            "src/application/**",
            "src/application/Projects/**/Data/**",
            "src/sesstemp/**",
            "src/public/**"
        ]));
        // generate phpunit-watcher file
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
            "   arguments: --stop-on-failure --colors=always --testdox --bootstrap src/application/Lib/igk/Lib/Tests/autoload.php src/application/Projects/",
        ]));

        // generate phpunit.xml.dist distribution
        $php_xml = igk_create_xmlnode("phpunit");
        $php_xml["xmlns:xsi"] = "http://www.w3.org/2001/XMLSchema-instance";
        $php_xml["xsi:noNamespaceSchemaLocation"] = "./src/application/Packages/vendor/phpunit/phpunit/phpunit.xsd";
        // $php_xml["bootstrap"] = "./src/application/Packages/vendor/autoload.php";
        $php_xml["bootstrap"] = "./src/application/Lib/igk/Lib/Test/autoload.php";
        $php_xml["colors"] = "true";

        $suites = $php_xml->add("testsuites");
        $ts =  $suites->add("testsuite");
        $ts["name"] = "projects";
        $ts->add("directory")->Content = "src/application/Projects";
        $penv = $php_xml->add("php");
        $penv->add("env")->setAttributes(["name" => "IGK_BASE_DIR", "value" => "src/public"]);
        $penv->add("env")->setAttributes(["name" => "IGK_APP_DIR", "value" => "src/application"]);
        ob_start();
        $xml_options =  (object)["xmldefinition" => 1, "noheader" => 1];
        $php_xml->renderAJX($xml_options);
        $ob = ob_get_clean();
        igk_io_w2file($folder . "/phpunit.xml.dist", $ob);


        if (!is_link($lnk = $src . "/application/Lib/igk")) {
            igk_io_createdir(dirname($lnk));
            symlink(dirname($core), $lnk);
        }
        // + | package folder
        if (!empty($packagefolder =igk_getv($options, "packagedir")) && !is_link($lnk = $src . "/application/" . IGK_PACKAGES_FOLDER)) {
            symlink($packagefolder, $lnk);
        } 
        if (!empty($moduledir = igk_getv($options, "moduledir")) && !is_link(
            $lnk = $src . "/application/" . IGK_PACKAGES_FOLDER . "/Modules"
        )) {
            symlink($moduledir, $lnk);
        }

        if (!empty($projectdir = igk_getv($options, "projectdir")) && !is_link(
            $lnk = $src . "/application/" . IGK_PROJECTS_FOLDER 
        )) {
            symlink($projectdir, $lnk);
        }
        if (!empty($sessiondir = igk_getv($options, "sessiondir")) && !is_link(
            $lnk = $src . "/sesstemp/"
        )) {
            symlink($sessiondir, $lnk);
        }
        $index = $src . "/public/index.php";
        igk_io_w2file(
            $index,
            InstallerUtils::GetEntryPointSource([
                
            ])
        );

        if (empty($environment)) {
            $environment = "development";
        }
        $tport = "80";
        if (is_numeric($listen) && (strlen($listen) >= 4)) {
            $tport = $listen;
            $listen = "Listen " . $tport . "\n";
        } else
            $listen = "";
        $root = $src . "/public";
        $servername = igk_getv($options, "ServerName", "localhost");
        igk_io_w2file(
            $src . "/vhost.conf",
            <<<EOF

{$listen}<IfDefine !ServerName>
ServerName ${servername}
</IfDefine>
<VirtualHost *:$tport>
SetEnv ENVIRONMENT {$environment}
SetEnv IGK_LIB_DIR {$src}/application/Lib/igk
DocumentRoot {$root}
<Directory {$root}>
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
        // create vhost link on apache
        $vhost_dir = rtrim(igk_getv($options, "apachedir", "/private/etc/apache2/other"), "/");
        // igk_wln_e("vshot", $vhost_dir, $options);
        if (is_dir($vhost_dir)) {
            $conf_file = $vhost_dir . "/vhost-" . basename($folder) . ".conf";
            @symlink($src . "/vhost.conf", $conf_file);
        }
        igk_hook("sys://install_site", [$this, $folder]);
        return true;
    }
}
