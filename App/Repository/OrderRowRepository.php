<?php

namespace App\Repository;

use App\Model\Pizza;
use App\AppRepoManager;
use App\Model\OrderRow;
use Core\Repository\Repository;

class OrderRowRepository extends Repository
{
  public function getTableName(): string
  {
    return 'order_row';
  }

  /**
   * méthode qui permet d'ajouter une ligne de commande
   * @param array $data
   * @return bool
   */

  public function insertOrderRow(array $data): bool
  {
    //on crée la requete sql
    $query = sprintf(
      'INSERT INTO `%s` (`order_id`, `pizza_id`, `quantity`, `price`, `size_id`)
       VALUES (:order_id, :pizza_id, :quantity, :price, :size_id)',
      $this->getTableName()
    );

    $stmt = $this->pdo->prepare($query);
    if (!$stmt->execute($data)) return false;
    return true;
  }

  /**
   * méthode qui permet de récupérer les lignes de commande d'une commande
   * @param int $order_id
   * @return array
   */
   public function findOrderRowByOrder(int $order_id):?array
   {
     //on déclare un tableau vide
    $array_result = [];
    //on crée la requete sql
    $query = sprintf(
      'SELECT *
      FROM `%s`
      WHERE `order_id` = :order_id',
      $this->getTableName()
    );
    //on prépare la requete
    $stmt = $this->pdo->prepare($query);

    //on vérifie que la requete est bien préparée
    if (!$stmt->execute(['order_id' => $order_id])) return null;

    //on récupère les résultats dans une boucle
    while ($result =$stmt->fetch())
    {
      $orderRow = new OrderRow($result);

      //on va hydrater OrderRow pour avoir les infos de la ligne de commande
      $orderRow->pizza = AppRepoManager::getRm()->getPizzaRepository()->readById(Pizza::class, $orderRow->pizza_id);

      //on ajout de l'objet OrderRow dans le tableau
      $array_result[] = $orderRow;

    }

    return $array_result;

   }

   /**
    * méthode qui calcule le montant total d'une commande
    * @param int $order_id
    * @return float
    */
    public function findTotalPriceByOrder(int $order_id):?float
    {
      //on créé la requere sql
      $q = sprintf(
        'SELECT SUM(`price`) AS total_price
        FROM `%s`
        WHERE `order_id` = :order_id',
        $this->getTableName()
      );

      //on prépare la requete
      $stmt = $this->pdo->prepare($q);

      //on véririe que la requete est bien préparée
      if (!$stmt->execute(['order_id' => $order_id])) return null;

      //on récupère le résultat
      $result = $stmt->fetchObject();

      return $result->total_price ??0;
    }

    /**
     * méthode qui additionne le nombre de pizzas pour chaque ligne de commande
     * @param int $order_id
     * @return ?int
     */
    public function countOrderRow(int $order_id): ?int
    {
      //on crée la requete sql
      $q = sprintf(
        'SELECT SUM(`quantity`) AS total_quantity
        FROM `%s`
        WHERE `order_id` = :order_id',
        $this->getTableName()
      );

      //on prépare la requere
      $stmt = $this->pdo->prepare($q);

      //on vérifie que la requete est bien préparée

      if (!$stmt->execute(['order_id' => $order_id])) return null;

      //on récupère le résultat
      $result = $stmt->fetchObject();

      return $result->total_quantity ?? 0;
    }

    /**
     * méthode qui permet de mettre à jour une ligne de commande
     * @param array $data
     * @return bool
     */
    public function updateOrderRow(array $data):bool
    {
      //on récupère le prix de la pizza
      $pizza_price = AppRepoManager::getRm()->getPriceRepository()->getPriceByPizzaIdBySize($data['pizza_id'], $data['size_id']);
      var_dump($pizza_price);
      //on va recalculer le prix total avec la nouvelle quantité
      $price = $pizza_price * $data['quantity'];
      $q= sprintf(
        'UPDATE `%s`
        SET `quantity` = :quantity, `price` = :price
        WHERE `id` = :id',
        $this->getTableName()
      );
      $stmt = $this->pdo->prepare($q);

      if(!$stmt) return false;

      return $stmt->execute([
        'id' => $data['id'],
        'quantity' => $data['quantity'],
        'price' => $price
      ]);
    }

    /**
     * méthode qui permet de supprimer une ligne de commande
     * @param int $id
     * @return bool
     */
    public function deleteOrderRow(int $id):bool
    {
      //on crée la requete sql
      $q = sprintf(
        'DELETE FROM `%s`
        WHERE `id` = :id',
        $this->getTableName()
      );

      //on prépare la requete
      $stmt = $this->pdo->prepare($q);

      //on vérifie que la requete est bien préparée
      if(!$stmt) return false;

      return $stmt->execute(['id' => $id]);
    }

    /**
     * méthode qui permet de récupérer le nombre de lignes de commande d'une commande
     * @param int $order_id
     * @return int
     */
    public function countOrderRowByOrder(int $order_id):int
    {
      //on crée la requete sql
      $q = sprintf(
        'SELECT COUNT(`id`) AS total_row
        FROM `%s`
        WHERE `order_id` = :order_id',
        $this->getTableName()
      );

      //on prépare la requete
      $stmt = $this->pdo->prepare($q);

      //on vérifie que la requete est bien préparée
      if(!$stmt->execute(['order_id' => $order_id])) return 0;

      //on récupère le résultat
      $result = $stmt->fetchObject();

      return $result->total_row;
    }
}
