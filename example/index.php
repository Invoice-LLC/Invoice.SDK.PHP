<?php
require_once "PseudoProcessing.php";

$action = @$_GET["action"] ?? "pay";
$orderId = @$_GET["action"] ?? "1235";

$shop = [
    "id" => "28b839d026189922", // id магазина в системе
    "name" => "Магазин игрушек" // Название магазина в системе
];

$customer = [
    "name" => "Иван",
    "phone" => "79991234567",
    "email" => "em@invoice.su"
];

$processing = new PseudoProcessing(
    "demo",
    "1526fec01b5d11f4df4f2160627ce351",
    $shop,
    "https://invoice.su/",
    "https://google.com/"
);

$items = [
    [
        "name" => "Машинка",
        "price" => 100,
        "count" => 2,
        "fullPrice" => 200
    ],
    [
        "name" => "Робот",
        "price" => 199,
        "count" => 1,
        "fullPrice" => 199
    ]
];

switch ($action) {
    case "pay":
        if($processing->onPay($orderId, $items, $customer))
            echo "Платеж оформлен: https://pay.invoice.su/P" . $processing->paymentInfo->id;
        else
            echo "Ошибка платежа " . $processing->paymentInfo->error;
        break;

    case "cancel":
        if ($processing->onCancel($orderId))
            echo "Платеж отменен";
        else
            echo "Ошибка отмены платежа";
        break;

    case "status":
        if ($processing->getStatus($orderId))
            echo json_encode($processing->paymentInfo);
        else
            echo "Ошибка получения статуса";
        break;

    case "refund":
        unset($items[0]);
        $refund = $processing->onRefund($orderId, $items, "Бракованный робот");

        if($refund) {
            echo "Возврат оформлен";
        }else {
            echo "Ошибка возврата " . $processing->refundInfo->error;
        }
        break;
}