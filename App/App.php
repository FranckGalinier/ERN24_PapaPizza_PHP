<?php

namespace App;

use MiladRahimi\PhpRouter\Router;
use App\Controller\AuthController;
use App\Controller\UserController;
use App\Controller\OrderController;
use App\Controller\PizzaController;
use Core\Database\DatabaseConfigInterface;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use MiladRahimi\PhpRouter\Exceptions\InvalidCallableException;

// point d'entrée de l'application
class App implements DatabaseConfigInterface
  {
    //on défni les constantes de la base de donnée
    private const DB_HOST = 'database';
    private const DB_NAME = 'database_lamp';
    private const DB_USER = 'admin';
    private const DB_PASS = 'admin';

    private static ?self $instance = null;

    //on crée une méthode publique qui sera démarré au début de l'application dans le fichier index.php
    public static function getApp():self
    {
      //si l'instance est null
      if(is_null(self::$instance))
      {
        //on crée une nouvelle instance de la classe
        self::$instance = new self();
      }
      //on retourne l'instance
      return self::$instance;
    }
    //on crée un propriété privée pour stocker le router
    private Router $router;

    //méthode qui permet de récupérer les infos du routet
    public function getRouter(){
      return $this->router;
    }

    private function __construct()
    {
      //on instancie le router
      $this->router = Router::create();
    }

    //on a 3 méthode à définir
    // 1. méthode start pour activer le router
    public function start():void
    {
      //on ouvre l'accés à la session
      session_start();
      //enregistre les routes
      $this->registerRoutes();
      //démarre le router
      $this->startRouter();

    }

    //2. méthode qui va enregistrer les routes
    private function registerRoutes():void
    {
      //on va définir des patterns de routes
      $this->router->pattern('id', '[0-9]\d*'); //autorise que l'id soit un nombre de 0 à 9 


      // Partie AUTH :
      //connexion
      //get va renvoyer une vue
      $this->router->get('/connexion', [AuthController::class, 'loginForm']);
      $this->router->get('/inscription', [AuthController::class, 'registerForm']);
      //post va 
      //réceptionner des données
      $this->router->post('/login', [AuthController::class, 'login']);
      $this->router->post('/register', [AuthController::class, 'register']);

      //Partie PIZZA :

      $this->router->get('/', [PizzaController::class, 'home']);
      $this->router->get('/pizzas', [PizzaController::class, 'getPizzas']);
      $this->router->get('/pizza/{id}', [PizzaController::class, 'getPizzaById']);

      //PArtie panier
      $this->router->post('/add/order', [OrderController::class, 'addOrder']);
      $this->router->get('/order/{id}', [UserController::class, 'order']);
      $this->router->post('/order/update/{id}', [OrderController::class, 'updateOrder']);
      $this->router->post('/order-row/delete/{id}', [OrderController::class, 'deleteOrderRow']);

      //Partie user
      $this->router->get('/user/create-pizza/{id}', [UserController::class, 'createPizza']);
      $this->router->get('/user/list-custom-pizza/{id}', [UserController::class, 'listCustomPizza']);
      $this->router->get('/user/pizza/delete/{id}', [UserController::class, 'deleteCustomPizza']);
      $this->router->post('/add-custom-pizza-form', [PizzaController::class, 'addCustomPizzaForm']);
      

    }

    //3. méthode qui va démarrer le router
    private function startRouter():void
    {
      try{
        $this->router->dispatch();
      } catch(RouteNotFoundException $e)
      
      {
        echo $e;
      }catch(InvalidCallableException $e)
      {
        echo $e;
      }
    }








    //chaque fonction retoune la valeur de la constante
    public function getHost():string
    {
      return self::DB_HOST;
    }
    public function getName():string
    {
      return self::DB_NAME;
    }
    public function getUser():string
    {
      return self::DB_USER;
    }
    public function getPass():string
    {
      return self::DB_PASS;
    }

  }