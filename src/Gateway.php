<?php

namespace Omnipay\PayU;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayU\Message\Notification;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'PayU';
    }

    public function getDefaultParameters()
    {
        return [
            'posId'        => '',
            'secondKey'    => '',
            'clientSecret' => '',
            'ip'           => '',
        ];
    }

    public function getSecondKey()
    {
        return $this->getParameter('secondKey');
    }

    public function setSecondKey($secondKey)
    {
        return $this->setParameter('secondKey', $secondKey);
    }

    public function getPosId()
    {
        return $this->getParameter('posId');
    }

    public function setPosId($posId)
    {
        return $this->setParameter('posId', $posId);
    }

    public function getClientSecret()
    {
        return $this->getParameter('clientSecret');
    }

    public function setClientSecret($clientSecret)
    {
        return $this->setParameter('clientSecret', $clientSecret);
    }

    public function getPosAuthKey()
    {
        return $this->getParameter('posAuthKey');
    }

    public function setPosAuthKey($posAuthKey = null)
    {
        return $this->setParameter('posAuthKey', $posAuthKey);
    }

    public function getIp()
    {
        return $this->getParameter('ip');
    }

    public function setIp($value)
    {
        return $this->setParameter('ip', $value);
    }

    public function getItems()
    {
        return $this->getParameter('items');
    }

    public function setItems($items)
    {
        if ($items && !$items instanceof ItemBag) {
            $items = new PayuItemBag($items);
        }

        return $this->setParameter('items', $items);
    }

    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayU\Message\AuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayU\Message\CompletePurchaseRequest', $parameters);
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayU\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PayU\Message\CompletePurchaseRequest', $parameters);
    }

    public function acceptNotification()
    {
        return new Notification($this->httpRequest, $this->httpClient, $this->getParameter('secondKey'));
    }
}
