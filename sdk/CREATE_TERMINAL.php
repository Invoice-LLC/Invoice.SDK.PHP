<?php


class CREATE_TERMINAL
{
    /**
     * @var string
     */
    public $alias;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string{"statical", "dynamical"}
     */
    public $type;
    /**
     * @var double
     */
    public $defaultPrice;
    /**
     * @var boolean
     */
    public $canComments;

    /**
     * CREATE_TERMINAL constructor.
     * @param $name string
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
}