<?php

namespace Core\Database;

use PDO;
// SINGLETON PATTERN

class Database
{
  private const PDO_OPTIONS =[
    //attr default  prends les donnée des requetes et nous le retourne en tableau associatif
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    
  ];
  //préparation du singleton pour stocker l'instance de PDO
  private static ?PDO $pdoinstance = null; // ?PDO = PDO ou null
  //PDO = PHP Data Object

  //fonction qui est accessible pour récupérer l'instance de PDO
  public static function getPDO(DatabaseConfigInterface $config): PDO
  {
    if (is_null(self::$pdoinstance)) { // si pdo instance est null
      //on créee une instance de PDO
      // ici on utilise sprintf pour formater la chaine de caractère avec les valeurs de la config
      $dsn =sprintf('mysql:dbname=%s;host=%s', $config->getName(), $config->getHost());
      self::$pdoinstance = new PDO(
        $dsn,
        $config->getUser(),
        $config->getPass(),
        self::PDO_OPTIONS);
    }
    //ici retoune une instance de PDO
    return self::$pdoinstance;
  }
  //on empeche l'instanciation de la classe avec le private function __construct(){}
  private function __construct(){}
  //on empeche le clonage de la classe avec le private function __clone(){}
  private function __clone(){}

}