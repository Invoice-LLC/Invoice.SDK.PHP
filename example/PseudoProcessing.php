<?php
require_once "../sdk/RestClient.php";
require_once "../sdk/GET_TERMINAL.php";
require_once "../sdk/CREATE_TERMINAL.php";
require_once "../sdk/CREATE_PAYMENT.php";
require_once "../sdk/CREATE_REFUND.php";
require_once "../sdk/CLOSE_PAYMENT.php";
require_once "../sdk/GET_PAYMENT_BY_ORDER.php";
require_once "../sdk/common/ITEM.php";
require_once "../sdk/common/SETTINGS.php";
require_once "../sdk/common/ORDER.php";
require_once "../sdk/common/REFUND_INFO.php";
require_once "../sdk/GET_PAYMENT.php";

class PseudoProcessing
{
    /**
     * @var RestClient
     */
    private $restClient;
    /**
     * @var PaymentInfo
     */
    public $paymentInfo;
    /**
     * @var TerminalInfo
     */
    public $terminalInfo;
    /**
     * @var RefundInfo
     */
    public $refundInfo;

    /**
     * Optional fields
     */

    private $failUrl;
    private $successUrl;
    private $currency;

    public function __construct($login, $apiKey, $shop, $successUrl = null, $failUrl = null, $currency = null)
    {
        $this->failUrl = $failUrl;
        $this->successUrl = $successUrl;
        $this->currency = $currency;

        $this->restClient = new RestClient($login, $apiKey);

        // Пытаемся найти терминал в базе с alias==shop_id.
        // shop_id - любой уникальный неменяющейся индетефекатор в рамках этого юзера
        $terminal = new GET_TERMINAL();
        $terminal->alias = $shop["id"];
        $this->terminalInfo = $this->restClient->GetTerminal($terminal);

        // Если нашли, то выходим
        if ($this->terminalInfo->id)
            return;

        // Иначе создаем новый терминал, указывая alias
        $terminal = new CREATE_TERMINAL($shop["name"]);
        $terminal->description = "Онлайн оплата";
        $terminal->alias = $shop["id"];
        $this->terminalInfo = $this->restClient->CreateTerminal($terminal);
    }

    public function onPay($orderId, array $items, $customer)
    {
        $amount = 0;
        $receipt = [];
        foreach ($items as $item){ // Формируем список товаров
            $receipt[] = new ITEM(
                $item["name"],
                $item["price"],
                $item["count"],
                $item["fullPrice"]
            );
            $amount += $item["fullPrice"]; // Считаем общую стоимость
        }

        $order = new ORDER($amount);
        $order->currency = $this->currency;
        $order->id = $orderId;
        $order->description = "Заказ №" . strval($orderId);

        $settings = new SETTINGS($this->terminalInfo->id);
        $settings->fail_url = $this->failUrl;
        $settings->success_url = $this->successUrl;

        $create_payment = new CREATE_PAYMENT($order, $settings, $receipt);
        $create_payment->mail  = $customer["email"]; // Для отправки чека,
        $create_payment->phone = $customer["phone"]; // если фискализацию выполняет Invoice
        $create_payment->custom_parameters = $customer; // Сюда можно подставить что угодно, можно не использовать

        $this->paymentInfo = $this->restClient->CreatePayment($create_payment);
        return $this->paymentInfo != null and $this->paymentInfo->id != null;
    }

    public function onCancel($orderId)
    {
        // Так как мы наш псевдо процессинг дает нам ID заказа в своей системе,
        // то мы должны его привести к ID в системе Invoice.
        // Для этого сначала отправим запрос на получения платежа.

        $order = $this->restClient->GetPaymentByOrder(
            new GET_PAYMENT_BY_ORDER($orderId)
        );

        if ($order == null or $order->error != null)
            return false;

        // Если заказ уже закрыт, то вернем успешный результат
        if ($order->status == "closed")
            return true;

        // Если оплата уже прошла, тогда вернем неудачный результат
        if ($order->status == "successful")
            return false;

        // Закроем платеж
        $result = $this->restClient->ClosePayment(
            new CLOSE_PAYMENT($order->id)
        );

        if ($result == null or $result->error != null)
            return false;

        return $result->status == "closed";
    }

    public function onRefund($orderId, array $items, string $reason)
    {
        // Так как мы наш псевдо процессинг дает нам ID заказа в своей системе,
        // то мы должны его привести к ID в системе Invoice.
        // Для этого сначала отправим запрос на получения платежа.

        $order = $this->restClient->GetPaymentByOrder(
            new GET_PAYMENT_BY_ORDER($orderId)
        );

        if ($order == null or $order->error != null)
            return false;

        $amount = 0;
        $receipt = [];
        foreach ($items as $item){ // Формируем список товаров
            $receipt[] = new ITEM(
                $item["name"],
                $item["price"],
                $item["count"],
                $item["fullPrice"]
            );
            $amount += $item["fullPrice"]; // Считаем общую стоимость
        }

        $refund_info = new REFUND_INFO($amount, $reason);
        $refund_info->currency = $this->currency;

        $create_refund = new CREATE_REFUND($order->id, $refund_info);
        $create_refund->receipt = $receipt;

        $this->refundInfo = $this->restClient->CreateRefund($create_refund);
        return $this->refundInfo != null and $this->refundInfo->id != null;
    }

    public function getStatus(string $orderId)
    {
        $get_payment = new GET_PAYMENT_BY_ORDER($orderId);
        $this->paymentInfo = $this->restClient->GetPaymentByOrder($get_payment);

        return $this->paymentInfo != null;
    }
}