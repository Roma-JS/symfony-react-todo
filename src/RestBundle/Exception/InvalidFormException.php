<?php

namespace RestBundle\Exception;


class InvalidFormException extends \RuntimeException
{
    protected $form;

    public function __construct($message, $form = NULL)
    {
        parent::__construct($message);
        $this->form = $form;
    }

    /**
     * @return array|null
     */
    public function getForm()
    {
        return $this->form;
    }
}