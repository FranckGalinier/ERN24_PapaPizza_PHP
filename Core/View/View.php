<?php

namespace Core\View;

use App\Controller\AuthController;

class View
{
  //on doit définir le chemin absolue vers le dossier views
  public const PATH_VIEW = PATH_ROOT .'views'. DS;
  //on crée une constante pour aller dans le dossier _templates
  public const PATH_PARTIALS = self::PATH_VIEW .'_templates'. DS;
  // on déclare un titre par défaut
  public string $title ='Papa Pizza';  

  //on déclare un construc
  public function __construct(private string $name, private bool $is_completed = true)
  {

  }

  //méthode pour récupérer le chemin de la vue
  //'home/home'
  private function getRequirePath():string
  {
    //on va explode le nom de la vue pour récupérer le dossier et le fichier
    $arr_name = explode('/', $this->name);
    //on récupère le premier élément
    $category = $arr_name[0];
    //on récupère le deuxième élément
    $name = $arr_name[1];
    //si je crée un template on ajoutera un _ devant le nom du fichier
    $name_prefix = $this->is_completed ? '' : '_';
    //reste plus qu'à retourner le chemin de la vue complet
    return self::PATH_VIEW . $category . DS . $name_prefix. $name . '.html.php';
  }

  //on crée la méthode de rendu
  public function render(?array $viewdata = [])

  {
    //on récupère les données de l'utilisatuer
    $auth = AuthController::class;
    // si on a des données, on les extrait
    if(!empty($viewdata))
    {
     extract($viewdata);
    }
    //mise en cache du contenu de la vue
    ob_start();
    //on importe le template _header.html.php si la vue est complete
    if($this->is_completed)
    {
      require self::PATH_PARTIALS .'_header.html.php';
    }

    //on importe la vue
    require_once $this->getRequirePath();

    //on importe le template _footer.html.php si la vue est complete
    if($this->is_completed)
    {
      require self::PATH_PARTIALS .'_footer.html.php';
    }

    //on libère le cache
    ob_end_flush();

  }
}