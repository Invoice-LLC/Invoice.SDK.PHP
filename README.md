## Invoice.SDK.PHP
В репозитории находятся SDK для интеграции и простой пример для интеграции Invoice, используя PHP

<h3>Примеры</h3>

[Более подробная информация по работе API](https://dev.invoice.su)

API ключ и ID компании:

![image](https://user-images.githubusercontent.com/91345275/198650619-cc0a590a-28ec-4d41-9496-35b16b2619e3.png)
![image](https://user-images.githubusercontent.com/91345275/198650678-0ee56d2c-2485-4195-acdc-0faff9c966fc.png)

ID терминала:

![image](https://user-images.githubusercontent.com/91345275/198652040-8746e362-139b-46f4-a97b-135f63b42b4c.png)
![image](https://user-images.githubusercontent.com/91345275/198652082-96cdf363-d15a-4c09-bfb0-c098f67dd28f.png)

Создание терминала
```php
<?php
include "sdk/RestClient.php";
include "sdk/CREATE_TERMINAL.php";

$name = "Название магазина";
$description = "Описание магазина";
$merchant_id = "c24360cfac0a0c40c518405f6bc68cb0"; // ID компании
$api_key = "1526fec01b5d11f4df4f2160627ce351"; // API ключ

$restClient = new RestClient($merchant_id, $api_key);

$create_terminal = new CREATE_TERMINAL($name);
$create_terminal->description = $description;
$create_terminal->type = "dynamical"; // Тип терминала(statical или dynamical)

/**
**@var $terminalInfo TerminalInfo
**/
$terminalInfo = $restClient->CreateTerminal($create_terminal);

echo "ID Терминала: " . $terminalInfo->id; 
?>
```
Создание платежа
```php
<?php
include "sdk/RestClient.php";
include "sdk/CREATE_PAYMENT.php";
include "sdk/common/ORDER.php";
include "sdk/common/ITEM.php";
include "sdk/common/SETTINGS.php";

$merchant_id = "c24360cfac0a0c40c518405f6bc68cb0"; // ID компании
$api_key = "1526fec01b5d11f4df4f2160627ce351"; // API ключ
$terminalId = "9ad01d262144a13cda1e90593bf64479"; //ID терминала, в котором будет создаваться платеж

$restClient = new RestClient($merchant_id, $api_key);

$amount = 1000; // Общая сумма заказа

$order = new ORDER($amount);
$order->description = "Иванов Иван Иванович"; // Комментарий к заказу(Необязательно)
$order->id = 137; // ID заказа на вашем сайте

$settings = new SETTINGS($terminalId);
$settings->success_url = "https://example.com/success.html"; // Ссылка на которую будет переправлен пользователь в случае успешной оплаты
$settings->fail_url = "https://example.com/fail.html"; // Ссылка на которую будет переправлен пользователь в случае неудачной оплаты

$item1 = new ITEM("Кефир", 200, 2, 400); // Предмет заказа "Кефир", стоиомость за 1 предмет - 200, кол-во - 2, общая стоимость - 400
$item2 = new ITEM("Суп", 1000, 1, 600);
$item2->discount = "40%"; // Скидка 40% на суп

$receipt = [$item1, $item2]; // Массив с заказанными предметами

$create_payment = new CREATE_PAYMENT($order, $settings, $receipt);

/**
**@var $paymentInfo PaymentInfo
**/
$paymentInfo = $restClient->CreatePayment($create_payment);

echo "Оплатить заказ: ".$paymentInfo->payment_url;
?>
```
Оформление возврата средств
```php
<?php
include "sdk/RestClient.php";
include "sdk/CREATE_REFUND.php";
include "sdk/common/REFUND_INFO.php";

$merchant_id = "c24360cfac0a0c40c518405f6bc68cb0"; // ID компании
$api_key = "1526fec01b5d11f4df4f2160627ce351"; // API ключ
$paymentId = "126d4c806ef04b10f822541f1a5b41d9"; // ID платежа

$restClient = new RestClient($merchant_id, $api_key);

$amountRefund = 40; // Сумма возврата
$reason = "В супе нашли муху"; // Причина возврата

$refund = new REFUND_INFO($amountRefund, $reason);
$create_refund = new CREATE_REFUND($paymentId, $refund);

/**
**@var RefundInfo $refundInfo
**/
$refundInfo = $restClient->CreateRefund($create_refund);

echo "Статус возврата: ".$refundInfo->status;
?>
```
Пример WebHook
```php
<?php
$api_key = "1526fec01b5d11f4df4f2160627ce351"; // API ключ
//Данные о заказе
$myOrderAmount = 1000;
$myCurrency = "RUB";

$postData = file_get_contents("php://input");
$notification = json_decode($postData, true);

$signature = $notification["signature"]; // Сигнатура из тела запроса
$paymentId = $notification["id"]; // ID платежа
$status = $notification["status"]; // Статус платежа(successful|error)
$notification_type = $notification["notification_type"]; // Тип уведомления(pay|refund)
$orderId = strstr($notification["order"]["id"], "-", true);; // ID заказа, который мы передали при создании платежа
$orderAmount = $notification["order"]["amount"]; // Сумма заказа
$currency = $notification["order"]["currency"]; // Валюта заказа

if($signature != getSignature($paymentId, $status, $api_key)) { // Обязательно проверяйте сигнатуру
    die("Wrong signature");
}

if($currency != $myCurrency or $orderAmount != $myOrderAmount) { // Всегда проверяйте сумму оплаты и валюту
    die("Неверная сумма оплаты или валюта");
}

switch ($notification_type) {
    case "pay":
        switch($status) {
            case "successful":
                // TODO: Действие при успешной оплате заказа 
                break;
            case "error":
                // TODO: Действие при неудачной оплате заказа 
                break;
        }       
        break;
    case "refund":
        $amountRefund = $notification["amount"]; // Сумма возвращенных средств
        //TODO: Действие при возврате товара
        break;
}

function getSignature($paymentId, $status, $api_key) {
    return md5($paymentId.$status.$api_key);
}
?>
```