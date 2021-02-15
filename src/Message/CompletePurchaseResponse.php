<?php

namespace Omnipay\PayU\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

class CompletePurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $requestId = null;

    protected $headers = [];

    public function __construct(RequestInterface $request, $data, $headers = [])
    {
        $this->request = $request;
        $this->data = $data;
        $this->headers = $headers;
    }

    public function isSuccessful()
    {
        if (!empty($this->data['status']['statusCode']) && $this->data['status']['statusCode'] == 'SUCCESS') {
            return true;
        }

        return false;
    }

    public function getResponse()
    {
        if ($this->isSuccessful()) {
            return $this->data;
        }

        return null;
    }

    public function getMessage()
    {
        if (isset($this->data['error_description'])) {
            return $this->data['error_description'];
        } elseif (isset($this->data['message'])) {
            return $this->data['message'].': '.$this->data['error'];
        } elseif (isset($this->data['status']['statusDesc'])) {
            return $this->data['status']['statusDesc'];
        }

        return null;
    }

    public function getCode()
    {
        if (isset($this->data['status']['statusCode'])) {
            return $this->data['status']['statusCode'];
        } elseif (isset($this->data['status']) && is_numeric($this->data['status'])) {
            return $this->data['status'];
        }

        return null;
    }

    public function isRedirect()
    {
        return false;
    }

    public function redirect()
    {
        return false;
    }

    public function getRedirectUrl()
    {
        return null;
    }

    public function getTransactionReference()
    {
        $reference = $this->data['orders'][0]['orderId'];

        if (empty($reference)) {
            $reference = (!empty($_SESSION['OmniPay_PayU_OrderId'])) ? $_SESSION['OmniPay_PayU_OrderId'] : null;
        }

        $_SESSION['OmniPay_PayU_OrderId'] = null;

        return $reference;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }
}
