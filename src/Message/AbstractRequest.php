<?php

namespace Omnipay\PayU\Message;

use GuzzleHttp\Client;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://secure.payu.com';
    protected $testEndpoint = 'https://secure.snd.payu.com';

    protected $tokenResonseFailure = null;

    public function getIp()
    {
        return $this->getParameter('ip');
    }

    public function setIp($value)
    {
        return $this->setParameter('ip', $value);
    }

    public function getPosId()
    {
        return $this->getParameter('apiKey');
    }

    public function setPosID($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getClientSecret()
    {
        return $this->getParameter('clientSecret');
    }

    public function getSecondKey()
    {
        return $this->getParameter('secondKey');
    }

    public function setSecondKey($secondKey)
    {
        return $this->setParameter('secondKey', $secondKey);
    }

    public function setClientSecret($clientSecret)
    {
        return $this->setParameter('clientSecret', $clientSecret);
    }

    public function getOrderId()
    {
        return (!empty($_SESSION['OmniPay_PayU_OrderId'])) ? $_SESSION['OmniPay_PayU_OrderId'] : $this->getParameter('order_id');
    }

    public function setOrderId($value)
    {
        $_SESSION['OmniPay_PayU_OrderId'] = $value;

        return $this->setParameter('order_id', $value);
    }

    public function getItems()
    {
        return $this->getParameter('items');
    }

    public function setItems($items)
    {
        return $this->setParameter('items', $items);
    }

    protected function getAccessToken()
    {
        if (!$this->getToken()) {
            $header = ['Content-type' => 'application/json'];
            $uri = 'grant_type=client_credentials&client_id='.$this->getPosId().'&client_secret='.$this->getClientSecret();

            $httpResponse = $this->httpClient->request('GET', $this->getEndpointUrl().'/pl/standard/user/oauth/authorize?'.$uri, $header);
            $responseBody = json_decode($httpResponse->getBody()->getContents(), true);

            if (!empty($responseBody['access_token'])) {
                $this->setToken($responseBody['access_token']);
            } else {
                $this->tokenResonseFailure = $responseBody;
            }
        }

        return $this->getToken();
    }

    public function getRequestMethod()
    {
        $method = $this->getParameter('request_method');

        return ($method) ? $method : 'POST';
    }

    public function setRequestMethod($value)
    {
        return $this->setParameter('request_method', $value);
    }

    protected function toAmount($value)
    {
        if (!empty($value)) {
            return (int) round($value * 100);
        }

        return '';
    }

    public function getHeaders()
    {
        $token = $this->getAccessToken();

        $headers = [
            'Content-type'    => 'application/json',
            'Authorization'   => 'Bearer '.$token,
            'allow_redirects' => false,
        ];

        return $headers;
    }

    public function sendData($data)
    {
        $headers = $this->getHeaders();

        if (empty($this->tokenResonseFailure)) {
            $httpResponse = (new Client())->request(
                $this->getRequestMethod(),
                $this->getEndpoint(),
                [
                    'allow_redirects' => false,
                    'headers'         => $headers,
                    'body'            => \json_encode($data),
                ]
            );
            $responseBody = json_decode($httpResponse->getBody()->getContents(), true);
        } else {
            $responseBody = $this->tokenResonseFailure;
        }

        return $this->createResponse($responseBody, []);
    }

    protected function createResponse($data, $headers = [])
    {
        if (!empty($data['orderId'])) {
            $this->setOrderId($data['orderId']);
        }

        return $this->response = new Response($this, $data, $headers);
    }

    protected function getEndpointUrl()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
