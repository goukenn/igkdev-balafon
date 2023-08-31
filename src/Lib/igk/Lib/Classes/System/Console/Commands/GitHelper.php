<?php
// @author: C.A.D. BONDJE DOUE
// @filename: GitHelper.php
// @date: 20220309 14:44:49
// @desc: Git Helper

namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Console\Commands
 */
class GitHelper
{
    ///<summary>Generate project git ignore file </summary>
    /**
     * Generate project git ignore file 
     */
    public static function Generate(&$bind, $dir, $name = null, $author = null, $desc = null, ?array $ignorelist = null)
    {
        if (is_null($author)){
            $author = IGK_AUTHOR;
        }
        $init_git = function()use($dir){
            if (igk_is_function_disable("exec"))
            {
                return "false";
            }
            $bck =getcwd();
            chdir($dir);
            if (is_dir($git_dir = $dir."/.git")){
                IO::RmDir($git_dir);
            }
            exec("git init");
            exec("git add . ");
            exec("git commit -m'init'");
            exec("git branch develop && git branch release");
            chdir($bck);
            Logger::success("init git....");
        };
        $bind[$dir . "/.gitignore"] = function ($file) use ($author, $ignorelist) {
            igk_io_w2file(
                $file,
                implode("\n", array_merge([
                    "**/.vscode/*",
                    "Data/**",
                    ".gitignore"
                ], $ignorelist ?? []))
            );
        };
        $bind[$dir . "/README.md"] = function ($file) use ($name, $author, $desc, $init_git ) {
            igk_io_w2file(
                $file,
                implode("\n", [
                    "# {$name}",
                    "$desc ",
                    "@" . str_replace(" ", "", $author)
                ])
            );
            $init_git ();
        };
    }
}
