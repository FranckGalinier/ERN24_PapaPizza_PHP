<?php use Core\Session\Session; ?>


<?php if (!$auth::isAuth()) $auth::redirect('/connexion') ?> 

<h1 class="title title-detail">La "<?= $pizza->name ?>"</h1>


<!-- Gestion d'erruer -->

<!-- Affichage des erreurs -->
<?php if ($form_result && $form_result->hasErrors()):?>
    <div class="alert alert-danger" role="alert">
      <?= $form_result->getErrors()[0]->getMessage() ?>
    </div>
    <script>
    setTimeout(function() {
      <?php
      Session::remove(Session::FORM_RESULT);
      ?>
    }, 300);
    setTimeout(function() {
      document.querySelector('.alert-danger').remove();
    }, 3000);
  </script>
<?php endif ?>
<!-- si j'ai un message de succes on l'affiche -->
<?php if ($form_success && $form_success->hasSuccess()) : ?>
  <div class="alert alert-success" role="alert">
    <?= $form_success->getSuccessMessage()->getMessage() ?>
  </div>
  <script>
    setTimeout(function() {
      <?php
      Session::remove(Session::FORM_SUCCESS);
      ?>
    }, 300);
    setTimeout(function() {
      document.querySelector('.alert-success').remove();
    }, 3000);
  </script>
<?php endif ?>
<div class="container-pizza-detail">
  <div class="box-image-detail">
    <img class="image-detail" src="/assets/images/pizza/<?= $pizza->image_path ?>" alt="<?= $pizza->name ?>">
    <div class="allergene">
      <!-- gérer l'affichage des allergènes s'il y en a -->
      <?php if (in_array(true, array_column($pizza->ingredients, 'is_allergic'))) : ?>
        <h3 class="sub-title-allergene">Attention, cette pizza contient des allergènes : </h3>
        <!-- J'affiche le label de l'ingrédient allergène -->
        <?php foreach ($pizza->ingredients as $ingredient) : ?>
          <?php if ($ingredient->is_allergic) : ?>
            <div>
              <span class="badge text-bg-danger"><?= $ingredient->label ?></span>
            </div>
          <?php endif ?>
        <?php endforeach ?>
      <?php endif ?>
    </div>
  </div>
  <div class="info-pizza-detail">
    <h3 class="sub-title sub-title-detail"> Ingrédients</h3>
    <div class="box-ingredient-detail">
      <?php foreach ($pizza->ingredients as $ingredient) : ?>
        <p class="detail-description">| <?= $ingredient->label ?></p>
      <?php endforeach ?>
    </div>
    <h3 class="sub-title sub-title-detail"> Nos Prix</h3>
    <table>
      <thead>
        <tr>
          <th class="footer-description">Taille</th>
          <th class="footer-description">Prix</th>
          <th class="footer-description text-center"><i class="bi bi-basket"></i></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pizza->prices as $price): ?>
          <tr>
            <td class="footer-description"><?= $price->size->label ?></td>
            <td class="footer-description"><?= number_format($price->price, 2, ',', '.') ?> €</td>
            <td class="footer-description text-center">
              <form action="/add/order" method="POST">
                <!-- on récupère les données cachées dont on a besoin avec de inpout -->
                <input type="hidden" name="user_id" value="<?= Session::get(Session::USER)->id ?>">
                <input type="hidden" name="pizza_id" value="<?= $pizza->id ?>"> 
                <input type="hidden" name="price" value="<?= $price->price ?>">
                <input type="hidden" name="size_id" value="<?= $price->size->id ?>">
                <!-- ajout d'un input pour choisir la quantité -->
                <input type="number" name="quantity" value="1" min="1" max ="10" class="quantity">
                <!-- bouton submit -->
                <button type="submit" class="call-action p-2">
                  <i class="bi bi-plus-circle"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>