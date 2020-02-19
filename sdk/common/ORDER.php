<?php


class ORDER
{
    /**
     * @var string
     */
    public $currency;
    /**
     * @var double
     */
    public $amount;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $id;

    /**
     * ORDER constructor
     * @param $amount
     */
    public function __construct($amount)
    {
        $this->amount = $amount;
    }
}