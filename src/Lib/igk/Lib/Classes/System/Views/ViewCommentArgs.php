<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCommandArgs.php
// @date: 20230123 10:38:46
namespace IGK\System\Views;

use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompileProcessCommandHandler;
use IGK\System\Views\Traits\ViewCommentEvalTrait;
use ReflectionException;

///<summary></summary>
/**
* store comment detected on a view 
* @package IGK\System\View
*/
class ViewCommentArgs{
    use ViewCommentEvalTrait;
    private static $sm_info =[]; 
    private $m_entries;
    private $activates = [
        "MainLayout"
    ];
    const COMMENT_EXPRESSION_REGEX =  "/\/\/#\s*\{\{%(?P<expression>.+)%\}\}\s*$/";
    
    /**
     * 
     * @param mixed $comment 
     * @param mixed $file 
     * @return bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Check(string $comment, string $file): bool{
        if (isset(self::$sm_info[$file])){
            return self::$sm_info[$file]->getBool($comment);
        }
        $tab = token_get_all(file_get_contents($file));
        $g = new static;        
        $g->_parseToken($tab);
        self::$sm_info[$file] = $g;
        return $g->getBool($comment);
    }
    public function get($n){
        return igk_getv($this->m_entries , $n);
    }
    public function getBool($n):bool{
        $c = igk_getv($this->m_entries , $n); 
        return boolval($c); 
    }
    private function _parseToken($tab){
        $this->m_entries = [];
        while(count($tab)>0){
            $v = $e = array_shift($tab);
            if (is_array($e)){
                $v = $e[1];
                $e = $e[0];
            }
            switch($e){
                case T_COMMENT:
                    if (preg_match(self::COMMENT_EXPRESSION_REGEX, $v, $data)){                        
                        $this->_evaluate(trim($data['expression']));
                    }
                    break;
            }
        }
    }
    private function _getViewCommandProcess(){
        return null; //  new ViewCompileProcessCommandHandler($this);
    } 
    private function _evaluate(string $expression){
        $this->evalData($expression);
    }   
    public function MainLayout(){
        $this->m_entries['@'.__FUNCTION__.'()'] = true;
    }
}