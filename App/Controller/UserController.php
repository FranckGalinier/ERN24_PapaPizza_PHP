<?php

namespace App\Controller;

use App\App;
use Core\View\View;
use App\AppRepoManager;
use Core\Form\FormError;
use Core\Form\FormResult;
use Core\Session\Session;
use Core\Form\FormSuccess;
use Core\Controller\Controller;

class UserController extends Controller
{

  /**
   * méthode qui renvoie la vue du panier d'un utilisateur
   * @param int|string $id
   * @return void
   */
  public function order(int|string $id):void
  {
    $order= AppRepoManager::getRm()->getOrderRepository()->findOrderInProgressWithOrderRow($id);
    //on récupère le total de commande
    $total = $order ? AppRepoManager::getRm()->getOrderRowRepository()->findTotalPriceByOrder($order->id) : 0;

    $countRow = $order ? AppRepoManager::getRm()->getOrderRowRepository()->countOrderRow($order->id) : 0;
    
    //on récupère les quantités de pizzas pour chaque ligne de commande
    $view_data = [
      'order' => $order,
      'total' => $total,
      'count_row' => $countRow,
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS),
    ];
    
    $view = new View('user/order');
    $view->render($view_data);
  }

  /**
   * méthode pour afficher le formulaire de création de pizza custom
   * @param int|string $id
   * @return void
   */
   
  public function createPizza(int $id):void
  {

    $view_data=[
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS),
    ];
    $view = new View('user/create_pizza');
    $view->render($view_data);
  }

  /**
   * méthode pour afficher la liste des pizzas custom
   * @pram int $id
   * @return void
   */
  public function listCustomPizza(int $id):void
  {

    $pizza = AppRepoManager:: getRm()->getPizzaRepository()->getPizzasByUserId(Session::get(Session::USER)->id);
    $view_data =[
      'h1'=> 'Mes cowgirls favorites &#x1F495; &#x1F355;',
      'pizzas'=> $pizza,
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS),
    ];
    $view = new View('user/pizzas');

    $view->render($view_data);
  }

  /**
   * méthode pour supprimer une pizza custom
   * @param int $id
   * @return void
   */
  public function deleteCustomPizza(int $id):void
  {
    $form_result = new FormResult();
    $user_id = Session::get(Session::USER)->id; // on récupére l'id de l'utilisateur connecté
    //appel de la méthode qui désactive la pizza
    $deletepizza = AppRepoManager::getRm()->getPizzaRepository()->disablePizza($id);

    //on afficjer un message de succès ou d'erreur
    if($deletepizza){
      $form_result->addError(new FormError('La pizza a bien été supprimée'));
    }else{
      $form_result->addSuccess(new FormSuccess('La pizza n\'a pas pu être supprimée'));
    }
    //si on a des erreurs, on les mets en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page detail
      self::redirect('/user/list-custom-pizza/' . $user_id);
    }

    //si on a des succès, on les mets en sessions
    if ($form_result->hasSuccess()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page detail
      self::redirect('/user/list-custom-pizza/' . $user_id);
    }
  }

  /**
   * méthode qui retourne la liste des commandes utilisatuers
   * @param int $id
   * @return void
   */
  public function listOrder(int $id):void
  {

    $view_data = [
      'orders' => AppRepoManager::getRm()->getOrderRepository()->findOrderByUser($id),
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS),
    ];
    $view = new View('user/list_order');
    $view->render($view_data);
  }
}