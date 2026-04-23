<?php
// @author: C.A.D. BONDJE DOUE
// @file: Html5Document.php
// @date: 20230417 10:07:04
namespace IGK\System\Html\Dom;
use IGK\System\IO\StringBuilder;
use IGKObject;

/**
* html5 document helper
* @package IGK\System\Html\Dom
*/
class Html5Document extends IGKObject{
    /**
    * Property: html.
    * @var mixed
    */
    private $m_html;
    /**
    * Property: body.
    * @var mixed
    */
    private $m_body;
    /**
    * Property: head.
    * @var mixed
    */
    private $m_head;
    /**
    * Returns Body.
    */
    public function getBody(){
        return $this->m_body;
    }
    /**
    * Returns Head.
    */
    public function getHead(){
        return $this->m_head;
    }
    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_head = new HtmlNode("head");
        $this->m_body = new HtmlNode("body");
        $this->m_html = new HtmlNode("html");
        $this->m_html->add($this->m_head);
        $this->m_html->add($this->m_body);
        $this->m_html['lang']="en";
        $this->setCharset('utf-8');
        $this->setMeta('viewport', 'width=device-width, initial-scale=1.0');
    }
    /**
    * Sets Charset.
    * @param mixed $value
    */
    public function setCharset($value){
        $this->m_head->add("meta")->setAttributes(["charset"=>$value]);
        return $this;
    }
    /**
    * Sets Meta.
    * @param mixed $name
    * @param mixed $content
    */
    public function setMeta($name, $content){
        $this->m_head->add("meta")->setAttributes(["name"=>$name, "content"=>$content]);
        return $this;
    }
    /**
    * Sets Title.
    * @param null|string $title
    */
    public function setTitle(?string $title){
        $t = igk_getv($this->m_head->getElementsByTagName('title'), 0) ?? $this->m_head->add('title');
        $t->setContent($title);
        return $this;
    }
    /**
    * Renders.
    */
    public function render(){
        $sb = new StringBuilder();
        $sb->appendLine("<!DOCTYPE html>");
        $sb->appendLine($this->m_html->render());
        return $sb."";
    }
}