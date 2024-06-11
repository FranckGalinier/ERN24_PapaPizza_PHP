<?php

namespace App;


use App\Repository\OrderRowRepository;
use App\Repository\SizeRepository;
use App\Repository\UnitRepository;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\PizzaRepository;
use App\Repository\PriceRepository;
use App\Repository\IngredientRepository;
use App\Repository\PizzaIngredientRepository;

class AppRepoManager
{
  //on récupére le trait RepositoryManagerTrait
  use \Core\Repository\RepositoryManagerTrait;

  //on déclare une propriété privée qui contiendra l'instance du repository

  private IngredientRepository $ingredientRepository;
  private OrderRepository $orderRepository;
  private OrderRowRepository $orderRowRepository;
  private PizzaIngredientRepository $pizzaIngredientRepository;
  private PizzaRepository $pizzaRepository;
  private PriceRepository $priceRepository;
  private SizeRepository $sizeRepository;
  private UnitRepository $unitRepository;
  private UserRepository $userRepository;

  //on crée les getter de chaque repository pour pouvoir les utiliser n'importe où dans notre application
  public function getIngredientRepository(): IngredientRepository
  {
    return $this->ingredientRepository;
  }
  public function getOrderRepository(): OrderRepository
  {
    return $this->orderRepository;
  }
  public function getOrderRowRepository(): OrderRowRepository
  {
    return $this->orderRowRepository;
  }
  public function getPizzaIngredientRepository(): PizzaIngredientRepository
  {
    return $this->pizzaIngredientRepository;
  }
  public function getPizzaRepository(): PizzaRepository
  {
    return $this->pizzaRepository;
  }
  public function getPriceRepository(): PriceRepository
  {
    return $this->priceRepository;
  }
  public function getSizeRepository(): SizeRepository
  {
    return $this->sizeRepository;
  }

  public function getUnitRepository(): UnitRepository
  {
    return $this->unitRepository;
  }
  public function getUserRepository(): UserRepository
  {
    return $this->userRepository;
  }








  //on déclare un construc qui va instancier les repositories
  protected function __construct()
  {
    //configuration de la base de donnée
    $config = App::getApp();
    //on instancie le repository
    $this->unitRepository = new UnitRepository($config);
    $this->sizeRepository = new SizeRepository($config);
    $this->priceRepository = new PriceRepository($config);
    $this->pizzaRepository = new PizzaRepository($config);
    $this->pizzaIngredientRepository = new PizzaIngredientRepository($config);  
    $this->orderRowRepository = new OrderRowRepository($config);
    $this->orderRepository = new OrderRepository($config);
    $this->ingredientRepository = new IngredientRepository($config);
    $this->userRepository = new UserRepository($config);
  }
}
