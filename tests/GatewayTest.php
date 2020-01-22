<?php

namespace Omnipay\PayU;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;

class GatewayTest extends GatewayTestCase
{

    protected $gateway;
    public $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'posId'        => '123456',
            'secondKey'    => '12345abcdABCD',
            'clientSecret' => '123abc456DEF',
            'secondKey' => '123abc456DEF',
            'ip' => '127.0.0.1',
            'amount'      => '10.12',
            'currency'    => 'PLN',
            'description' => 'description',
            'transactionId' => '1234567',
            'email' => 'name@example.com',
            'name' => 'Jan Kowalski',
            'products' => [
                [
                    'name' => 'Name',
                    'amount' => '10.12',
                    'quantity' => '1',
                ]
            ],
        );
    }

    public function testAuthorize()
    {
        $options = array(
            'amount'      => '10.12',
            'currency'    => 'PLN',
            'description' => 'description',
            'transactionId' => '1234567',
            'email' => 'name@example.com',
            'name' => 'Jan Kowalski',
            'items' => [
                [
                    'name' => 'Name',
                    'price' => '10.12',
                    'quantity' => '1',
                ]
            ],
        );
        $request = $this->gateway->authorize($options);

        $this->assertInstanceOf('\Omnipay\PayU\Message\AuthorizeRequest', $request);
        $this->assertSame($options['amount'], $request->getAmount());
        $this->assertSame($options['currency'], $request->getCurrency());
        $this->assertSame($options['description'], $request->getDescription());
        $this->assertSame($options['transactionId'], $request->getTransactionId());
        $this->assertSame($options['email'], $request->getEmail());
        $this->assertSame($options['name'], $request->getName());

        foreach($request->getItems() as $id => $item) {

            $this->assertSame($options['items'][$id]['name'], $item['name']);
            $this->assertSame($options['items'][$id]['price'], $item['price']);
            $this->assertSame($options['items'][$id]['quantity'], $item['quantity']);
        }
    }

    public function authorizeSuccess() {

        $this->setMockHttpResponse('AuthorizeSuccess.txt');

        $response = $this->gateway->authorize($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getMessage());

        $this->assertSame('SUCCESS', $response->getData()['status']['statusCode']);
        $this->assertSame('12345ABCD', $response->getData()['orderId']);
        $this->assertSame('123456', $response->getData()['extOrderId']);
    }

    public function authorizeFailure() {

        $this->setMockHttpResponse('AutorizeFailure.txt');

        $response = $this->gateway->authorize($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotNull($response->getMessage());

        $this->assertSame('8033', $response->getCode());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getMessage());

        $this->assertSame('SUCCESS', $response->getData()['status']['statusCode']);
        $this->assertSame('12345ABCD', $response->getData()['orderId']);
        $this->assertSame('123456', $response->getData()['extOrderId']);
    }


    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotNull($response->getMessage());

        $this->assertSame('8033', $response->getCode());
    }


    public function testCompletePurchaseSuccess()
    {
        $this->setMockHttpResponse('CompletePurchaseSuccess.txt');

        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
       // $this->assertFalse($response->isRedirect());

        $this->assertSame('12345ABCD', $response->getTransactionReference());
    }


    public function testCompletePurchaseFailure()
    {
        $this->setMockHttpResponse('CompletePurchaseFailure.txt');

        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNotNull($response->getMessage());

        $this->assertSame('DATA_NOT_FOUND', $response->getCode());
    }


    //=========================================================================//

    public function testAuthorizeParameters()
    {
        if ($this->gateway->supportsAuthorize()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                $getter = 'get'.ucfirst($key);
                $setter = 'set'.ucfirst($key);
                $value = $this->getParameterValue($key);
                $this->gateway->$setter($value);
                $request = $this->gateway->authorize();
                $this->assertSame($value, $request->$getter());
            }
        }
    }
    public function testPurchaseParameters()
    {
        foreach ($this->gateway->getDefaultParameters() as $key => $default) {
            $getter = 'get'.ucfirst($key);
            $setter = 'set'.ucfirst($key);
            $value = $this->getParameterValue($key);
            $this->gateway->$setter($value);
            $request = $this->gateway->purchase();
            $this->assertSame($value, $request->$getter());
        }
    }
    public function testCompleteAuthorizeParameters()
    {
        if ($this->gateway->supportsCompletePurchase()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                $getter = 'get'.ucfirst($key);
                $setter = 'set'.ucfirst($key);
                $value = $this->getParameterValue($key);
                $this->gateway->$setter($value);
                $request = $this->gateway->completePurchase();
                $this->assertSame($value, $request->$getter());
            }
        }
    }
    public function testCompletePurchaseParameters()
    {
        if ($this->gateway->supportsCompletePurchase()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                $getter = 'get'.ucfirst($key);
                $setter = 'set'.ucfirst($key);
                $value = $this->getParameterValue($key);
                $this->gateway->$setter($value);
                $request = $this->gateway->completePurchase();
                $this->assertSame($value, $request->$getter());
            }
        }
    }
    protected function getParameterValue($key = '')
    {
        if ($key == 'merchantId') {
            $value = mt_rand(32767, mt_getrandmax());
        } elseif ($key == 'method') {
            $value = (rand(0, 1) ? 'POST' : 'GET');
        } else {
            $value = uniqid();
        }
        return $value;
    }
}
