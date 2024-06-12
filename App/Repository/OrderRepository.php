<?php

namespace App\Repository;

use App\App;
use App\Model\Order;
use App\AppRepoManager;
use Core\Repository\Repository;

class OrderRepository extends Repository
{
  public function getTableName(): string
  {
    return 'order';
  }

  /**
   * méthode qui permet de récupérer la dernière commande
   * @return int|null
   */

  public function findLastOrder():?int
  {
    $query = sprintf(
      'SELECT *
      FROM `%s`
      ORDER BY id DESC LIMIT 1',
      $this->getTableName()
    );
    $stmt = $this->pdo->query($query);

    if(!$stmt)return null;

    $result = $stmt->fetchObject();

    return $result->id ?? 0;
  }

  /**
   * méthode qui retourne une commande si elle est dans le panier
   * @param int $user_id
   * @param string $status
   * @return bool 
   */
  public function findLastStatusByUser(int $user_id, string $status):bool
    {
      $query= sprintf('SELECT *
      FROM `%s`
      WHERE `user_id` = :user_id
      AND status = :status
      ORDER BY id DESC
      LIMIT 1',
      $this->getTableName());

      //on prépare la requete
      $stmt = $this->pdo->prepare($query);
      //on vérifie que la requete est bien préparée
      if(!$stmt->execute(['user_id'=>$user_id, 'status'=>$status])) return false;

      //on récupère les résultats
      $result = $stmt->fetchObject();

      //si je n'ai pas de résultat je retourne false
      if(!$result) return false;

      //Si on a des résulats on vérifie que la commande contient bien des lignes
      $count_row= $this->countOrderRows($result->id);
      //si on a pas de résultat on renvoir false
      if(!$count_row) return false;
      //si on a des résultats on retourne true
      return true;

    }

    /**
     * méthode qui retourne le nombre de ligne de commande
     * @param int $order_id
     * @return int|null
     */
    public function countOrderRows(int $order_id):?int
    {
      //query qui additiione les nombre de ligne dde commande

      $qq = sprintf(
        'SELECT SUM(quantity) as count
        FROM `%s`
        WHERE order_id = :order_id',
        AppRepoManager::getRm()->getOrderRowRepository()->getTableName()
      );

       //on prépare la requete
       $stmt = $this->pdo->prepare($qq);
       //on vérifie que la requete est bien préparée
       if(!$stmt->execute(['order_id'=>$order_id])) return 0;
 
       //on récupère les résultats
       $result = $stmt->fetchObject();
  
       //si je n'ai pas de résultat je retourne false
       if(!$result || is_null($result)) return 0;
       //sinon on retourne le nombre de ligne de commande
        return $result->count;


    }

    /**
     * méthode qui permet de créer une commande
     * @param array $data
     * @return int|null
     */

    public function createOrder(array $data):?int
    {
      $q = sprintf('INSERT INTO `%s`
      (`order_number`, `date_order`, `status`, `user_id`)
      VALUES(:order_number, :date_order, :status, :user_id)',
      $this->getTableName());

      //préparation de la requete
      $stmt = $this->pdo->prepare($q);

      //si la reuquete n'est pas exécuté, on retourne null
      if(!$stmt->execute($data)) return null;

      //si elle s'est exécuté on retourne l'id de la commande
      return $this->pdo->lastInsertId();
    }

    /**
     * méthode qui retourne l'id de la commande si le statut = IN_CARD
     * @param int $user_id
     * @return null|înt 
     */

     public function findOrderIdByStatus (int $user_id):?int
     {
      $status = Order::IN_CART;

      //on créer la requete sql
      $query = sprintf(
        'SELECT *
        FROM`%s`
        WHERE `user_id` = :user_id
        AND `status` = :status
        ORDER BY id DESC
        LIMIT 1',
        $this->getTableName()
      );

      //on prépare la requete
      $stmt = $this->pdo->prepare($query);

      //on vérifie que la requete est bien préparée
      if(!$stmt->execute(['user_id'=>$user_id, 'status'=>$status])) return null;

      //on récupère les résultats
      $result = $stmt->fetchObject();
      //si on a pas de résultat on retourne null
      if(!$result) return null;
      //sinon on retourne l'id de la commande
      return $result->id;
     }

     /**
      * méthode qui récupère la commande en cours d'un utilisateur avec une ligne de commande
      * @param int $user_id
      * @return Object|null
      */
    public function findOrderInProgressWithOrderRow(int $user_id):?object
    {
      //on crée la requete sql
      $q = sprintf(
        'SELECT * 
        FROM `%s`
        WHERE user_id = :user_id
        AND status = :status',
        $this->getTableName()
      );

      //on prépare la requete
      $stmt = $this->pdo->prepare($q);

      //on execute la requete 
      if(!$stmt->execute(['user_id'=>$user_id, 'status'=>Order::IN_CART])) return null;

      //on récupère les résultats
      $result = $stmt->fetchObject();

      //si pas de résultat on retourne null
      if(!$result) return null;

      //on doit hydrater notre objet order avec les lignes de commande
      $result->order_rows = AppRepoManager::getRm()->getOrderRowRepository()->findOrderRowByOrder($result->id);

      return $result;


    }

    /**
     * méthode qui permet de supprimer une commande
     * @param int $id
     * @return bool
     */
    public function deleteOrder(int $id):bool
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
     * méthode qui recupère le nombre ligne de commande d'une commande
     * @param int $order_id
     * @return int
     */
    public function countOrderRowByOrder(int $order_id):int
    {
        //on cree la requete SQL
        $q = sprintf(
            'SELECT COUNT(*) AS count 
            FROM `%s` 
            WHERE `order_id` = :order_id',
            $this->getTableName()
        );

        //on prepare la requete
        $stmt = $this->pdo->prepare($q);

        //on verifie que la requete est bien executée
        if(!$stmt->execute(['order_id' => $order_id])) return 0;

        //on recupere le resultat
        $result = $stmt->fetchObject();

        return $result->count;
    }

    /**
     * méthode qui permet de récupérer une commande avec son id avec toutes ces lignes de commances
     * @param int $order_id
     * @return Object|null
     */
    public function findOrderByIdWithRow(int $order_id):?object
    {
      //on crée la requete sql
      $q = sprintf(
        'SELECT *
        FROM `%s`
        WHERE `id` = :order_id',
        $this->getTableName()
      );

      //on prépare la requete
      $stmt = $this->pdo->prepare($q);

      //on reagrde si la requete est bien préparée
      if(!$stmt->execute(['order_id'=>$order_id])) return null;

      //on récupère les résultats
      $result = $stmt->fetchObject();

      //on va hydrater objet order avec toutes les lignes de commande
      $result->order_rows = AppRepoManager::getRm()->getOrderRowRepository()->findOrderRowByOrder($result->id);

      return $result;

    }


    /**
     * méthode qui permet de mettre à jour le statut d'une commande
     * @param array $data
     * @return bool
     */
    public function updateOrder(array $data ):bool
    {
      //on crée la requete sql
      $q = sprintf(
        'UPDATE `%s`
        SET `status` = :status
        WHERE `id` = :id',
        $this->getTableName()
      );

      //on prépare la requete
      $stmt = $this->pdo->prepare($q);

      return $stmt->execute($data);

    }

}