<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlVisitor.php
// @date: 20230322 19:37:54
namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlItemBase;

///<summary>used to visit html node</summary>
/**
 * used to visit html node
 * @package IGK\System\Html
 */
class HtmlVisitor
{
    /**
     * target node to visit
     * @var HtmlItemBase
     */
    var $target;
    /**
     * end visitor
     * @var  @var callable ($n, bool $first_child, bool $last_child, bool $end):void
     */
    var $endVisitorListener;
    /**
     * 
     * @var callable ($n, $first_child, $has_child):?bool
     */
    var $startVisitorListener;

    /**
     * skip treatment
     * @var mixed
     */
    protected $skip;

    /**
     * skip end flag
     * @var mixed
     */
    protected $skip_end;

    public function __construct(HtmlItemBase $t)
    {
        $this->target = $t;
    }
    /**
     * base visit algorithm
     * @return void 
     */
    public function visit()
    {
        /**
         * @var HtmlNode $n
         */
        $tq = [["n" => $this->target, "visit" => false, "has_child" => false, "first_child" => true, "last_child" => true]];
        $v_startc = $this->startVisitorListener;
        $v_endc = $this->endVisitorListener;
        while (count($tq) > 0) {
            $t = array_shift($tq);
            extract($t, EXTR_OVERWRITE);

            if (!$visit) {
                if (!$n->AcceptRender()) {
                    continue;
                }
                $childs = $n->getRenderedChilds();
                $counter = count($childs);
                $has_child = $counter > 0;
                $check = $v_startc($n, $first_child, $has_child, $last_child);
                if (!$this->skip) {
                    if ($has_child) {
                        if ($check) {
                            array_unshift($tq, [
                                'n' => $n, 'visit' => true,
                                "has_child" => $has_child,
                                "skipend" => is_null($check)
                            ]);
                        }
                        $childs = array_reverse($childs);
                        $counter--;
                        $last = true;
                        foreach ($childs as $v) {
                            array_unshift($tq, [
                                'n' => $v,
                                'visit' => false,
                                "has_child" => null,
                                'last_child' => $last,
                                'first_child' => $counter == 0
                            ]);
                            $counter--;
                            $last = false;
                        }
                        continue;
                    } else if (is_null($check)) {
                        // next item must be consider as first childs 
                        if (count($tq)>0){
                            $tq[0]['first_child'] = true;
                        }  else {
                            $v_endc($this->target, false, true, true);
                        }                       
                        continue;
                    }
                } else {
                    $this->skip = false;
                    if (is_null($check)) {
                        if (!$this->skip_end){
                            $v_endc($n, false, true,count($tq)==0);
                        }
                        $this->skip_end = false;
                        continue;
                    }
                }
            }
            
            if (!$this->skip_end)
                $v_endc($n, $has_child, $last_child, count($tq)==0);
            else{
                $v_end = count($tq)==0;
                if ($v_end){
                    $v_endc($this->target, $has_child, $last_child, true); 
                }
                $this->skip_end = false;  
            } 
        }
    }
}
