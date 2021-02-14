<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class BackyardController extends Controller
{
    public function index1()
    {
        $pageAttribute = [
            "module" => "dashboard",
            "submodule" => "index1",
        ];
        $notifMessage = [
            'success' => "Selamat Datang 1",
        ];
        $options = compact([
            'pageAttribute',
            'notifMessage'
        ]);
        return view("backyards.index1", $options);
    }
    
    public function index2()
    {
        $pageAttribute = [
            "module" => "dashboard",
            "submodule" => "index1",
        ];
        $notifMessage = [
            'warning' => "Selamat Datang 2",
        ];
        $options = compact([
            'pageAttribute',
            'notifMessage',
        ]);
        return view("backyards.index2", $options);
    }
    
    public function index3()
    {
        $pageAttribute = [
            "module" => "dashboard",
            "submodule" => "index1",
        ];
        $notifMessage = [
            'info' => "Selamat Datang 3",
        ];
        $options = compact([
            'pageAttribute',
            'notifMessage',
        ]);
        return view("backyards.index3", $options);
    }
    
    public function data()
    {
        $pageAttribute = [
            "module" => "tables",
            "submodule" => "data",
        ];
        $options = compact("pageAttribute");
        return view("backyards.data", $options);
    }
    
    public function chart()
    {
        $pageAttribute = [
            "module" => "charts",
            "submodule" => "chart",
        ];
        $options = compact("pageAttribute");
        return view("backyards.chart", $options);
    }
}

