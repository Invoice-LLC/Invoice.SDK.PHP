<?php


class PAYMENT_METHOD
{
    /**
     * @var string
     */
    public $terminal_id;
    /**
     * @var string{"card", "qiwi", "phone","wm"} $type
     */
    public $type;
    /**
     * @var string
     */
    public $account;
    /**
     * @var double
     */
    public $funds;
    /**
     * @var double
     */
    public $bonuses;

}