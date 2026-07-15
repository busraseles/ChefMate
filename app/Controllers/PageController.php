<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

final class PageController extends Controller
{
    public function home(Request $request): void
    {
        $this->view('pages.home');
    }

    public function fridge(Request $request): void
    {
        $this->view('pages.fridge');
    }

    public function about(Request $request): void
    {
        $this->view('pages.about');
    }
}
