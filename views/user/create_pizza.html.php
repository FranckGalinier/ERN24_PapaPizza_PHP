
<main class="container-form">
  

<?php

use App\AppRepoManager;
use Core\Session\Session; ?>
<h1 class="title title-detail">Je crée ma Piiiizzzaaaa 	&#x1F60B;</h1>
  <!-- si j'ai un message de succes on l'affiche avec le template de message d'erreur-->
  <?php include(PATH_ROOT . 'views/_templates/_message.html.php') ?>

  <form action="/add-custom-pizza-form" method="POST" class="auth-form" enctype="multipart/form-data">

  <input type="hidden" name="user_id" value="<?= Session::get(Session::USER)->id  ?>">
  <!-- nom de la pizza -->
  <h3 class="sub-title">Le nom de pizza :</h3>
  <div class="box-auth-input">
    <input type="text" name="name" class="form-control">
  </div>
  <!-- nom de la pizza -->
  <h3 class="sub-title">Les ingredients :</h3>
  <div class="box-auth-input list-ingredient">
    <!-- ici on va devoir boucler sur notre tableau d'ingredients -->
    <?php foreach(AppRepoManager::getRm()->getIngredientRepository()->getIngredientsActiveByCategory() as $category => $ingredients): ?>
    <div class="list-ingredient-box-update">
      <h5 class="title-update"><?= ucfirst($category) ?></h5>
      <?php foreach($ingredients as $ingredient): ?>
        <div class="form-check form-switch">
          <input type="checkbox" name="ingredients[]" value ="<?= $ingredient->id ?>" 
          class="form-check-input" role="switch">
          <label class="form-check-label footer description m-1" for=""><?= $ingredient->label ?></label>

        </div>
      <?php endforeach ?>
    </div>
    <?php endforeach ?>
  </div>
  <!-- taille de la pizza -->
   <div class="box-auth-input list-size">
    <h3 class="sub-title">La taille :</h3>
    <!-- affichage de la taille -->
     <?php foreach(AppRepoManager::getRm()->getSizeRepository()->getAllSize() as $size): ?>
      <div class="d-flex align-items-center">
        <div class="list-size-input me-2">
          <input type="radio" name="size_id" value="<?= $size->id ?>">
        </div>
        <label class="footer-description"><?= $size->label ?></label>
      </div>

      <?php endforeach ?>
   </div>
   <!-- affichage du button -->
    <button type="submit" class="call-action">Je crée ma Piizzaaaa</button>
</form>
</main>