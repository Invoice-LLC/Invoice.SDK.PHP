<?php


class CREATE_PAYMENT
{
    /**
     * @var ORDER
     */
    public $order;
    /**
     * @var SETTINGS
     */
    public $settings;
    /**
     * @var array
     */
    public $custom_parameters;
    /**
     * @var array(ITEM)
     */
    public $receipt;

    /**
     * CREATE_PAYMENT constructor.
     * @param $order ORDER
     * @param $settings SETTINGS
     * @param $receipt array
     */
    public function __construct($order, $settings, $receipt)
    {
        $this->settings = $settings;
        $this->order = $order;
        $this->receipt = $receipt;
    }
}