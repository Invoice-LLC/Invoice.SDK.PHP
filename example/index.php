<?php
require_once "PseudoProcessing.php";

$action = @$_GET["action"];
if($action == null or empty($action)) {
    die("Не выбрано действие");
}

$processing = new PseudoProcessing("demo","1526fec01b5d11f4df4f2160627ce351");

$processing->setTerminal("9ad01d262144a13cda1e90593bf64479");

$processing->shopDescription = "Магазин бытовой техники";
$processing->description = "Иванов Иван Иванович";
$processing->failUrl = "https://google.com";
$processing->successUrl = "https://google.com";

$processing->customParameters = [
    "phone" => "79992223343"
];

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
            echo "Платеж оформлен: https://pay.invoice.su/P".$processing->getPayment()->id;
        } else {
            echo "Ошибка платежа ".$processing->getPayment()->error;
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
            echo "Ошибка возврата ".$processing->getRefund()->error;
        }
        break;

    default:
        echo $processing->getTerminal()->id;
        echo "<br>";
        echo $processing->getTerminal()->link;
        break;
}


?>

