<?php


class SETTINGS
{
    /**
     * @var string
     */
    public $terminal_id;
    /**
     * @var string{"card", "qiwi", "phone","wm"}
     * Payment method type
     * card | qiwi | wm | phone
     */
    public $payment_method;
    /**
     * @var string
     */
    public $success_url;
    /**
     * @var string
     */
    public $fail_url;

}