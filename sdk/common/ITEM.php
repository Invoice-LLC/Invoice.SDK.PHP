<?php


class ITEM
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var double
     */
    public $price;
    /**
     * @var double
     */
    public $resultPrice;
    /**
     * @var string
     */
    public $discount;
    /**
     * @var float
     */
    public $quantity;

    /**
     * ITEM constructor.
     * @param $name string
     * @param $price string
     * @param $quantity string
     * @param $resultPrice string
     */
    public function __construct($name, $price, $quantity, $resultPrice)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->resultPrice = $resultPrice;
    }
}