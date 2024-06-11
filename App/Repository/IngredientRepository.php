<?php

namespace App\Repository;

use App\Model\Ingredient;
use Core\Repository\Repository;

class IngredientRepository extends Repository
{
  public function getTableName(): string
  {
    return 'ingredient';
  }

  /**
   * méthode pour récupérer tout les ingrédients actifs rangé par category 
   * @return array
   */
  public function getIngredientsActiveByCategory():array
  {
    //on déclare un tableau vide
    $array_result = [];
    //on prépare la requete
    $query= 
    sprintf('SELECT * FROM %s WHERE `is_active` = 1 ORDER BY `category` ASC',
    $this->getTableName());

    // on exécute la requete
    $stmt = $this->pdo->query($query);

    //on vérifie que la requete est bien exécuté
    if(!$stmt) return $array_result;
    
    //on récupère les résultats
    while($row_data = $stmt->fetch())
    {
      $array_result[$row_data['category']][] = new Ingredient($row_data);
    }
    return $array_result;
  }
}