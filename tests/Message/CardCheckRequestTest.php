<?php
namespace Omnipay\Paylane\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class CardCheckRequestTest extends TestCase
{
    protected $request;

    public function testGetData()
    {
        $card = new CreditCard($this->getValidCard());

        $this->request = new CardCheckRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'card'          => $card
        ));

        $data = $this->request->getData();

        $this->assertSame($card->getNumber(), $data['card_number']);
    }
}