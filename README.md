# Omnipay: PayU

**Paylane driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Dummy support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "sylapi/payu": "~1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* PayU


For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

### Basic purchase example

```php
$gateway = \Omnipay\Omnipay::create('PayU');  
$gateway->setPosId('12345');
$gateway->setSecondKey('12345ABCD');
$gateway->setClientSecret('12345ABCD');

$response = $gateway->purchase(
    [
        "amount" => "10.00",
        "currency" => "PLN",
        "description" => "My Payment",
        "transactionId" => "12345",
        "returnUrl" => "https://webshop.example.org/mollie-return.php",
        "email" => "email@example.com",
        "name" => "Jan Kowalski",
        "payMethod" => "m",
        "items" => [
            [
                "name" => "Product name",
                "price" => "10.00",
                "quantity" => 1
            ]
        ]
    ]
)->send();

// Process response
if ($response->isSuccessful()) {

    // Payment was successful
    print_r($response);

} elseif ($response->isRedirect()) {

    // Redirect to offsite payment gateway
    $response->redirect();

} else {

    // Payment failed
    echo $response->getMessage();
}
```

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/sylapi/omnipay-payu/issues),
or better yet, fork the library and submit a pull request.
