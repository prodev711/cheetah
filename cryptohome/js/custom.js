$(document).ready(function () {
  var connectedAccounts ;
  if ( typeof web3 !== undefined ){
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
      alert("please choose payment");
      return ;
    }
    if ( connectedAccounts?.length > 0 ){
      window.location.href = `http://localhost/payment?method=${crypto}&address=${connectedAccounts[0]}`;
    } else {
      web3.eth.requestAccounts().then(accounts => {
        const account = accounts[0];
        window.location.href = `http://localhost/payment?method=${crypto}&address=${account}`;
      }).catch(error => {
        console.error(error);
      })
    }
  })
  $(".proceed_pay_ether").on("click",function() {
      web3.eth.getBalance(Address,(err,balances) => {
        if ( err ){
          console.error(err);
          return ;
        }
        if ( balances < Amount ) {
          alert("Sorry! Your account has not enough to buy");
          return;
        }
        const txObject = {
          from : Address,
          to : '0x0CA051175A0DEba6635Df8D6E2Cd8cEb8014Bda4',
          value : web3.utils.toWei(`${Amount}`,'ether'),
          gas: 21000,
          gasPrice: web3.utils.toWei('10','gwei')
        }
        web3.eth.sendTransaction(txObject, (error,hash) => {
          if ( error ){
            console.error(error);
            return ;
          }
          checkTrans(hash);
        });
      });
      // checkTrans('0x2446f1fd773fbb9f080e674b60c6a033c7ed7427b8b9413cf28a2a4a6da9b56c');
  })
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
            console.log('Transaction successed !');
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
            }).then(response => response.json())
            .then(data => console.log(data))
            .catch(err => console.error(err));
            clearInterval(checkTransaction);
          } else {
            console.log('Transaction failed');
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
          console.error(error);
          clearInterval(checkTransaction);
        })
      },5000)
    })
    .catch(err => console.error(err));
    /**************************         Must Delete           ******************************* */
    // console.log(apiKey);
    // console.log(checkoutSession.chainIds[0]);
    // console.log(checkoutSession.checkoutId);
    // console.log(hash);
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
    }).then(response => response.json())
    .then(data => console.log(data))
    .catch(err => console.error(err));
  }
});
