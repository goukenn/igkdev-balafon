<?php
// @file: IGKDbExpression.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Database;

use IGKObject;


class DbExpression extends IGKObject implements IHtmlGetValue{
    protected $m_v;
    ///<summary></summary>
    ///<param name="value"></param>
    public function __construct($value=null){
        $this->m_v=$value;
    }
    ///<summary>Represente Create function</summary>
    ///<param name="expression"></param>
    public static function Create($expression){
        $g=new static($expression);
        $g->m_v=$expression;
        return $g;
    }
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    public function getValue($o=null){
        return $this->m_v;
    }
}
