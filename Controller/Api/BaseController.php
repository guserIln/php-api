<?php
class BaseController
{
    /** 
    * __call magic method. 
    */
    public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    /** 
    * Получить части URI. 
    * 
    * @return array 
    */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        return $uri;
    }

    /** 
    * Получить querystring параметры. 
    * 
    * @return array 
    */
    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }

    /** 
    * Отправить API вывод. 
    * 
    * @param mixed $data 
    * @param string $httpHeader 
    */
    protected function sendOutput($data, $httpHeaders=array())
    {
        header_remove('Set-Cookie');
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
    }
}