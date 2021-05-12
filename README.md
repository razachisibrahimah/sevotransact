# SEVOTRANSACT PHP SDK

Interact easily with the [Sevotransact](https://sevotransact.com) API.

## Installation

```shell
composer require razachisibrahimah/sevotransact
```

## Usage

```php
$momo = new MomoCollection;

$amount = 0.2;
$phone = '233...';
$channel = ''; // MTN|VODAFONE|AIRTEL

$response = $momo->pay($amount, $phone, $channel);

$status = $response['status']; // 200
$message = $response['message']; // success
$transactionId = $response['data']['client-reference'];
$code = $response['data']['code']; // 100
$msisdn = $response['data']['msisdn'];
```

## Licence

[MIT](LICENCE)