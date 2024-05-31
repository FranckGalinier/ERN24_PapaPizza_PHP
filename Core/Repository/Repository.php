<?php

namespace Core\Repository;

use PDO;
use Core\Database\Database;
use Core\Database\DatabaseConfigInterface;

abstract class Repository
{
  //on crée une propriété protégée qui contiendra l'instance de PDO
  protected PDO $pdo;
  
  abstract public function getTableName(): string;

  public function __construct(DatabaseConfigInterface $config) {
    //on crée une instance de PDO 
    $this->pdo = Database::getPDO($config);
  }

  //ici on peut définir des méthodes génériques pour les repositories
  /**
   * méthode qui récupère tout les éléments de la table
   * ex: SELECT * FROM table
   * @return array
   * @param string $class_name
   */
  public function readAll(string $class_name):array
  {
    //on déclare un tableau vide
    $array_result = [];
    //on crée notre requête SQL
    $q = sprintf('SELECT * FROM %s', $this->getTableName();
    //on exécute la requête
    $stmt = $this->pdo->query($q);
    //si la requete n'est pas valide, on retourne un tableau vide
    if(!$stmt) return $array_result;
    //on récupère les données de la requête
    while($row_data = $stmt->fetch())
    {$array_result[]= new $class_name($row_data);
    }

    return $array_result;
  }

  //ici on peut définir des méthodes génériques pour les repositories
  /**
   * méthode qui récupère tout les éléments de la table
   * ex: SELECT * FROM table WHERE id = $id
   * @return object
   * @param string $class_name
   * @param int $id
   */
  public function readById(string $class_name, int $id): ?Object
  {
    //on crée notre requete SQL
    $q = sprintf('SELECT * FROM %s WHERE id = :id', $this->getTableName());
    
    //on prépare la requête
    $stmt = $this->pdo->prepare($q);
    //on vérifie que la requête est bien préparée
    if(!$stmt) return null;
    //si tout est ok, on bind les valeurs
    //ici on a pas beaucoup de paramètres, du coup on peut exécuter la requête en une seule fois
    $stmt->execute(['id' => $id]);
    $row_data = $stmt->fetch();
    // si on a des données, on retourne une instance de la classe sinon on retourne null
    return !empty($row_data) ? new $class_name($row_data) : null;
  }
}