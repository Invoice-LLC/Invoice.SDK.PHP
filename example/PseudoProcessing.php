<?php
require_once "../sdk/RestClient.php";
require_once "../sdk/GET_TERMINAL.php";
require_once "../sdk/CREATE_TERMINAL.php";
require_once "../sdk/CREATE_PAYMENT.php";
require_once "../sdk/CREATE_REFUND.php";
require_once "../sdk/CLOSE_PAYMENT.php";
require_once "../sdk/common/ITEM.php";
require_once "../sdk/common/SETTINGS.php";

class PseudoProcessing
{
    const shopName = "Название магазина";
    const shopDescription = "Описание магазина";
    const terminalType = "dynamical";
    const defaultPrice = 0;

    private $restClient;
    private $paymentInfo;
    private $terminalInfo;
    private $refundInfo;

    public function init($login, $apiKey, $alias)
    {
        if($this->terminalInfo == null)
        {
            $this->restClient = new RestClient($login, $apiKey);
            $get_terminal = new GET_TERMINAL($alias);

            $this->terminalInfo = $this->restClient->GetTerminal($get_terminal);
            if($this->terminalInfo or $this->terminalInfo->id == null)
            {
                $create_terminal = new CREATE_TERMINAL();

                $create_terminal->alias = $alias;
                $create_terminal->name = self::shopName;
                $create_terminal->description = self::shopDescription;
                $create_terminal->type = self::terminalType;
                $create_terminal->defaultPrice = self::defaultPrice;

                $this->restClient->CreateTerminal($create_terminal);
            }

        }
    }

    public function onPay(array $items)
    {
        $create_payment = new CREATE_PAYMENT();

        $create_payment->receipt = $items;

        $settings = new SETTINGS();
        $settings->terminal_id = $this->terminalInfo->id;

        $create_payment->settings = $settings;

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
        $create_refund = new CREATE_REFUND();
        $create_refund->receipt = $items;
        $create_refund->id = $orderID;

        $refund_info = new REFUND_INFO();
        $refund_info->reason = $reason;
        $refund_info->amount = $amount;

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
