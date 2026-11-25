<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

class IndexController extends Controller
{
    /**
     * Depois do login a Senha Única volta para "/": quem aprova cai direto na lista de pendentes
     */
    public function index()
    {
        if (Gate::allows('autorizador')) {
            return redirect('/aprovacoes');
        }
        if (Gate::allows('admin')) {
            return redirect('/aprovacoes/todas');
        }

        return view('index');
    }
}
