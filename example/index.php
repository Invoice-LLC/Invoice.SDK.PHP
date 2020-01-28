<?php
require_once "PseudoProcessing.php";

$action = $_GET['action'];

$processing = new PseudoProcessing();

$processing->init("demo","1526fec01b5d11f4df4f2160627ce351","1:1");

$item1 = new ITEM();
$item1->name = "Суп";
$item1->price = 10;
$item1->discount = 0;
$item1->quantity = 2;
$item1->resultPrice = 20;

$item2 = new ITEM();
$item2->name = "Кефир";
$item2->price = 1000;
$item2->discount = 10;
$item2->quantity = 1;
$item2->resultPrice = 990;

$items = [
    $item1,
    $item2
];

switch ($action) {
    case "pay":
        $pay = $processing->onPay($items);

        if($pay)
        {
            echo "Платеж оформлен: ".$processing->getPayment()->id;
        } else {
            echo "Ошибка платежа";
        }
        break;
    case "cancel":
        if(empty($_GET['id']))
            break;

        $processing->onCancel($_GET['id']);
        echo "Платеж отменен";
        break;
    case "status":
        if(empty($_GET['id']))
            break;

        $status = $processing->getStatus($_GET['id']);
        echo $status;
        break;
    case "refund":
        if(empty($_GET['id']))
            break;

        $refund = $processing->onRefund($_GET['id'] ,$items, "Муха в супе",20);

        if($refund) {
            echo "Возврат оформлен";
        }else {
            echo "Ошибка возврата";
        }
        break;

    default:
        echo $processing->getTerminal()->id;
        echo "<br>";
        echo $processing->getTerminal()->link;
        break;
}


?>

