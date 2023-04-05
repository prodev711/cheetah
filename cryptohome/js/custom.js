$(document).ready(function () {
  var connectedAccounts ;
  var states = "start";
  if ( typeof web3 !== "undefined" && web3.currentProvider !== null ){
    web3 = new Web3(web3.currentProvider);
  } else {
    web3 = new Web3(new Web3.providers.HttpProvider('http://localhost:8545'));
  }
  web3.eth.getBlockNumber((err,BlockNumber) => {
    if ( err ){
      console.error(err);
    } else {
      console.log(BlockNumber);
    }
  })
  //Prevent Page Reload on all # links
  $("body").on("click", "a[href='#']", function (e) {
    e.preventDefault();
  });

  //placeholder
  $("[placeholder]").each(function () {
    $(this).attr("data-placeholder", this.placeholder);
    $(this).bind("focus", function () {
      this.placeholder = "";
    });
    $(this).bind("blur", function () {
      this.placeholder = $(this).attr("data-placeholder");
    });
  });

  // Add remove class when window resize finished
  var $resizeTimer;
  $(window).on("resize", function (e) {
    if (!$("body").hasClass("window-resizing")) {
      $("body").addClass("window-resizing");
    }
    clearTimeout($resizeTimer);
    $resizeTimer = setTimeout(function () {
      $("body").removeClass("window-resizing");
    }, 250);
  });

  // Add new js functions here -----------------------------------------------------------------
 

  // Don't add anything below this --------------------------------------------------------------
  // Add Class on Window Load
  $("body").addClass("page-loaded");
  $(".choosePayment").on("click",function() {
    var crypto = $('input[name="radio-list"]:checked').val();
    if ( crypto == undefined ) {
      toastr.error("please choose payment");
      return ;
    }
    if ( connectedAccounts?.length > 0 ){
      window.location.href = `${homeUrl}/payment?method=${crypto}&address=${connectedAccounts[0]}`;
    } else {
      web3.eth.requestAccounts().then(accounts => {
        const account = accounts[0];
        window.location.href = `${homeUrl}/payment?method=${crypto}&address=${account}`;
      }).catch(error => {
        toastr.error("Can't connect wallet");
      })
    }
  })
  var startProcessing = () => {
    states = "process";
    $(".loader-text").html("on process ...");
    $(".proceed_click").css('display','none');
    $(".proceed_loading").css('display','block');
  }
  var stopProcessing = () => {
    states = "start";
    $(".proceed_click").css('display','block');
    $(".proceed_loading").css('display','none');
  }
  $(".proceed_pay_ether").on("click",function() {
    console.log(states);
      if ( states == "process" ){
        toastr.warning("Currently in progress.");
        return ;
      }
      if ( states != "process" ){
        startProcessing();
      }
      web3.eth.getBalance(Address,(err,balances) => {
        if ( err ){
          toastr.error("Proccess error, please try again");
          stopProcessing();
          return ;
        }
        if ( balances < Amount ) {
          toastr.warning("Sorry. Your account has not enough to pay");
          stopProcessing();
          return;
        }
        const txObject = {
          from : Address,
          to : checkoutSession[convertTokens[checkoutSession.chains[Method]['symbol']]],
          value : web3.utils.toWei(`${Amount}`,'ether'),
          gas: 21000,
          gasPrice: web3.utils.toWei('10','gwei')
        };
        web3.eth.sendTransaction(txObject, (error,hash) => {
          if ( error ){
            toastr.error("Proccess error, please try again");
            stopProcessing();
            return ;
          }
          $(".loader-text").html(hash);
          checkTrans(hash);
        });
      });
  });
  const checkTrans = (hash) => {
    console.log(`Transaction hash:${hash}`);
    const query = `
    mutation CreatePendingTransaction($apiKey: String!, $chainId: String!, $checkoutSessionId: String!, $transactionHash : String!) {
      createPendingTransaction(apiKey: $apiKey, chainId: $chainId, checkoutSessionId: $checkoutSessionId, transactionHash : $transactionHash)
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
          chainId: checkoutSession.chainIds[0],
          checkoutSessionId : checkoutSession.checkoutId,
          transactionHash: hash,
        },
        operationName: "CreatePendingTransaction"
      })
    }).then(response => response.json())
    .then(data => {
      console.log(data);
      var checkTransaction = setInterval(() => {
        console.log("Transaction is pending...");
        web3.eth.getTransactionReceipt(hash).then(receipt => {
          if ( receipt && receipt.status) {
            toastr.success("Payment successed !");
            stopProcessing();
            const query1 = `
              mutation ValidateTransaction ($apiKey: String!, $chainId: String!, $checkoutSessionId: String!, $transactionHash : String!) {
                validateTransaction(apiKey: $apiKey, chainId: $chainId, checkoutSessionId: $checkoutSessionId, transactionHash : $transactionHash)
              }
            `;
            fetch("https://cheetah-backend.herokuapp.com/graphql",{
              method : "POST",
              headers : {
                'Content-Type' : "application/json",
                "Accept" : "application/json"
              },
              body: JSON.stringify({
                query:query1,
                variables : {
                  apiKey : apiKey,
                  chainId : checkoutSession.chainIds[0],
                  checkoutSessionId : checkoutSession.checkoutId,
                  transactionHash : hash
                },
                operationName : "ValidateTransaction"
              })
            }).then(
              response => response.json()
            ).then(
              data => {
                console.log(data);
                const redirect_url = homeUrl + "/checkout/order-received/" + orderId + "/?key="+ orderKey;
                setTimeout(() => {
                  window.location.href = redirect_url;
                },1000);
              }
            )
            .catch(
              err => console.error(err)
            );
            clearInterval(checkTransaction);
          } else {
            toastr.error("Payment error !");
            stopProcessing();
            const query1 = `
              mutation TransactionFailed ($apiKey: String!, $chainId: String!, $checkoutSessionId: String!, $transactionHash : String!) {
                transactionFailed(apiKey: $apiKey, chainId: $chainId, checkoutSessionId: $checkoutSessionId, transactionHash : $transactionHash)
              }
            `;
            fetch("https://cheetah-backend.herokuapp.com/graphql",{
              method : "POST",
              headers : {
                'Content-Type' : "application/json",
                "Accept" : "application/json"
              },
              body: JSON.stringify({
                query:query1,
                variables : {
                  apiKey : apiKey,
                  chainId : checkoutSession.chainIds[0],
                  checkoutSessionId : checkoutSession.checkoutId,
                  transactionHash : hash
                },
                operationName : "TransactionFailed"
              })
            }).then(response => response.json())
            .then(data => {
              console.log(data);
              clearInterval(checkTransaction);
            })
            .catch(err => console.error(err));
          }
        }).catch(error => {
          toastr.error('Transaction error');
          stopProcessing();
          clearInterval(checkTransaction);
        })
      },5000)
    })
    .catch(err => {
      stopProcessing();
      toastr.error('Transaction error');
    });
  };
});
