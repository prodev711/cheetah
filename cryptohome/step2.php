<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Cheeta</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
      name="description"
      content="Meta descriptions may be included in search results to concisely summarize page content."
    />
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="css/main.css" />
  </head>

  <body>
    <div class="row m-0 wrapper">
      <!-- Start Left Block -->
      <div class="col-lg-6 bg-primary left-block d-none d-lg-block">
        <div class="left-block-heading">
          <a href="#" title="Cheeta" class="logo">
            <img src="images/logo.svg" alt="Cheeta" />
          </a>
        </div>
        <img src="images/logo-symbol.svg" alt="" class="bg-img" />
      </div>
      <!-- End Left Block -->

      <!-- Start Content Block -->
      <div class="col-lg-6 content-block">
        <!-- Content Block Heading -->
        <div
          class="content-block-heading d-lg-flex justify-content-between align-items-center mb-4 text-center text-lg-start"
        >
          <a href="#" title="Cheeta" class="logo d-lg-none d-inline-block mb-5">
            <img src="images/logo-dark.svg" alt="Cheeta" />
          </a>
          <div class="content-block-heading-left-part">
            <h4>Envoyer un paiement à</h4>
            <h2>Zalando</h2>
          </div>
          <span class="remaining-time text-white rounded-pill d-none d-lg-flex"
            >44:32</span
          >
        </div>

        <!-- Content Transaction Detail -->
        <div class="transaction-detail-block card-block p-3 p-lg-4 p-xl-5 mb-4">
          <h4>Transaction XY08122022</h4>
          <h2 class="mb-3">149,90€</h2>
          <h6 class="text-light">À régler avant le 30 Septembre 2022</h6>
        </div>

        <!-- Content Transaction Detail -->
        <div class="transaction-detail-block card-block p-3 p-lg-4 p-xl-5">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bolder">Méthode de paiement</h4>
            <span class="remaining-time text-white rounded-pill d-lg-none"
              >44:32</span
            >
          </div>

          <div class="you-choose-block mb-4 mb-lg-5">
            <h6 class="d-flex align-items-center">
              <strong>Vous avez choisi :</strong>
              <span class="d-flex align-items-center ps-3">
                <img src="images/ic-usdt.png" alt="" />
                <em>Tether</em>
                <i>USDT</i>
              </span>
            </h6>
          </div>

          <h6 class="mb-4">
            Sélectionnez une cryptomonnaie pour effectuer votre paiement.
          </h6>
          <div class="transaction-detail-block-content mb-4">
            <ul class="radio-list">
              <li>
                <label>
                  <input type="radio" name="radio-list" checked />
                  <span>
                    <img src="images/ic-eth.png" alt="" />
                    <em>Ethereum</em>
                    <i>ERC20</i>
                  </span>
                </label>
              </li>
              <li>
                <label>
                  <input type="radio" name="radio-list" />
                  <span>
                    <img src="images/ic-bnb.png" alt="" />
                    <em>Binance</em>
                    <i>BEP20</i>
                  </span>
                </label>
              </li>
            </ul>
          </div>

          <div
            class="btn-outer d-flex align-items-center justify-content-between"
          >
            <button class="btn btn-outline-primary rounded-pill">
              Précédent
            </button>
            <button class="btn btn-primary rounded-pill">Valider</button>
          </div>
        </div>
      </div>
      <!-- End Content Block -->
    </div>

    <!-- JavaScripts -->
    <script src="js/main.js"></script>
  </body>
</html>
