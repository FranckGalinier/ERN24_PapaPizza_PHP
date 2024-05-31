<?php

namespace App;

use App\Repository\UserRepository;

class AppRepoManager
{
  //on récupére le trait RepositoryManagerTrait
  use \Core\Repository\RepositoryManagerTrait;

  //on déclare une propriété privée qui contiendra l'instance du repository
  private UserRepository $userRepository;
  
  //on crée le getter
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
    $this->userRepository = new UserRepository($config);
  }
}