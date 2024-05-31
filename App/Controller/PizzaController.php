<?php

namespace App\Controller;

use Core\View\View;
use Core\Controller\Controller;

class PizzaController extends Controller
 {
  public function home()
  {
    //prepation des données à envoyer à la vue
    $viewdata = [
      'title'=>'Accueil',
      'pizza_list'=>[
        'reine',
        '4 fromages',
        'calzone',
      ]
    ];
  
    $view = new View('home/home');
    $view->render($viewdata);
  }
 }
