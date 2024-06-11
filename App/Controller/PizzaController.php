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
use Laminas\Diactoros\ServerRequest;

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
    //le controllerdoit récupèrer le tableau de pizzas pour le donner à la vue
    $pizza = AppRepoManager:: getRm()->getPizzaRepository()->getAllPizzas();
    $view_data =[
      'h1'=>'Notre carte des pizzas',
      'pizzas'=> $pizza
    ];
    $view = new View('home/pizzas');
    $view->render($view_data);
  }

/**
 * méthode qui renvoie la vue d'une pizza par son id
 * @param int $id
 * @return void
 */
  public function getPizzaById(int $id):void
  {
    $pizza = AppRepoManager::getRm()->getPizzaRepository()->getPizzaById($id);
    $view_data = [
      'pizza'=>$pizza,
      'form_result'=> Session::get(Session::FORM_RESULT),
      'form_success'=> Session::get(Session::FORM_SUCCESS),
    ];

    $view = new View('home/pizza_detail');
    $view->render($view_data);
  }

  /**
   * méthode qui permet d'ajouter une pizza custom
   * @param ServerRequest $request
   * @return void
   */
  public function addCustomPizzaForm(ServerRequest $request):void
  {
    $data_form = $request->getParsedBody();
    $form_result= new FormResult();
    $name = $data_form['name'] ?? '';
    $user_id = $data_form['user_id'] ?? '';
    $ingredients = $data_form['ingredients'] ?? [];
    $size_id = $data_form['size_id'] ?? '';
    $array_ingredients = count($ingredients);
    $image_path ='pizza-custom.png';
    

    //vérification des données
    if(empty($name) || empty($user_id) || empty($size_id) || empty($ingredients)){
      $form_result->addError(new FormError('Tous les champs sont obligatoires'));
    }elseif($array_ingredients < 2)
    {
      $form_result->addError(new FormError('Il faut au moins 2 ingrédients'));
    }elseif($array_ingredients > 10)
    {
      $form_result->addError(new FormError('Il faut pas plus de 10 ingrédients'));
    }else{
      //on définit un prix fixe par taille + ingrédients
      if($size_id ==1)
      {
        $price = 7 + ($array_ingredients * 1);

      }elseif($size_id ==2)
      {
        $price = 9 + ($array_ingredients * 1.2);

      }else{
        $price = 12 + ($array_ingredients * 1.4);
      }

      //on peut reconstruire un tableau de données pour insérer la pîzza
      $pizza_data=[
        'name'=>htmlspecialchars(trim($name)),
        'image_path'=>$image_path,
        'user_id'=>intval($user_id),
        'is_active'=>1
      ];
      $pizza_id = AppRepoManager::getRm()->getPizzaRepository()->addPizza($pizza_data);

      if(is_null($pizza_id))
      {
        $form_result->addError(new FormError('Erreur lors de la création de la pizza'));
      }
      //on va boucler sur les ingredients
      foreach($ingredients as $ingredient)
      {
        $pizza_ingredient_data = [
          'pizza_id'=>intval($pizza_id),
          'ingredient_id'=>intval($ingredient),
          'unit_id'=>5,
          'quantity'=>1
        ];
        //toujours dans le boucle on appelle la méthode pour ajouter les ingredients dans la table pizza_ingredient

        $pizza_ingredient = AppRepoManager::getRm()->getPizzaIngredientRepository()->insertPIzzaIngredient($pizza_ingredient_data);
        if(!$pizza_ingredient)
        {
          $form_result->addError(new FormError('Erreur lors de l\'ajout des ingrédients'));
        }
      }

      //on reconstruit un tableau de données pour oinsérer les prix
      $pizza_price_data = [
        'pizza_id'=>intval($pizza_id),
        'size_id'=>intval($size_id),
        'price'=>floatval($price)
      ];

      $pizza_price = AppRepoManager::getRm()->getPriceRepository()->addPrice($pizza_price_data);
    
      if(!$pizza_price)
      {
        $form_result->addError(new FormError('Erreur lors de l\'ajout du prix'));
      }

      //si tout est ok on envoie un message de succès
      $form_result ->addSuccess(new FormSuccess('Pizza ajoutée avec succès'));
    }
    //si on a des erreurs, on les mets en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page detail
      self::redirect('/user/create-pizza/' . $user_id);
    }

    //si on a des succès, on les mets en sessions
    if ($form_result->hasSuccess()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page detail
      self::redirect('/user/create-pizza/' . $user_id);//TODO: changer la redirection vers la pliste des pizza
    }
  }

 }
