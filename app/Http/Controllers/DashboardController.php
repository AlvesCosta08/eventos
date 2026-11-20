<?php

namespace App\Http\Controllers;

use App\Domain\Dashboard\Actions\GetDashboardDataAction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(GetDashboardDataAction $action): View
    {
        // Delegamos toda a complexidade de agregação de dados para a Action
        $data = $action->execute(auth()->user());

        return view('dashboard', $data);
    }
}