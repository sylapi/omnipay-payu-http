<?php

namespace Omnipay\Paylane\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    protected $request;

    public function testGetData()
    {
        $card = new CreditCard($this->getValidCard());
        $card->setStartMonth(10);
        $card->setStartYear(2020);

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'ip'            => '127.0.0.1',
            'amount'        => '12.00',
            'card'          => $card,
        ]);

        $data = $this->request->getData();

        $this->assertSame('127.0.0.1', $data['customer']['ip']);
        $this->assertSame(12.00, $data['sale']['amount']);
        $this->assertSame($card->getNumber(), $data['card']['card_number']);
        $this->assertSame((($card->getExpiryMonth() < 10) ? '0' : '').$card->getExpiryMonth(), $data['card']['expiration_month']);
        $this->assertSame($card->getExpiryYear(), $data['card']['expiration_year']);
        $this->assertSame($card->getCvv(), $data['card']['card_code']);
    }
}
