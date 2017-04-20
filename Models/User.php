<?php

namespace Appointment\Models;

use Exception;

class User
{
    private $name;
    private $email;

    /**
     * User constructor.
     * @param $name
     * @param $email
     * @throws Exception
     */
    public function __construct($name, $email)
    {
        if(!$this->validate_name($name) || !$this->validate_email($email)){
            throw new Exception('Invalid name or email');
        }

        $this->name = $name;
        $this->email = $email;
    }

    /**
     * getter name
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getter email
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * setter name.
     * @param $name
     * @throws Exception
     */
    public function setName($name)
    {
        if(!$this->validate_name($name)){
            throw new Exception('Invalid name');
        }

        $this->name = $name;
    }

    /**
     * setter for email address.
     * @param $email
     */
    public function setEmail($email)
    {
        if(!$this->validate_email($email)){
            throw new Exception('Invalid email');
        }

        $this->email = $email;
    }

    /**
     * validate name accept only alphabhet, dash, period and maximum 50 characters.
     * @param $name
     * @return bool
     */
    private function validate_name($name)
    {
        if(!preg_match('/^[a-zA-Z0-9-.]+$/', $name)){
            return false;
        }

        if(strlen($name) > 50){
            return false;
        }

        return true;
    }


    /**
     * validate email accept only alphabhet, dash, period.
     * @param $email
     * @return bool
     */
    private function validate_email($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
            return false;
        }

        return true;
    }

}