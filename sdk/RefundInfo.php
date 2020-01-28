<?php


class RefundInfo
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
     * @var REFUND_INFO
     */
    public $refund;
    /**
     * @var string{"created", "init", "process", "successful", "error"}
     */
    public $status;
    /**
     * @var string
     */
    public $payment_id;
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
     * @var array
     */
    public $custom_parameters;
    /**
     * @var string
     */
    public $error;
    /**
     * @var string
     */
    public $description;
}