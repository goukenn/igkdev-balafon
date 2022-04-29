<?php
// @author: C.A.D. BONDJE DOUE
// @filename: LaravelMix.php
// @date: 20220414 13:46:56
// @desc: laravel mix instataller
namespace IGK\System\Installers;

use IGK\Helper\IO;
use IGK\System\Console\Logger;
use IGKException;

 
class OsWindowCommand extends OsShell{
    public static function Where($cmd){ 
        return exec("where ".$cmd);
    }
}
/**
 * laravel mix installer
 * @package IGK\System\Installers
 */
class LaravelMixInstaller {
    /**
     * handle installer
     * @param mixed $e 
     * @return void 
     * @throws IGKException 
     */
    public static function Handle($e){    

        $options = igk_geto($e->args, "options");
        if (!$options->is_laravel_mix){
            return;
        }
        $installdir = igk_getv($e->args, "folder");
        Logger::info("install laravel-mix");         
        $lib    = "src/application/Lib/igk/Scripts";
        $output = "src/public";
        if ($options->is_primary){
            $lib = "Lib/igk/Scripts";
            $output = "./";
        }
        $out = "const mix  = require('laravel-mix');".PHP_EOL;
$out .= <<<EOF
mix.js('{$lib}/*', 'dist')
    .setOutputPath('{$output}');
EOF;

        // JSON PACKAGE

        $file = igk_glue("/", $installdir, "package.json");
        if (!file_exists($file)){
            $js_data = (object)[
                "private"=>true,
                "scripts"=>(object)[
                    "dev"=>"mix",
                    "prod"=>"mix --production"
                ],
                "devDependencies"=>(object)[
                    "laravel-mix"=>"^latest"
                ]
            ];
            igk_io_w2file($file, json_encode($js_data, JSON_PRETTY_PRINT));
        }


        
        $file = igk_glue("/", $installdir, "webpack.mix.js");
        // igk_io_w2file($file, $out);
        list($npm, $npx) = [OsShell::Where("npm"),
        OsShell::Where("npx")];
        if ($npx){
            $package = (!$options->is_primary ? "src/application" :"")."/Packages";

            $cmd = $npm." --prefix {$package} init -y" ;
            Logger::info($package, $npm);
            Logger::info($cmd);
            
            $bck = getcwd();
            $cdir = $installdir."/".$package;
            IO::CreateDir($cdir);
            chdir($cdir);
            exec($npm." init -y" );
            exec($npm." install laravel-mix --save-dev" );
            exec($npx." mix --n");
            chdir($bck);
        }
        Logger::success("generate : ".$file); 
    }
}