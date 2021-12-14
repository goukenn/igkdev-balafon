<?php
namespace IGK\System\Console\Commands;


/**
 * 
 * @package IGK\System\Console\Commands
 */
class GitHelper{
    ///<summary>Generate project git ignore file </summary>
    /**
     * Generate project git ignore file 
     */
    public static function Generate(& $bind, $dir, $name=null, $author=null, $desc=null){
        $bind[$dir."/.gitignore"]= function($file)use($author){              
            igk_io_w2file( $file, 
            implode("\n", [
                "**/.vscode/*",
                "Data/**",
                ".gitignore"
            ])
            );
        };  
        $bind[$dir."/README.md"]= function($file)use($name, $author, $desc){              
            igk_io_w2file( $file, 
            implode("\n", [
                "** {$name}",
                "$desc ",
                "@".str_replace (" ", "", $author )
            ])
            );
        };  
    }
}