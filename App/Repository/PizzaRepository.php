<?php

namespace App\Repository;

use App\Model\Pizza;
use App\AppRepoManager;
use Core\Repository\Repository;

class PizzaRepository extends Repository
{
  public function getTableName(): string
  {
    return 'pizza';
  }
  
  /**
   * méthode qui permet de récupérer toutes les pizzas de l'admin
   * @return array
   */
  public function getAllPizzas(): array
  {
    //on déclare un tableau vide
    $array_result = [];
    //on crée la requête SQL
    $query = sprintf(
      'SELECT p.`id`, p.`name`, p.`image_path`
      FROM %1$s as p
      INNER JOIN %2$s as u ON p.`user_id` = u.`id`
      WHERE u. `is_admin` =1
      AND p. `is_active` = 1',
      $this->getTableName(),//correspond au %1$s
      AppRepoManager::getRm()->getUserRepository()->getTableName()//correspond au %2%s
    );
    //on exécute la requête
    $stmt = $this->pdo->query($query);

    //on vérifie que la requête est bien exécutée
    if(!$stmt) return $array_result;
    //on récupère les données que l'on stocke dans le tableau
    while($row_data =$stmt->fetch()){
      //a chaque tour de boucle on instancie un objet pizza
      $array_result[] = new Pizza($row_data);
    }
    //retourne le tableau
    return $array_result;
  }

  /**
   * méthode qui permet de récupérer une pizza par son id
   * @param int $pizza_id
   * @return ?Pizza
   */
  public function getPizzaById(int $pizza_id): ?Pizza
  {
   //on fiat lar requête SQL
    $query = sprintf(
      'SELECT * FROM %s WHERE `id` = :id',
      $this->getTableName()
    );
    //on prépare la requête
    $stmt = $this->pdo->prepare($query);
    //on vérifie que la requête est bien préparée
    if(!$stmt) return null;
    //on exécute la requête en passant les valeurs
    $stmt->execute(['id'=>$pizza_id]);
    //on récupère les données
    $result = $stmt->fetch();
    
    //si je n'ai pas de résuktat, je retourne null
    if(!$result) return null;
    //si j'ai un résultat on retourne un objet pizza
    $pizza = new Pizza($result);
    
    //on va hydrater les ingredients de la pizza
    $pizza->ingredients = AppRepoManager::getRm()->getPizzaIngredientRepository()->getIngredientsByPizzaId($pizza_id);
    //on va hydrater les prix de la pizza
    $pizza->prices = AppRepoManager::getRm()->getPriceRepository()->getPriceByPizzaId($pizza_id);
    //on retourne pizza
    return $pizza;
  
  }

  /**
   * méthode qui permet d'ajouter une nouvelle pizza
   * @param array $data
   * @return ?int
   */
  public function addPizza(array $data): ?int
  {
    //:on crée la requete sql
    $query = sprintf(
      'INSERT INTO `%s` (`name`, `image_path`, `user_id`, `is_active`) VALUES (:name, :image_path, :user_id, :is_active)',
      $this->getTableName());

      $stmt = $this->pdo->prepare($query);

      if(!$stmt)return null;

      //on exécute la requete en passant les paramètres
      $stmt->execute($data);

      return $this->pdo->lastInsertId();
    
  
  }

  /**
   * méthode qui permet de drcupérer les pizzades de l'utilisateur en cours
   * @param int $user_id
   * @return array
   */
    
  public function getPizzasByUserId(int $user_id): array
  {
    //on déclare un tableau vide
    $array_result = [];
    //on crée la requête SQL
    $query = sprintf(
      'SELECT * FROM `%s` WHERE `user_id` = :user_id AND `is_active` = 1',
      $this->getTableName()
    );
    //on prépare la requête
    $stmt = $this->pdo->prepare($query);
    //on vérifie que la requête est bien préparée
    if(!$stmt) return $array_result;
    //on exécute la requête en passant les valeurs
    $stmt->execute(['user_id'=>$user_id]);
    //on récupère les données
    while($row_data = $stmt->fetch()){
      //a chaque tour de boucle on instancie un objet pizza
      $pizza = new Pizza($row_data);
      //on va hydrater les ingredients de la pizza
      $pizza->ingredients = AppRepoManager::getRm()->getPizzaIngredientRepository()->getIngredientsByPizzaId($pizza->id);
      //on va hydrater les prix de la pizza
      $pizza->prices = AppRepoManager::getRm()->getPriceRepository()->getPriceByPizzaId($pizza->id);
      //on stocke la pizza dans le tableau
      $array_result[] = $pizza;
    }
    //retourne le tableau
    return $array_result;
  }

  /**
   * méthode qui va permttre de desactiver une pizza
   * @param int $pizza_id
   * @return bool
   */
  public function disablePizza(int $pizza_id): bool
  {
    //on crée la requête SQL
    $query= sprintf('UPDATE `%s` SET `is_active` = 0 WHERE `id` = :id',
    $this->getTableName());
    //on prépare la requête
    $stmt = $this->pdo->prepare($query);
    //on vérifie que la requête est bien préparée
    if(!$stmt) return false;
    //on exécute la requête en passant les valeurs
    $stmt->execute(['id'=>$pizza_id]);
    //on retourne true si la requête est bien exécutée
    return true;
  }

}