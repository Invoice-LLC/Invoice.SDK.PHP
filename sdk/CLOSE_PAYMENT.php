<?php


class CLOSE_PAYMENT
{
    /**
     * @var string
     * Payment ID
     */
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}