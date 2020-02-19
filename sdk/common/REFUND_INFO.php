<?php


class REFUND_INFO
{
    /**
     * @var double
     */
    public $amount;
    /**
     * @var string
     * Example: RUB
     */
    public $currency;
    /**
     * @var string
     */
    public $reason;

    /**
     * REFUND_INFO constructor.
     * @param $amount string
     * @param $reason string
     */
    public function __construct($amount, $reason)
    {
        $this->amount = $amount;
        $this->reason = $reason;
    }
}