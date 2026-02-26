<?php
// @author: C.A.D. BONDJE DOUE
// @file: WebDocument.php
// @date: 20230918 19:28:20
namespace IGK\System\Html;
use IGK\System\Html\Dom\HtmlNode;
use IGKObject;
/**
* represent a simple web document 
* @package IGK\System\Html
*/
class WebDocument extends IGKObject{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_html_document;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_head;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_body;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_title;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_charset;

    /**
    * auto generate doc.
    * @var mixed
    */
    public $docType = 'html';

    /**
    * auto generate doc.
    * @var mixed
    */
    public $charset = 'UTF-8';

    /**
    * .ctr
    */
    public function __construct()
    {
        $this->_initialize();
    }

    /**
    * auto generate doc.
    */
    public function getHead(){return $this->m_head; }

    /**
    * auto generate doc.
    */
    public function getBody(){return $this->m_body; }

    /**
    * auto generate doc.
    */
    protected function _initialize(){
        $this->m_html_document = new HtmlNode('html');
        $this->m_head = $this->m_html_document->head();
        $this->m_body = $this->m_html_document->body();
        $this->m_charset = $this->m_head->meta();
        $this->m_charset['charset'] = new HtmlAttributeValueListener(function(){
            return $this->charset;
        });
    }

    /**
    * auto generate doc.
    * @param string $title
    */
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