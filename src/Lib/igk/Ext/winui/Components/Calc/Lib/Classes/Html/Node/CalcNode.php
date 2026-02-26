<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CalcNode.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Ext\WinUI\Components\Calc\Html\Node;

use IGK\System\Html\Dom\HtmlNode;
use IGK\ValueListener;

/**
* Calc node.
* @package IGK\Ext\WinUI\Components\Calc\Html\Node
*/
final class CalcNode extends HtmlNode
{

    /**
    * Property: mode.
    * @var mixed
    */
    private $m_mode;

    /**
    * Property: value.
    * @var mixed
    */
    private $m_value;

    /**
    * Returns Mode.
    */

    public function getMode(){return $this->m_mode; }

    /**
    * Sets Mode.
    * @param mixed $v
    */

    public function setMode($v){$this->m_mode = $v; return $this; }

    /**
    * Returns Value.
    */

    public function getValue(){return $this->m_value; }

    /**
    * Sets Value.
    * @param mixed $v
    */

    public function setValue($v){$this->m_value = $v; return $this; }

    /**
    * .ctr
    */
    public function __construct(){
		parent::__construct("div");
		$this["class"]="igk-calc";

	}

    /**
    * Initializes View.
    */

    public function initView(){
		$this->clearChilds();
		//model de vuew
		$frm = $this->addForm();
		$dv = $frm->div();
		//$dv->addLabel("clValue")->Content = R::ngets("lb.verser");
		$i = $dv->addInput("clValue", "text", new ValueListener($this, "Value"))->setAttribute("default-v",new ValueListener($this, "Value"));
		$i["class"] = "+alignr";
		$frm->div()->add("span")->Content = "0";
	}
}