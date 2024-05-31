<?php

namespace App\Controller;

use App\Model\User;
use Core\View\View;
use App\AppRepoManager;
use Core\Form\FormError;
use Core\Form\FormResult;
use Core\Session\Session;
use Core\Controller\Controller;
use Laminas\Diactoros\ServerRequest;

class AuthController extends Controller
{
  /**
   * méthode qui renvoie la vue du formulaire de connexion
   * @return void
   */
  public function loginForm()
  {
    $view = new View('auth/login');
    $view_data =[
      'form_result' => Session::get(Session::FORM_RESULT) // on récupère les erreurs de la session et on les stocke dans form_result
    ];
    $view->render($view_data); // ici on envoie les erreurs à la vue

  }
  
  /**
   * méthode qui renvoie la vue du formulaire d'enregistrement
   * @return void
   */
  public function registerForm()
  {
    $view = new View('auth/register');
    $view_data =[
      'form_result' => Session::get(Session::FORM_RESULT) // on récupère les erreurs de la session et on les stocke dans form_result
    ];
    $view->render($view_data); // ici on envoie les erreurs à la vue

  }

  /**
   * méthode qui permet de traiter le formulaire d'enregistrement
   */
  public function register(ServerRequest $request)
  {
    $data_form= $request->getParsedBody(); // on récupère les données du formulaire
    //on instancie formResult pour stocker les messages d'erreurs
    $formResult = new FormResult();
    //on doit crée une instance de User
    $user = new User();

    //on s'occupe de toute les vérifications
    if(
      empty($data_form['email']) ||
      empty($data_form['password']) ||
      empty($data_form['password_confirm']) ||
      empty($data_form['firstname']) ||
      empty($data_form['lastname']) ||
      empty($data_form['phone'])
    ){
      $formResult->addError(new FormError('Veuillez remplir tous les champs'));
    } elseif($data_form['password'] !== $data_form['password_confirm'])
    {
      $formResult->addError(new FormError('Les mots de passe ne correspondent pas'));
    } elseif(!$this->validEmail($data_form['email']))
    {
      $formResult->addError(new FormError('L\'email n\'est pas valide'));
    } elseif(!$this->validPassword($data_form['password']))
    {
      $formResult->addError(new FormError('Le mot de passe doit contenir au moins 8 caractères,
       une majuscule, une minuscule et un chiffre'));
    }


    var_dump($data_form);
  }

  /**
   * méthode qui permet de traiter le formulaire de connexion
   */
  public function login()
  {}


  /**
   * méthode qui vérifie que l'email est valide au bon format
   * @param string $email
   * @return bool
   */
  public function validEmail(string $email):bool
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  /**
   * méthode qui vérifie que le mot de passe est valide donc qui contient au moins 8 caractères, une majuscule, une minuscule et un
   * chiffre
   * @param string $password
   * @return bool
   */
  public function validPassword(string $password):bool
  {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password);
  }

  /**
   * méthode qui vérifie si l'utilisateur existe
   * @param string $email
   * @return bool
   */
  public function userExist(string $email):bool
  {
    $user = AppRepoManager::getRm()->getUserRepository()->findUserByEmail($email);
  }
}
