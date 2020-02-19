<?php
require_once "PseudoProcessing.php";

$action = @$_GET['action'];

if(!isset($action)) {
    die();
}

$processing = new PseudoProcessing();

$processing->init("demo","1526fec01b5d11f4df4f2160627ce351","1:1");

$item1 = new ITEM("Суп",10,2,20);

$item2 = new ITEM("Кефир", 1000, 1, 990);
$item2->discount = 10;

$items = [
    $item1,
    $item2
];

switch ($action) {
    case "pay":
        $pay = $processing->onPay($items, 2000);

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

