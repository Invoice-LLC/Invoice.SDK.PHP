<?php
require_once "../sdk/RestClient.php";
require_once "../sdk/GET_TERMINAL.php";
require_once "../sdk/CREATE_TERMINAL.php";
require_once "../sdk/CREATE_PAYMENT.php";
require_once "../sdk/CREATE_REFUND.php";
require_once "../sdk/CLOSE_PAYMENT.php";
require_once "../sdk/common/ITEM.php";
require_once "../sdk/common/SETTINGS.php";
require_once "../sdk/common/ORDER.php";
require_once "../sdk/common/REFUND_INFO.php";
require_once "../sdk/GET_PAYMENT.php";

class PseudoProcessing
{
    public $shopName = "Название магазина";
    public $shopDescription = "Описание магазина";
    const terminalType = "dynamical";
    const defaultPrice = 0;

    /**
     * @var RestClient
     */
    private $restClient;
    /**
     * @var PaymentInfo
     */
    private $paymentInfo;
    /**
     * @var TerminalInfo
     */
    private $terminalInfo;
    /**
     * @var RefundInfo
     */
    private $refundInfo;

    /**
     * Optional fields
     */
    public $description;
    public $customParameters;
    public $failUrl;
    public $successUrl;
    public $currency;
    public $email;
    public $phone;

    public function __construct($login, $apiKey)
    {
        $this->restClient = new RestClient($login, $apiKey);
    }

    public function setTerminal($id) {
        $get_terminal = new GET_TERMINAL();
        $get_terminal->id = $id;

        $this->terminalInfo = $this->restClient->GetTerminal($get_terminal);
    }

    public function createTerminal($name) {
        $create_terminal = new CREATE_TERMINAL($name);

        $create_terminal->description = $this->shopDescription;
        $create_terminal->defaultPrice = self::defaultPrice;

        $this->terminalInfo = $this->restClient->CreateTerminal($create_terminal);
    }

    public function onPay(array $items, $amount)
    {
        $settings = new SETTINGS($this->terminalInfo->id);

        if($this->failUrl != null and !empty($this->failUrl)) {
            $settings->fail_url = $this->failUrl;
        }

        if($this->successUrl != null and !empty($this->successUrl)) {
            $settings->success_url = $this->successUrl;
        }

        $order = new ORDER($amount);
        $order->currency = $this->currency;

        if($this->description != null and !empty($this->description)) {
            $order->description = $this->description;
        }

        $create_payment = new CREATE_PAYMENT($order, $settings,$items);

        if($this->customParameters != null and !empty($this->customParameters)) {
            $create_payment->custom_parameters = $this->customParameters;
        }

        $create_payment->mail = $this->email;
        $create_payment->phone = $this->phone;

        $this->paymentInfo = $this->restClient->CreatePayment($create_payment);

        if($this->paymentInfo == null or $this->paymentInfo->id == null)
        {
            return false;
        }else
        {
            return true;
        }
    }

    public function onCancel($orderId)
    {
        $close_payment = new CLOSE_PAYMENT($orderId);

        $this->restClient->ClosePayment($close_payment);
    }

    public function onRefund($orderID, array $items, string $reason, int $amount)
    {
        $refund_info = new REFUND_INFO($amount, $reason);
        $refund_info->reason = $reason;
        $refund_info->amount = $amount;

        $create_refund = new CREATE_REFUND($orderID,$refund_info);
        $create_refund->receipt = $items;
        $create_refund->refund = $refund_info;

        $this->refundInfo = $this->restClient->CreateRefund($create_refund);

        if($this->refundInfo->error == null)
        {
            return true;
        }else {
            return false;
        }
    }

    public function getStatus(string $orderId)
    {
        $get_payment = new GET_PAYMENT($orderId);
        $this->paymentInfo = $this->restClient->GetPayment($get_payment);

        return $this->paymentInfo->status;
    }

    public function getPayment()
    {
        return $this->paymentInfo;
    }

    public function getTerminal()
    {
        return $this->terminalInfo;
    }

    public function getRefund()
    {
        return $this->refundInfo;
    }
}