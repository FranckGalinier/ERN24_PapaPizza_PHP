<?php

namespace App\Controller;

use Core\View\View;
use Core\Controller\Controller;

class PizzaController extends Controller

  /**
   * méthode qui renvoie la vue de la page d'accueil
   * @return void
   */
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

  /**
   * méthode qui renvoie la vue de la carte des pizzas
   * @return void
   */
  public function getPizzas():void
  {
    
  }
 }
