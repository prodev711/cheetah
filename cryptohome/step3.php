<?php
  require_once (__DIR__.'/../../../../wp-load.php');
  $method = $_GET['method'] ;
  $address = $_GET['address'];
  $product = json_decode(WC()->session->get('order_product'));
  $userId = get_current_user_id();
  $apiKey = get_option('custom_cheetah_api_key');
?>
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
    <link rel="shortcut icon" href="wp-content/plugins/cheetah/cryptohome/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="wp-content/plugins/cheetah/cryptohome/css/main.css" />
  </head>

  <body>
    <div class="row m-0 wrapper">
      <!-- Start Left Block -->
      <div class="col-lg-6 bg-primary left-block d-none d-lg-block">
        <div class="left-block-heading">
          <a href="#" title="Cheeta" class="logo">
            <img src="wp-content/plugins/cheetah/cryptohome/images/logo.svg" alt="Cheeta" />
          </a>
        </div>
        <img src="wp-content/plugins/cheetah/cryptohome/images/logo-symbol.svg" alt="" class="bg-img" />
      </div>
      <!-- End Left Block -->

      <!-- Start Content Block -->
      <div class="col-lg-6 content-block">
        <!-- Content Block Heading -->
        <div
          class="content-block-heading d-lg-flex justify-content-between align-items-center mb-4 text-center text-lg-start"
        >
          <a href="#" title="Cheeta" class="logo d-lg-none d-inline-block mb-5">
            <img src="wp-content/plugins/cheetah/cryptohome/images/logo-dark.svg" alt="Cheeta" />
          </a>
          <div class="content-block-heading-left-part">
            <h4>Envoyer un paiement à</h4>
            <h2>Zalando</h2>
          </div>
          <span class="remaining-time text-white rounded-pill d-none d-lg-flex"
            ><?php echo date('m.d');?></span
          >
        </div>

        <!-- Content Transaction Detail -->
        <div class="transaction-detail-block card-block p-3 p-lg-4 p-xl-5 mb-4">
          <h4>Transaction XY08122022</h4>
          <h2 class="mb-3 amountvalue">... ETH</h2>
          <h6 class="text-light">À régler avant le 30 Septembre 2022</h6>
        </div>

        <!-- Content Transaction Detail -->
        <div class="transaction-detail-block card-block p-3 p-lg-4 p-xl-5">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bolder">Méthode de paiement</h4>
            <span class="remaining-time text-white rounded-pill d-lg-none"
              ><?php echo date('m.d');?></span
            >
          </div>

          <div
            class="you-choose-block mb-4 mb-lg-5 d-lg-flex justify-content-between align-items-center"
          >
            <h6 class="d-flex align-items-center fw-bolder">
              Vous avez choisi :
              <span class="d-flex align-items-center ps-3 fw-normal">
                <img src="wp-content/plugins/cheetah/cryptohome/images/ic-usdt.png" alt="" />
                <em>Tether</em>
                <i>USDT</i>
              </span>
            </h6>

            <h6 class="fw-bolder text-end">Réseaux ERC20</h6>
          </div>

          <h6 class="fw-bolder mb-3">Paiement rapide</h6>
          <a
            href="#"
            class="btn btn-primary rounded-pill d-block mb-5 btn-lg proceed_pay_ether"
            title=`Payer ${$amount}€ avec Metamask`
            >Payer ... ETH</a
          >
          <h6 class="mb-3">
            Ou envoyez 154,870000 USDT (en un seul paiement) à l’adresse
            indiquée ci-dessous.
          </h6>

          <div class="row address-form">
            <div class="col">
              <input
                type="text"
                class="form-control rounded-pill"
                value="TQodgX8gBBzubAexj5zYCvGtg"
                disabled
              />
            </div>
            <div class="col-auto ps-0">
              <button class="btn btn-primary rounded-pill">Copier</button>
            </div>
          </div>
        </div>
      </div>
      <!-- End Content Block -->
    </div>

    <!-- JavaScripts -->
    <script src="wp-content/plugins/cheetah/cryptohome/js/jquery-3.6.0.min.js"></script>
    <script src="wp-content/plugins/cheetah/cryptohome/js/bootstrap.bundle.min.js"></script>
    <script>
      var Address = '<?php echo $address; ?>';
      var Method = <?php echo $method ;?>;
      var apiKey = '<?php echo $apiKey;?>';
      var checkoutSession = JSON.parse(window.localStorage.getItem('checkoutSession'));
      var Amount = checkoutSession.price ;
      const url = "https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=eur";
      fetch(url)
        .then(response => response.json())
        .then(data => {
          const conversionValue = data.ethereum.eur;
          Amount = Amount / conversionValue;
          $(".amountvalue").html(Amount+" ETH");
          $(".proceed_pay_ether").html('Payer ' + Amount+" ETH")
        })
        .catch(error => console.error(error));
    </script>
    <script src="wp-content/plugins/cheetah/cryptohome/js/custom.js"></script>
    <script src="wp-content/plugins/cheetah/web3.js/dist/web3.min.js"></script>
  </body>
</html>
