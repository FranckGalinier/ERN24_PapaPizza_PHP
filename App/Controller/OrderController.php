<?php

namespace App\Controller;

use App\Model\Order;
use App\Model\Pizza;
use App\AppRepoManager;
use Core\Form\FormError;
use Core\Form\FormResult;
use Core\Session\Session;
use Core\Form\FormSuccess;
use Core\Controller\Controller;
use Laminas\Diactoros\ServerRequest;

class OrderController extends Controller
{
  /**
   * méthode qui permet de générer un numéro de commmande
   * @return string
   *
   */
  private function generateOrderNumber()
  {
    // je veux un numéro de commande du type : FACT2406_00001 par exemple
    $order_number = 1;
    $order = AppRepoManager::getRm()->getOrderRepository()->findLastOrder();
    $order_number = str_pad($order + 1, 5, '0', STR_PAD_LEFT);
    $year = date('Y');
    $month = date('m');
    $final = "FACT'{$year}{$month}_{$order_number}";
    return $final;
  }

  public function addOrder(ServerRequest $request)
  {
    $form_data = $request->getParsedBody();

    $form_result = new FormResult();

    // ON REDÉFINIT LES VARIABLES
    $order_number = $this->generateOrderNumber();
    $date_order = date('Y-m-d H:i:s');
    $status = Order::IN_CART;
    $user_id = $form_data['user_id'];
    $size_id = $form_data['size_id'];
    $has_order_in_card = AppRepoManager::getRm()->getOrderRepository()->findLastStatusByUser($user_id, Order::IN_CART);
    // var_dump($has_order_in_card);
    $pizza_id = $form_data['pizza_id'];
    $quantity = $form_data['quantity'];
    $price = $form_data['price'] * $quantity;
    // on vérifie que la quanatité est bien qupérieure ou égale à 0
    if ($quantity <= 0) {
      $form_result->addError(new FormError('La quantité ne peux pas être zéro'));
    } elseif ($quantity > 10) {
      $form_result->addError(new FormError('La quantité ne peux pas être supérieure à 10'));

      //on vérifie que l'utilisateur n'a pas déjà une commande en cours
    } elseif (!$has_order_in_card) {
      //on crée la commande
      //on reconstruit un tableau de données pour la commande
      $data_order = [
        'order_number' => $order_number,
        'date_order' => $date_order,
        'status' => $status,
        'user_id' => $user_id
      ];
      $order_id = AppRepoManager::getRm()->getOrderRepository()->createOrder($data_order);
      //si la commande est bien créée : si order n'est pas null
      if ($order_id) {
        //on peut insérer la ligne de commande
        //on reconstruit un tableau de données pour la ligne de commande
        $data_order_row = [
          'pizza_id' => $pizza_id,
          'quantity' => $quantity,
          'price' => $price,
          'order_id' => $order_id,
          'size_id' => $size_id
        ];
        $order_line = AppRepoManager::getRm()->getOrderRowRepository()->insertOrderRow($data_order_row);
        //si la ligne de commande est bien créée
        if ($order_line) {
          $form_result->addSuccess(new FormSuccess('Pizza ajoutée au panier'));
        } else {

          $form_result->addError(new FormError('Erreur lors de la création la commande'));
        }
      }
      //si la commande a déjà été créée
    } else {
      //si l'utilisateur a déjà une commande en cours
      //on récupère l'id de la commande
      $order_id = AppRepoManager::getRm()->getOrderRepository()->findOrderIdByStatus($user_id);

      if ($order_id) {
        //on peut insérer la ligne de commande
        //on reconstruit un tableau de données pour la ligne de commande
        $data_order_row = [
          'pizza_id' => $pizza_id,
          'quantity' => $quantity,
          'price' => $price,
          'order_id' => $order_id,
          'size_id' => $size_id
        ];
        $order_line = AppRepoManager::getRm()->getOrderRowRepository()->insertOrderRow($data_order_row);
        //si la ligne de commande est bien créée
        if ($order_line) {
          $form_result->addSuccess(new FormSuccess('Pizza ajoutée au panier'));
        } else {

          $form_result->addError(new FormError('Erreur lors de la récupération de l\'id la commande'));
        }
      }
    }
    //si on a des erreurs, on les mets en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page detail
      self::redirect('/pizza/' . $pizza_id);
    }

