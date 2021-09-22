<?php

namespace Omnipay\PayU\Message;

class AuthorizeRequest extends AbstractRequest
{
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getPhone()
    {
        return $this->getParameter('phone');
    }

    public function setPhone($value)
    {
        return $this->setParameter('phone', $value);
    }

    public function getName()
    {
        return $this->getParameter('name');
    }

    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    public function getPayMethod()
    {
        return $this->getParameter('payMethod');
    }

    public function setPayMethod($value)
    {
        return $this->setParameter('payMethod', $value);
    }

    public function getPayMethodValue()
    {
        return $this->getParameter('payMethodValue');
    }

    public function setPayMethodValue($value)
    {
        return $this->setParameter('payMethodValue', $value);
    }

    public function getRecurring()
    {
        return $this->getParameter('recurring');
    }

    public function setRecurring($value)
    {
        return $this->setParameter('recurring', $value);
    }

    public function setPayMethods($value)
    {
        return $this->setParameter('payMethods', $value);
    }

    public function getPayMethods()
    {
        return $this->getParameter('payMethods');
    }

    public function getData()
    {
        $this->getRequestMethod('POST');

        $this->validate('amount');

        $data['customerIp'] = $this->getIp();
        $data['merchantPosId'] = $this->getPosId();
        $data['description'] = $this->getDescription();
        $data['currencyCode'] = strtoupper($this->getCurrency());
        $data['totalAmount'] = $this->toAmount($this->getAmount());
        $data['extOrderId'] = $this->getTransactionId();

        //optional section buyer
        $data['buyer']['email'] = $this->getEmail();
        $data['buyer']['phone'] = $this->getPhone();
        $data['buyer']['firstName'] = $this->getName();

        if ($items = $this->getItems()) {
            $data['products'] = [];

            foreach ($items as $i => $item) {
                if (!empty($item['price']) && !empty($item['quantity']) && !empty($item['name'])) {
                    $data['products'][$i] = [
                        'name'      => $item['name'],
                        'unitPrice' => $this->toAmount($item['price']),
                        'quantity'  => $item['quantity'],
                    ];
                }
            }
        }

        if ($this->getPayMethod()) {
            $data['payMethods']['payMethod'] = [
                'type'  => 'PBL',
                'value' => $this->getPayMethod(),
            ];

            if ($this->getPayMethodValue()) {
                $data['payMethods']['payMethod']['authorizationCode'] = $this->getPayMethodValue();
            }
        }

        if ($this->getRecurring()) {
            $data['recurring'] = $this->getRecurring();
        }

        if ($this->getPayMethods() && \is_array($this->getPayMethods())) {
            $data['payMethods'] = $this->getPayMethods();
        }

        $data['continueUrl'] = $this->getReturnUrl();
        $data['notifyUrl'] = $this->getNotifyUrl();

        return $data;
    }

    protected function getEndpoint()
    {
        return $this->getEndpointUrl().'/api/v2_1/orders';
    }
}
