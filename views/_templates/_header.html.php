<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Import librairie des icones bootstrap-->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">

    <!-- Toujours importer la librairie Bootstrap CSS avant la feuille de style CSS -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="/style_homepage.css">
    <link rel="stylesheet" href="/auth_style.css">
    <link rel="stylesheet" href="/style_pizza.css">

    <title>Papa Pizza</title>
</head>

<body>
  <header>
        <div id="topbar">
            <div class="line1">

                <div class="box-phone">
                    <i class="bi bi-telephone"></i>
                    <span>04 68 89 65 22</span>
                </div>

                <div class="box-social-icons">
                    <a href="#"> <i class="bi bi-facebook"></i> </a>
                    <a href="#"> <i class="bi bi-instagram"></i> </a>
                    <a href="#"> <i class="bi bi-twitter-x"></i> </a>
                </div>

            </div>
            <div class="line2">

                <!-- 1er block : logo du site -->
                <div class="nav-logo">
                    <a href="/">
                        <img class="logo-papapizza" src="/assets/images/homepage/papapizza.svg" alt="logo Papapizza">
                    </a>
                </div>

                <!-- 2ème block : navigation -->
                <div class="nav-list">
                    <nav class="custom-nav">
                        <ul class="custom-ul">
                            <li class="custom-link"><a href="/">Accueil</a></li>
                            <li class="custom-link"><a href="#">Carte</a></li>
                            <li class="custom-link"><a href="#">Actualités</a></li>
                            <li class="custom-link"><a href="#">Contact</a></li>
                        </ul>
                    </nav>
                </div>

                <!-- 3ème block : menu du profil -->
                <div class="nav-profil">
                    <nav class="custom-nav-profil">
                        <ul class="custom-ul-profil">
                            <li class="custom-link"><a href="/connexion">Se connecter <i class="bi bi-person custom-svg"></i></a>
                            </li>
                            <li class="custom-link end-link"><a href="#"><i class="bi bi-cart"></i></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>