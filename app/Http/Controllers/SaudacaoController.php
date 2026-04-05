<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaudacaoController extends Controller
{
  public function index(){
    $mensagem = "Bem vindo ao sistema doe vida!";
    return view('hello_world', ['texto' => $mensagem]);
    }  //
}
