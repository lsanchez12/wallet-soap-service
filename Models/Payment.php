<?php
namespace Models;

class Payment
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $id_transaction;

    /**
     * @var string
     */
    public $status;

    /**
     * @var datetime
     */
    public $created_at;

    /**
     * @var datetime
     */
    public $updated_at;
}