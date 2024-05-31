<?php

namespace Core\Form;

class FormResult
{
  //?gestion des messages de réussite
  private FormSuccess $success_message;

  /**
   * méthode quie récupère les messages de succès
   * @param FormSuccess $success_message
   */

  public function getSuccessMessage(): FormSuccess
  {
    return $this->success_message;
  }

  /**
   * méthode qui permet d'ajouter un message de succès
   * 
   */
  public function addSuccess(FormSuccess $success):void
  {
    $this->success_message = $success;
  }

  /**
   * méthode qui vérifie si un message de succès existe
   * 
   */
  public function hasSuccess():bool
  {
    return !empty($this->success_message); // il retourne true si le message de succès n'est pas vide
  }

  //?gestion des messages d'Erreur
  private array $form_errors = [];

  /**
   * méthode qui récupère les messages d'erreur
   * @return array
   */
  public function getErrors(): array
  {
    return $this->form_errors;// Retourne les erreurs
  }

  /**
   * méthode qui permet d'ajouter un message d'erreur
   * @param FormError $error
   * @return void
   */
  public function addError(FormError $error);void
  {
    $this->form_errors[] = $error;//permet de pouvoir accumuler plusieurs erreurs dans un tableau
  }

  /**
    * méthode qui vérifie si un message d'erreur existe
    * @return bool
  */
  public function hasErrors():bool
  {
    return !empty($this->form_errors);// il retourne true si le tableau d'erreurs n'est pas vide
  }
}