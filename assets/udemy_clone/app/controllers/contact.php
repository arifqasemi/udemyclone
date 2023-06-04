<?php

namespace App\Controllers;
use App\Core\Controller;


class Contact extends Controller
{
    public function index(){

        $this->view('front/contact');
        
    }
}