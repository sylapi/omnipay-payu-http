<?php

namespace Omnipay\PayU\Message;

class CompletePurchaseNotifyRequest extends AbstractRequest
{
    public function getData()
    {
        $data = parent::getData();

        return $data;
    }

    public function isSuccessful()
    {
        $order = $this->httpRequest->request->get('order');

        if (!empty($order['status']) && $order['status'] == 'COMPLETED') {
            return true;
        }

        return false;
    }

    public function getMessage()
    {
        $order = $this->httpRequest->request->get('order');

        if (!empty($order['status'])) {
            return $order['status'];
        }

        return 'NONE';
    }

    public function getStatus()
    {
        $order = $this->httpRequest->request->get('order');

        if (!empty($order['status'])) {
            return $order['status'];
        }

        return 'NONE';
    }
}
