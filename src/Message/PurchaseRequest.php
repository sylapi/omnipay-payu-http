<?php

namespace Omnipay\PayU\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData()
    {
        $data = parent::getData();

        return $data;
    }

    protected function getEndpoint()
    {
        return $this->getEndpointUrl().'/api/v2_1/orders';
    }
}
