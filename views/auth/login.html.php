<main class="container-form">
  <h1>Je me connecte</h1>
  <!-- Affichage des erreurs s'il y en a -->
  <!-- si form result et form result a une erruer alors on affiche l'erreur -->
  <?php if ($form_result && $form_result->hasErrors()):?>
    <div class="alert alert-danger" role="alert">
      <?= $form_result->getErrors()[0]->getMessage() ?>
    </div>
  <?php endif ?>

  <!-- Formulaire -->
  <form class="auth-form" action="/login" method="POST">
    <div class="box-auth-input">
      <label class="detail-description">Adresse Email</label>
      <input type="email" class="form-control" name="email">
    </div>
    <div class="box-auth-input">
      <label class="detail-description">Mot de passe</label>
      <input type="password" class="form-control" name="password">
    </div>
    <button class="call-action" type="submit">Je m'inscris</button>
  </form>

  <p class="header-description">Je n'ai pas de compte, <a class="auth-link" href="/inscription">Je m'inscrit</a></p>
</main>