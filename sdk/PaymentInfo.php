<?php


class PaymentInfo
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var ORDER
     */
    public $order;
    /**
     * @var string{"created", "init", "process", "successful", "error", "closed"}
     */
    public $status;
    /**
     * @var string
     */
    public $status_description;
    /**
     * @var string
     */
    public $ip;
    /**
     * @var string{"card", "qiwi", "phone","wm"}
     */
    public $payment_method;
    /**
     * @var string
     */
    public $create_date;
    /**
     * @var string
     */
    public $update_date;
    /**
     * @var string
     */
    public $expire_date;
    /**
     * @var array
     */
    public $custom_parameters;
    /**
     * @var string
     */
    public $payment_url;
    /**
     * @var string
     */
    public $error;
    /**
     * @var string
     */
    public $description;

}