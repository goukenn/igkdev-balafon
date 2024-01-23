<?php
// @author: C.A.D. BONDJE DOUE
// @file: CurlHttpClient.php
// @date: 20230913 07:31:13
namespace IGK\System\Http;

use IGK\System\IO\Path;
use IGKException;
use IGKValidator;

///<summary></summary>
/**
* represent a core curl http client
* @package IGK\System\Http
*/
class CurlHttpClient implements IHttpClient{
    const HEADER_CONTENT_TYPE= 1048594;
    const HEADER_STATUS = 2097154;
    /**
     * enable use of session id
     * @var ?bool
     */
    var $session;
    /**
     * follow location
     * @var bool
     */
    var $followLocation = false;

    /**
     * accept content type
     * @var string
     */
    var $accept = '*/*';

    /**
     * get or set base uri
     * @var ?string
     */
    var $base;

    /**
     * base controller 
     * @var mixed
     */
    var $controller;

    /**
     * get request status
     * @var mixed
     */
    private $m_status = -1;
    private $m_requestInfo = null;
    /**
     * header to pass 
     * @var mixed
     */
    private $m_headers;

    public function __destruct()
    {
        if ($this->m_session_file){
            @unlink($this->m_session_file);
            $this->m_session_file = null;
        }
    }
    /**
     * info to get
     * @return null|array 
     */
    public function getRequestHeaderResponse(): ?array {
        return $this->m_requestInfo; 
    }

    /**
     * get last request status code
     * @return mixed 
     */
    public function getStatus():int{
        return $this->m_status;
    }

    public function download(string $url, IHttpClientOptions $options) { }

    public function get(string $url) { }

    public function post(string $url, array $data = []) { } 

    /**
     * 
     * @param string $url 
     * @return  
     */
    public function request(string $url){
        $v_is_uri = IGKValidator::IsUri($url);
        if ($this->controller){
            if (!$v_is_uri){

                // get inline resource
                $path = explode('?', Path::Combine(igk_io_basedir(), $url))[0];

                if (file_exists($f = $path)){
                    $response = file_get_contents($f);
                    $ext = igk_io_path_ext($f);
                    HttpUtility::GetContentTypeFromExtension($ext);
                    $this->m_status = 200;
                    $this->m_requestInfo = [
                        'Content-Type'=>HttpUtility::GetContentTypeFromExtension($ext)
                    ];
                     
                    return $response;
                }

                // $view = $this->controller->getViewFile($url);
                // try handle request - 
                // $this->controller->setCurrentView($url);
                // $doc = $this->controller->getDoc();
                // return $doc->render();
            }
        }

        if (!$v_is_uri && $this->base){
            $url = igk_uri(Path::Combine($this->base, $url));
        }
        return $this->_sendRequest($url);
    }
    /**
     * @param string $url the uri to request
     * @param ?array $args the parameters
     * @return ?string the response  
     */
    public function _sendRequest(string $url, $args=null){
        if (!function_exists('igk_curl_post_uri')){
            igk_die("missing : igk_curl_post_uri");
        }
        $this->m_status = -1;
        $c = igk_curl_post_uri($url, $args, $this->_getOptions(), $this->_getHeaders());
        $this->m_status = igk_curl_status();

        $t_info = igk_curl_info();          
        $this->m_requestInfo = $t_info;        
        if ($v_cookie_list = igk_getv($t_info, 'Cookie-List')){
            $t = implode("\n", $v_cookie_list);
            if (preg_match('/(?P<name>[^\s]+)\s+(?P<value>[^\s]+)$/m', $t, $tab)){
                $this->m_session_id = $tab['value'];
                $this->m_session_name = $tab['name'];
            }
        }
        return $c;
    }
    /**
     * 
     * @param mixed $key 
     * @return mixed 
     * @throws IGKException 
     */
    public static function ConvertCurlOptionKeyToName($key){
        return igk_getv([                
                self::HEADER_STATUS => 'Status',
                self::HEADER_CONTENT_TYPE=>'Content-Type',                
                CURLOPT_HTTPHEADER=>"Headers", 
        ], $key);
    }

    /**
     * get headers
     * @return null|array 
     */
    private function _getHeaders(): ?array{
        $v_headers = [];
        if ($this->accept){
            $v_headers[] = 'Accept: '.$this->accept;
        }
        $this->m_headers = $v_headers;
        return $this->m_headers;
    }
    /**
     * get options
     * @return array 
     */
    private function _getOptions(){
        $options = [];
        if($this->followLocation){
            $options[CURLOPT_FOLLOWLOCATION] = 1;
        }
        if ($this->session){
            $this->m_session_file = $this->m_session_file ??  igk_io_tempfile('sess_');
            $options[CURLOPT_COOKIESESSION] = true;
            $options[CURLOPT_COOKIEFILE] = $this->m_session_file;
            $options['session_id'] = $this->m_session_id;
            $options['session_name'] = $this->m_session_name;
        }
        return $options;
    }
    private $m_session_file;
    private $m_session_id;
    private $m_session_name;
}