<?php

namespace common\components;

use common\models\Request;
use ErrorException;
use Yii;
use yii\base\Component;

class RequestLogger extends Component
{
    private $startTime;
    private $skipUrls = [
        '/',
        '/request-log',
        '/debug/default/toolbar'
    ];

    public function setStartTime()
    {
        $this->startTime = microtime(true);
    }

    public function logRequest()
    {

        try {
            $request = Yii::$app->request;
            $baseUrl = $this->getBaseUrl($request->url);

            if (in_array($baseUrl, $this->skipUrls)) {
                return;
            }

            $params = $this->getParams($request->url);
            $responseCode = Yii::$app->response->statusCode;
            $requestType = $request->method;
            $responseTime = (int)((microtime(true) - $this->startTime) * 1000); // Tiempo de respuesta en milisegundos

            $requestRecord = new Request();
            $requestRecord->url = $baseUrl;
            $requestRecord->params = $params;
            $requestRecord->response_code = $responseCode;
            $requestRecord->response_time = $responseTime;
            $requestRecord->request_type = $requestType;
            $requestRecord->request_date = date('Y-m-d H:i:s');
            $requestRecord->save();
            if ($requestRecord->errors) {
                throw new ErrorException(json_encode($requestRecord->errors));
            };
        } catch (\Exception $e) {
            // Manejo de la excepción, puedes registrar el error o realizar alguna otra acción
            Yii::error('Error al registrar la petición: ' . $e->getMessage(), __METHOD__);
        }
    }

    private function getBaseUrl($url)
    {
        $parsedUrl = parse_url($url);
        return isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
    }

    private function getParams($url)
    {
        $parsedUrl = parse_url($url);
        return !empty($parsedUrl['query']) ? $parsedUrl['query'] : null;
    }
}
