<?php
namespace App\Controllers;

use App\Utils\Session;

class AuthController
{
    public function login()
    {
        Session::start();
        echo "Login funciona";
    }
}
