<?php

namespace App\Controllers;

class Migrate extends \CodeIgniter\Controller
{
    public function index()
    {
        $migrate = \Config\Services::migrations();

        try {
            $migrate->current();
        } catch (\Exception $e) {
            echo 'Migration executed';
        }
    }
}
