<?php
class ItemController extends BaseController
{
      /** 
    * "/item/generateToken" метод для генерации нового токена в СУБД 
    */
     public function generateTokenAction()
    {
       $strErrorDesc = '';
       $token = md5(microtime() . 'slat' . time());
       $tokenModel = new TokenModel();
       $insToken = $tokenModel->insertToken($token);
       print_r( "Токен сгенерирован: " . $token);  
    }
     /** 
    * метод поиска токена в СУБД 
    */
    private function searchToken($token)
    {
       $tokenModel = new TokenModel();
       if ($tokenModel->getToken($token)){
          return true;
       } else {
        return false;
       }
    }
    /** 
    * "/item/list" метод получения всех пользователей 
    */
    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        $arrQueryStringParams = $this->getQueryStringParams();

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $itemModel = new ItemModel();
                $intLimit = 10;
                $strToken = "";
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                if (isset($arrQueryStringParams['token']) && $arrQueryStringParams['token']) {
                    $strToken = $arrQueryStringParams['token'];
                }

                $responseData = null;

                if ( $this->searchToken($strToken)) {
                    $arrItems = $itemModel->getItems($intLimit);
                    $responseData = json_encode($arrItems);
                } else {
                  throw new Error("Error Processing Request. ");
                } 
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // вывод
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
        return $responseData;
    }

    public function listIdAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        $arrQueryStringParams = $this->getQueryStringParams();

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $itemModel = new ItemModel();
                $intLimit = 10;
                $strToken = "";
               
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }

                if (isset($arrQueryStringParams['id']) && $arrQueryStringParams['id']) {
                    $intId = $arrQueryStringParams['id'];
                }

                if (isset($arrQueryStringParams['token']) && $arrQueryStringParams['token']) {
                    $strToken = $arrQueryStringParams['token'];
                }

                if (!isset($intId)) {
                    throw new Error("Error Processing Request. ");
                }

                $responseData = null;

                if ($this->searchToken($strToken)) {
                    $arrItems = $itemModel->getByIdItems($intLimit,  $intId);
                    $responseData = json_encode($arrItems);
                } else {
                    throw new Error("Error Processing Request. ");
                } 
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // вывод
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function createAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
     
        $arrQueryStringParams = $this->getQueryStringParams();
        parse_str($_SERVER['QUERY_STRING'], $arrQueryStringParams);

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $itemModel = new ItemModel();
                $intLimit = 10;
                $intKey = 0;
                $strName = "";
                $strPhone = "";
                $strToken = "";

                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                if (isset($arrQueryStringParams['name']) && $arrQueryStringParams['name']) {
                    $strName = $arrQueryStringParams['name'];
                }
                if (isset($arrQueryStringParams['phone']) && $arrQueryStringParams['phone']) {
                    $strPhone = $arrQueryStringParams['phone'];
                }
                if (isset($arrQueryStringParams['key']) && $arrQueryStringParams['key']) {
                    $intKey = $arrQueryStringParams['key'];
                }

                if (isset($arrQueryStringParams['token']) && $arrQueryStringParams['token']) {
                    $strToken = $arrQueryStringParams['token'];
                }
                $responseData = null;

                if (//preg_match('/^\pL+$/u', $strName )  &&
                   preg_match('^((\+7|7|8)+([0-9]){10})$^',  $strPhone ) 
                    && preg_match('^[0-9]+[0-9]*$^', $intKey )  
                    && 
                    $this->searchToken($strToken)
                ) {
                  $arrItems = $itemModel->createItems($strName, $strPhone, $intKey);
                   $responseData = json_encode("Запись добавлена");
               } else {
                  throw new Error("Error Processing Request. ");
                } 
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Ошибка в запросе. Проверьте параметры в запросе';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // вывод
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
        return $responseData;
    }

    public function deleteAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();


        if (strtoupper($requestMethod) == 'GET') {
            try {
                $itemModel = new ItemModel();
               
                $intId = 0;
                $strToken = "";

                if (isset($arrQueryStringParams['id']) && $arrQueryStringParams['id']) {
                    $intId = $arrQueryStringParams['id'];
                }


                if (isset($arrQueryStringParams['token']) && $arrQueryStringParams['token']) {
                    $strToken = $arrQueryStringParams['token'];
                }
                 $responseData = null;

                if (preg_match('^[0-9]+[0-9]*$^', $intId )  && $this->searchToken($strToken)) {
                    $arrItems = $itemModel->deleteItems($intId);
                    $responseData = json_encode("Запись удалена");
                } else {
                  throw new Error("Error Processing Request. ");
                }

                
             
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Ошибка в запросе. Проверьте параметры в запросе';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // вывод
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
        return $responseData;
    }

    public function updateAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        parse_str($_SERVER['QUERY_STRING'], $arrQueryStringParams);

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $itemModel = new ItemModel();
                $intLimit = 10;
                $intKey = 0;
                $strName = "";
                $strPhone = "";
                $strToken = "";
                $intId = 0;
                $updateDate = date('Y-m-d H:i:s', time());

                 if (isset($arrQueryStringParams['id']) && $arrQueryStringParams['id']) {
                     $intId = $arrQueryStringParams['id'];
                 }

                 if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                     $intLimit = $arrQueryStringParams['limit'];
                 }
                 if (isset($arrQueryStringParams['name']) && $arrQueryStringParams['name']) {
                     $strName = $arrQueryStringParams['name'];
                 }
                 if (isset($arrQueryStringParams['phone']) && $arrQueryStringParams['phone']) {
                     $strPhone = $arrQueryStringParams['phone'];
                 }
                 if (isset($arrQueryStringParams['key']) && $arrQueryStringParams['key']) {
                     $intKey = $arrQueryStringParams['key'];
                 }

                if (isset($arrQueryStringParams['token']) && $arrQueryStringParams['token']) {
                   
                    $strToken = $arrQueryStringParams['token'];
                }
                 $responseData = null;

                 if (//preg_match('/^\pL+$/u', $strName ) && 
                    preg_match('^((\+7|7|8)+([0-9]){10})$^',  $strPhone ) 
                    && preg_match('^[0-9]+[0-9]*$^', $intKey )  && $this->searchToken($strToken)) {
                    $arrItems = $itemModel->updateItems($intId, $strName, $strPhone, $intKey, $updateDate);
                    $responseData = json_encode("Запись обновлена");
                } else {
                  throw new Error("Error Processing Request. ");
                }  
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
              }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // вывод
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
        return $responseData;
    }
}