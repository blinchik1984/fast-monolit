<?php


namespace Monolit\Controllers;


use AdServer\Engine\Components\Engine;

class MyController
{
    public function index()
    {
        return Engine::getContainer()->get('targetClientApi')->request(new \GuzzleHttp\Psr7\Request('GET', '/'))->getBody()->getContents();
    }
}