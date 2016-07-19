<?php

namespace RestBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use RestBundle\Exception\BadRequestDataException;

class BaseController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Makes response from given exception.
     *
     * @param \Exception $exception
     * @throws BadRequestDataException
     */
    protected function throwSupportedException(\Exception $exception)
    {
        throw new BadRequestDataException($exception->getMessage());
    }
}