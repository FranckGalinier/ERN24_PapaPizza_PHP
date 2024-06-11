<?php

namespace App\Repository;

use App\App;
use App\Model\Size;
use App\Model\Price;
use App\AppRepoManager;
use Core\Repository\Repository;

class PriceRepository extends Repository
{
  public function getTableName(): string
  {
    return 'price';
  }


  /**
   * méthode qui permet de récupérer les prix d'une pizza par son id
   * @param int $pizza_id
   * @return array
   */

   public function getPriceByPizzaId(int $pizza_id):array
   {
     //on déclare un tableau vide
     $array_result = [];
     
     //on fait la requête SQL
     $query = sprintf(
       'SELECT prix.*, s.`label` 
       FROM %1$s as prix
       INNER JOIN %2$s as s ON prix.`size_id` = s.`id`
       WHERE prix.`pizza_id` = :id',
       $this->getTableName(),//correspond au %1$s
       AppRepoManager::getRm()->getSizeRepository()->getTableName()//correspond au %2$s
      
     );
     //on prépare la requête
     $stmt = $this->pdo->prepare($query);
     //on exécute la requête
     if(!$stmt) return $array_result;
 
     //on execute la requete en passant l'id de la pizza
     $stmt->execute(['id'=>$pizza_id]);
 
     //on récupère les résultats
     while($row_data = $stmt->fetch()){
       $price = new Price($row_data);

       //on va reconstruire à la main un tableau associatif pour créer une instance de size
      $size_data = [
        'id'=>$row_data['size_id'],
        'label'=>$row_data['label']
      ];
      //on peut maintenant instancier un objet size
      $size = new Size($size_data);
      //on va hydrater price avec size
      $price->size = $size;

      //on remplit le tableau avec price 
      $array_result[] = $price;
      
     }
     //on retourne le tableau
     return $array_result;
   }

   /**
   * méthode qui permet de récupérer les prix d'une pizza par son id avec sa taille associée
   * @param int $pizza_id
   * @return float
   */
   public function getPriceByPizzaIdBySize(int $pizza_id, int $size_id):?float
   {
     
     //on fait la requête SQL
     $query = sprintf(
       'SELECT prix.*, s.`label` 
       FROM %1$s as prix
       INNER JOIN %2$s as s ON prix.`size_id` = s.`id`
       WHERE prix.`pizza_id` = :id AND prix.`size_id` = :size_id',
       $this->getTableName(),//correspond au %1$s
       AppRepoManager::getRm()->getSizeRepository()->getTableName()//correspond au %2$s
      
     );
     //on prépare la requête
     $stmt = $this->pdo->prepare($query);
     //on exécute la requête
     if(!$stmt) return null;
 
     //on execute la requete en passant l'id de la pizza
     $stmt->execute(['id'=>$pizza_id, 'size_id'=>$size_id]);
 
     //on récupère les résultats
     $result = $stmt->fetchObject();

      if(!$result) return null;

      // on retourne le prix
      return $result->price;
   }

   /**
   * méthode qui permet d'ajouter le prix d'une pizza
   * @param array $data
   * @return bool
   */
  public function addPrice(array $data):bool
  {
    //on crée la requete sql
    $query = sprintf(
      'INSERT INTO `%s` ( `price`, `size_id`, `pizza_id`) VALUES (:price, :size_id, :pizza_id)',
      $this->getTableName());

      $stmt = $this->pdo->prepare($query);

      if(!$stmt)return false;

      //on exécute la requete en passant les paramètres
      $stmt->execute($data);
      //on regarder si on a au moins une ligne qui a été ionsérere
      return $stmt->rowCount() > 0;
  
    }
}