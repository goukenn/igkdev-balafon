<?php
// @author: C.A.D. BONDJE DOUE
// @file: WebDocument.php
// @date: 20230918 19:28:20
namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlNode;
use IGKObject;

///<summary></summary>
/**
* represent a simple web document 
* @package IGK\System\Html
*/
class WebDocument extends IGKObject{
    private $m_html_document;
    private $m_head;
    private $m_body;
    private $m_title;
    private $m_charset;

    public $docType = 'html';
    public $charset = 'UTF-8';

    public function __construct()
    {
        $this->_initialize();
    }
    public function getHead(){return $this->m_head; }
    public function getBody(){return $this->m_body; }
    protected function _initialize(){
        $this->m_html_document = new HtmlNode('html');
        $this->m_head = $this->m_html_document->head();
        $this->m_body = $this->m_html_document->body();

        $this->m_charset = $this->m_head->meta();
        $this->m_charset['charset'] = new HtmlAttributeValueListener(function(){
            return $this->charset;
        });
    }
    public function setTitle(string $title){
        $t = $this->m_title ?? $this->m_title = $this->m_head->title();
        $t->Content = $title;
        return $this;
    }
    /**
     * render web document
     * @param mixed $options 
     * @return string 
     */
    public function render($options=null){
        return implode("\n",[
            '<!DOCTYPE '.$this->docType.'>',
            $this->m_html_document->render($options)
        ]);
    }

}