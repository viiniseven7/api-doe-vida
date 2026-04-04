<?php 

use Illuminate\Support\Facades\Route;


Route::get('/', function(){

    return view('minha-pagina');
});

Route::get('hello', function(){
    return view('minha-pagina');
});

?>