<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeStyleFileCommand.php
// @date: 20221206 09:25:36
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\IO\File\Php\PHPDoc;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGKCssDefaultStyle;
use IGKMedia;

use function igk_resources_gets as __;

///<summary></summary>
/**
* make style file command 
* @package IGK\System\Console\Commands
*/
class MakeStyleFileCommand extends AppExecCommand{
    var $command = "--make:style";
    var $desc = "make style file";

    public function showUsage(){
        Logger::print(sprintf("%s [controller] name", $this->command));
    }
    public function exec($command, ?string $controller=null, ?string $name=null) {
        if (is_null($name)){
            $name = $controller;
            $controller = null;
        }
        $ctrl = null; 
        if ($controller){
            $ctrl = self::GetController($controller, 0) ?? igk_die("require controller");
        }
        $v_outdir = null;
        if ($ctrl){
            $v_outdir = Path::Combine($ctrl->getDeclaredDir(), IGK_STYLE_FOLDER);
        }
        else 
            $v_outdir = getcwd();
        $file = Path::Combine($v_outdir, $name);
        if (igk_io_path_ext($file)!= IGK_DEFAULT_STYLE_EXT){
            $file .= ".".IGK_DEFAULT_STYLE_EXT;
        }

        $sb = new StringBuilder;
        $doc = new PHPDoc;
        $doc->comment("Style builder help generate a dynamic server side CSS");
        $doc->var('$cl', "array", 'store color definition');
        $doc->var('$def', IGKCssDefaultStyle::class);
        $doc->var('$css_m', "?string", 'controller style definition');
        $doc->var('$lg_screen', IGKMedia::class, 'large - media');
        $doc->var('$sm_screen', IGKMedia::class, 'small screen - media');
        $doc->var('$root', "array", 'store global root variable');
        $doc->var('$theme_name', "?string", 'theme require light|dark');        
        $doc->var('$theme', HtmlDocTheme::class);
        $doc->var('$xsm_screen', IGKMedia::class, 'extra small screen - media');
        $doc->var('$xlg_screen', IGKMedia::class, 'extra large screen - media');        
        $sb->appendLine($doc."");
        $sb->appendLine("");

        $sb->appendLine(implode("\n", [
            "// + | --------------------------------------------------------------------",
            "// + | internal parser help generate css style definition.",
            "// + | {sys: classStyle1 [,...classStylen]} get global theme class style  ",
            "// + | [cl: color [, default]] get theme color.",
            "// + | [var: varname [, default]] use var property.",
            "// + | [trans: varname [, default]] apply transition definition.",
            "// + | [transform: varname [, default]] apply transform definition.",
            "// + | [bgcl: color [, default]] get background-color: color",
            "// + | [fcl: color [, default]] get fore-color: color",
            "// + | (:.class_selector) re-use the declared",
            "",
        ]));

        $sb->appendLine('$def[$css_m] = "";');


        $builder = new PHPScriptBuilder;

        $builder->type('function')
        ->defs($sb.'')
        ;

        igk_io_w2file($file, $builder->render());
    }
    
}