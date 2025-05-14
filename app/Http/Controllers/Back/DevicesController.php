<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;

class DevicesController extends Controller
{


    public function index()
    {

        return view('back.pages.devices.index');

    }



}
