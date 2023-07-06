<?php
namespace Models;

class Wallet
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
    public $wallet_uuid;

    /**
     * @var float
     */
    public $balance;
}