<main class="d-flex flex-column align-items-center">
<h1 class="title title-detail">My panier</h1>
<?php use Core\Session\Session; ?>
<!-- si j'ai un message de succes on l'affiche -->
<?php include(PATH_ROOT . 'views/_templates/_message.html.php') ?>

<?php if($count_row <= 0): ?>
<div class="alert alert-info"> 
  Votre panier est vide, veuillez ajouter des pizzas
</div>
<?php else:
  $date_time = new DateTime($order->date_order);
  ?>
  <div>
    <p class="header-description">Commande : <?= $order->order_number ?> </p>
    <p class="header-description"> Panier crée le : <?= $date_time->format("d/m/Y H:i:s") ?></p>
  </div>

  <table class="table table-striped">
    <thead>
      <tr>
        <th class="footer-descrition">Nom de pizza</th>
        <th class="footer-descrition">Nombre de pizza</th>
        <th class="footer-descrition">Modifier quantité</th>
        <th class="footer-descrition">Prix total</th>
        <th class="footer-descrition">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($order->order_rows as $row): 
        //on formate l'affichage du nombre de pizza
        $total_pizza = $count_row > 1 ? $count_row . ' pizzas' : $count_row . ' pizza';
        ?>
        <tr class="footer-description">
          <td class="footer-description"><?= $row->pizza->name ?></td>
          <td class="footer-description"><?= $row->quantity ?></td>
          <td class="footer-description">
            <form action="/order/update/<?= $row->id ?>" method="POST">
              <input type="hidden" name="order_row_id" value="<?= $row->id ?>">
              <input type="hidden" name="pizza_id" value="<?= $row->pizza_id ?>">
              <input type="hidden" name="size_id" value="<?= $row->size_id ?>">
              <input type="number" name="quantity" value="<?= $row->quantity ?>" min="1" max="10" class="quantity">
              <button type="submit" class="call-action p-2">
                <i class="bi bi-check-circle"></i>
              </button>
            </form>
          </td>
          <td class="footer-description"><?= number_format($row->price, 2, ',', '.') ?> €</td>
          <td class="footer-description">
            <form action="/order-row/delete/<?= $row->id ?>" method="POST">
              <input type="hidden" name="order_id" value="<?= $order->id ?>">
              <input type="hidden" name="pizza_id" value="<?= $row->pizza_id ?>">
              <button class="call-action p-2" type="submit"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>


      <?php endforeach ?>
      <!-- ajout d'une dernière ligne sur la dernière case pour le total de la commande -->
       <tr class="footer-description">
        <td class="footer-description">Total</td>
        <td class="footer-description"><?= $total_pizza ?> </td>
        <td class="footer-description"> </td>
        <td class="footer-description">Total : <?= number_format($total, 2, ',','.') ?> €</td>
        <td class="footer-description">
          <a href="/order/confirm/<?= $order->id ?>" class="btn btn-warning">Payer <?= number_format($total, 2, ',','.') ?> €</a>
        </td>
       </tr>
    </tbody>
  </table>

<?php endif ?>







</main>