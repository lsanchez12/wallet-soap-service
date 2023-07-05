<?php
namespace Models;

class Transaction
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $id_user;

    /**
    * @var string
    */
    public $transaction_uuid;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $token;

    /**
     * @var datetime
     */
    public $created_at;

    /**
     * @var datetime
     */
    public $updated_at;
}