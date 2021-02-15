<?php

namespace Omnipay\PayU\Message;

class CompletePurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->setRequestMethod('GET');

        return [];
    }

    protected function getEndpoint()
    {
        return $this->getEndpointUrl().'/api/v2_1/orders/'.urlencode($this->getOrderId());
    }

    public function sendData($data)
    {
        $headers = $this->getHeaders();

        if (empty($this->tokenResonseFailure)) {
            $httpResponse = $this->httpClient->request('GET', $this->getEndpoint(), $headers);
            $responseBody = json_decode($httpResponse->getBody()->getContents(), true);
        } else {
            $responseBody = $this->tokenResonseFailure;
        }

        return $this->response = new CompletePurchaseResponse($this, $responseBody, []);
    }
}
