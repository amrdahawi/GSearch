<?php

namespace GSearchBundle\Validator;

use Doctrine\Common\Collections\ArrayCollection;

Abstract class Validator
{
    /**
     * @var ArrayCollection
     */
    private $errors;

    /**
     * Validator constructor.
     */
    public function __construct()
    {
        $this->errors = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errors->isEmpty();
    }

    /**
     * @return ArrayCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $error
     */
    public function addError($error)
    {
        $this->errors->add($error);
    }

}