    //si on a des succès, on les mets en sessions
    if ($form_result->hasSuccess()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page detail
      self::redirect('/pizza/' . $pizza_id);
    }
  }

  /**
   * méthode static qui regarder si on a des lignes dans le panier (encours)
   * @return bool
   */

   public static function hasOrderInCart():bool
   {
    $user_id = Session::get(Session::USER)->id; //on récupère l'id de l'utilisateur
    $has_order_in_card = AppRepoManager::getRm()->getOrderRepository()->findLastStatusByUser($user_id, Order::IN_CART);

    return $has_order_in_card;
   }

   /**
    * méthode qui permet de modifier la quantité d'une ligne de commande
    * @param ServerRequest $request
    * @param int $id
    * @return void
    */
    public function updateOrder(ServerRequest $request, int $id):void
    {
      $form_data = $request->getParsedBody();
      $form_result = new FormResult();
      $order_row_id = $form_data['order_row_id'];
      $quantity = $form_data['quantity'];
     $pizza_id = $form_data['pizza_id'];
     $size_id = $form_data['size_id'];
     $user_id = Session::get(Session::USER)->id;

     //on vérifie que la quantité est bien supérieure ou égale à 0
      if($quantity <= 0)
      {
        $form_result->addError(new FormError('La quantité ne peux pas être zéro'));
        //on vérifie que la quantité n'est pas supérieure à 10
      }elseif($quantity > 10)
      {
        $form_result->addError(new FormError('La quantité ne peux pas être supérieure à 10'));
      }else{
        //on reconstruit un tableau de données pour mettre à jour la ligne de commande
        $data_order_line=[
          'id' => $order_row_id,
          'quantity' => $quantity,
          'pizza_id' => $pizza_id,
          'size_id' => $size_id
        ];
        // on appelle la méthode qui permer de modifier la ligne de commande
        $order_line = AppRepoManager::getRm()->getOrderRowRepository()->updateOrderRow($data_order_line);

        if($order_line)
        {
          $form_result->addSuccess(new FormSuccess('Quantité modifiée'));
        }else{
          $form_result->addError(new FormError('Erreur lors de la modification de la quantité'));
        }
        //si on a des erreurs, on les mets en sessions
        if ($form_result->hasErrors()) {
          Session::set(Session::FORM_RESULT, $form_result);
          //on redirige sur la page detail
          self::redirect('/order/' . $user_id);
        }

        //si on a des succès, on les mets en sessions
        if ($form_result->hasSuccess()) {
          Session::remove(Session::FORM_RESULT);
         Session::set(Session::FORM_SUCCESS, $form_result);
          //on redirige sur la page detail
          self::redirect('/order/' . $user_id);
        }

    }
  }
   /**
   * méthode qui permet de supprimer une ligne de commande
   * @param ServerRequest $request
   * @param int $id
   * @return void
   */
  public function deleteOrderRow(ServerRequest $request, int $id):void
  {
    $form_data = $request->getParsedBody();
    $form_result = new FormResult();
    $user_id = Session::get(Session::USER)->id;
    $order_row = AppRepoManager::getRm()->getOrderRowRepository()->deleteOrderRow($id);
    //si la suppression s'est bien passé, on regarde si la commande a encore des lignes
    if($order_row){
      $countOrder = AppRepoManager::getRm()->getOrderRowRepository()->countOrderRowByOrder($form_data['order_id']);
      $form_result->addSuccess(new FormSuccess('Pizza supprimé du panier'));
      if($countOrder <= 0){
        //si je n'ai plus de ligne de commande on supprime la commande
        AppRepoManager::getRm()->getOrderRepository()->deleteOrder($form_data['order_id']);
      }
    }else{
      $form_result->addError(new FormError('Erreur lors de la suppression de la pizza'));
    }

    //si on a des erreur on les met en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page panier
      self::redirect('/order/' . $user_id);
    }

    //si on a des success on les met en sessions
    if ($form_result->getSuccessMessage()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page panier
      self::redirect('/order/' . $user_id);
    }
  }
}