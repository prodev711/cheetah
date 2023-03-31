<?php 
  // var_dump($_SERVER['REQUEST_SCHEME']);
  require_once (__DIR__.'/../../../../wp-load.php');
  date_default_timezone_set('America/New_York');
  $orderId = WC()->session->get('order_id');
  if ( $orderId == NULL ) {
    echo '<h1>There is no order</h1>';
    exit;
  }
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
            ><?php echo date('m.d');?></span>
        </div>

        <!-- Content Transaction Detail -->
        <div class="transaction-detail-block card-block p-3 p-lg-4 p-xl-5 mb-4">
          <h4>Transaction XY08122022</h4>
          <h2 class="mb-3 amountvalue">... €</h2>
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

          <h6 class="mb-4">
            Sélectionnez une cryptomonnaie pour effectuer votre paiement.
          </h6>
          <div class="transaction-detail-block-content mb-4">
            <ul class="radio-list">
              <li>
                <label>
                  <input type="radio" name="radio-list" value = '1' />
                  <span>
                    <img src="wp-content/plugins/cheetah/cryptohome/images/ic-btc.png" alt="" />
                    <em>Bitcoin</em>
                    <i>BTC</i>
                  </span>
                </label>
              </li>
              <li>
                <label>
                  <input type="radio" name="radio-list" value='2'/>
                  <span>
                    <img src="wp-content/plugins/cheetah/cryptohome/images/ic-eth.png" alt="" />
                    <em>Ethereum</em>
                    <i>ETH</i>
                  </span>
                </label>
              </li>
            </ul>
          </div>

          <div
            class="btn-outer d-flex align-items-center justify-content-between"
          >
            <a href="#" title="Voir plus" class="read-more-link">Voir plus</a>
            <button class="btn btn-primary rounded-pill choosePayment">Suivant</button>
          </div>
        </div>
      </div>
      <!-- End Content Block -->
    </div>

    <!-- JavaScripts -->
    <script src="wp-content/plugins/cheetah/cryptohome/js/jquery-3.6.0.min.js"></script>
    <script src="wp-content/plugins/cheetah/cryptohome/js/bootstrap.bundle.min.js"></script>
    <script>
      const apiKey = "<?php echo $apiKey;?>";
      const userId = "<?php echo $userId ;?>";
      const orderId = "<?php echo $orderId;?>" ;
      console.log(orderId);
      var checkoutId = "";
      const query = `
        query GenerateCheckoutSession($apiKey: String!, $orderId: String!, $userId: String!) {
          generateCheckoutSession(apiKey: $apiKey, orderId: $orderId, userId: $userId) {
            chainIds
            chains {
              _id
              address
              chainType
              createdAt
              imageUrl
              name
              symbol
              updatedAt
            }
            checkoutId
            price
          }
        }
      `;
      fetch("https://cheetah-backend.herokuapp.com/graphql",{
        method : "POST",
        headers : {
          "Content-Type": "application/json",
          "Accept" : "application/json"
        },
        body: JSON.stringify({
          query:query,
          variables: {
            apiKey: apiKey,
            orderId: orderId,
            userId: userId
          },
          operationName: "GenerateCheckoutSession"
        })
      }).then(response => response.json())
      .then(data => {
        window.localStorage.setItem("checkoutSession",JSON.stringify(data.data.generateCheckoutSession))
        $(".amountvalue").html(data.data.generateCheckoutSession.price + " €");
      })
      .catch(err => console.error(err));
    </script>
    <script src="wp-content/plugins/cheetah/cryptohome/js/custom.js"></script>
    <script src="wp-content/plugins/cheetah/web3.js/dist/web3.min.js"></script>
  </body>
</html>
