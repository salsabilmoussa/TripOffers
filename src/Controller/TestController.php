<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function index()
    {
        dd("ca fonctionne");
       
    }


    public function test(Request $request, $age)
    {
        // $age=$request->query->get('age',0);
       
        return new Response("age= $age ans");
       
    }
}
