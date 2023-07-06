<?php
namespace Models;

class User
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $document;

    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $api_token;

    /**
     * @var string
     */
    public $phone_number;

    /**
     * @var string
     */
    public $password;
}