<?php

namespace Core\Session;

abstract class SessionManager
{
  /**
   * méthode qui alimente notre session
   * @return void
   * @param string $key
   * @param mixed $value
   */
  public static function set(string $key, mixed $value):void
  {
    $_SESSION[$key] = $value;
  }

   /**
   * méthode qui récupère une valeur notre session
   * @param string $key
   * @return mixed
   */
  public static function get(string $key):mixed
  {
    if(!isset($_SESSION[$key])) return null;
    return $_SESSION[$key];
  }

  /**
   * méthode qui supprime une valeur de notre session
   * @param string $key
   * @return void
   */

  public static function remove(string $key):void
  {
    if(!self::get($key)) return; // si la clé n'existe pas on sort
    unset($_SESSION[$key]);// sinon on supprime la clé
  }
}