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
     * end visitor listener 
     * @var mixed
     */
    var $endVisitorListener;
    /**
     * 
     * @var callable ($n, $first_child, $has_child):?bool
     */
    var $startVisitorListener;

    protected $skip;

    public function __construct(HtmlItemBase $t)
    {
        $this->target = $t;
    }
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
                        continue;
                    }
                } else {
                    $this->skip = false;
                    if (is_null($check)) {
                        $v_endc($n, false, true);
                        continue;
                    }
                }
            }
            $v_endc($n, $has_child, $last_child);
        }
    }
}
