<?php

namespace App\Repository;

use App\AppRepoManager;
use App\Model\Ingredient;
use Core\Repository\Repository;

class PizzaIngredientRepository extends Repository
{
  public function getTableName(): string
  {
    return 'pizza_ingredient';
  }

  /**
   * méthode qui permet de récupérer les ingrédients d'une pizza par son id
   * @param int $pizza_id
   * @return array
   */

  public function getIngredientsByPizzaId(int $pizza_id):array
  {
    //on déclare un tableau vide
    $array_result = [];
    
    //on fait la requête SQL
    $query = sprintf(
      'SELECT * 
      FROM %1$s as pi
      INNER JOIN %2$s as i ON pi.`ingredient_id` = i.`id`
      WHERE pi.`pizza_id` = :id',
      $this->getTableName(),//correspond au %1$s
      AppRepoManager::getRm()->getIngredientRepository()->getTableName()//correspond au %2$s
    );
    //on prépare la requête
    $stmt = $this->pdo->prepare($query);
    //on exécute la requête
    if(!$stmt) return $array_result;

    //on execute la requete en passant l'id de la pizza
    $stmt->execute(['id'=>$pizza_id]);

    //on récupère les résultats
    while($row_data = $stmt->fetch()){
      $array_result[] = new Ingredient($row_data);
    }
    //on retourne le tableau
    return $array_result;
  }

  /**
   * méthode qui permet d'ajouter des ingredients de pizza
   * @param array $data
   * @return bool
   */
  public function insertPizzaIngredient(array $data):bool
  {
    //:on crée la requete sql
    $query = sprintf(
      'INSERT INTO `%s` (`pizza_id`, `ingredient_id`, `unit_id`, `quantity`) VALUES (:pizza_id, :ingredient_id, :unit_id, :quantity)',
      $this->getTableName());

      $stmt = $this->pdo->prepare($query);

      if(!$stmt)return false;

      //on exécute la requete en passant les paramètres
      $stmt->execute($data);
      //on regarder si on a au moins une ligne qui a été ionsérere
      return $stmt->rowCount() > 0;
  
    }
}