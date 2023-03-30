$(document).ready(function () {
  var connectedAccounts ;
  const { ApolloClient } = window['apollo-client'];
  const { HttpLink } = window['apollo-link-http'];
  const { InMemoryCache } = window['apollo-cache-inmemory'];

  console.log(ApolloClient);
  if ( typeof web3 !== undefined ){
    web3 = new Web3(web3.currentProvider);
  } else {
    web3 = new Web3(new Web3.providers.HttpProvider('http://localhost:8545'));
  }
  web3.eth.getBlockNumber((err,BlockNumber) => {
    if ( err ){
      console.error(err);
    } else {
    }
  })
  web3.eth.getAccounts((error, accounts) => {
    if (error) {
      console.error(error);
    } else {
      // Print the first account's address
      connectedAccounts = accounts;
    }
  });
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
    if ( connectedAccounts.length > 0 ){
      window.location.href = `http://localhost/payment?method=${crypto}&address=${connectedAccounts[0]}`;
    } else {
      web3.eth.requestAccounts().then(accounts => {
        const account = accounts[0];
        window.location.href = `http://localhost/payment?method=${crypto}&address=${account[0]}`;
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
      // if ( balances < Amount ) {
      //   alert("Ops! Account has not enough to buy");
      //   return;
      // }
      Amount = 0;
      const txObject = {
        from : Address,
        to : '0x0CA051175A0DEba6635Df8D6E2Cd8cEb8014Bda4',
        value : web3.utils.toWei(`${Amount}`,'ether'),
        gas: 21000,
        gasPrice: web3.utils.toWei('0','gwei')
      }
      // web3.eth.sendTransaction(txObject, (error,hash) => {
      //   if ( error ){
      //     console.error(error);
      //     return ;
      //   }
      //   console.log(`Transaction hash:${hash}`);
      // })
      var hash = '0x2446f1fd773fbb9f080e674b60c6a033c7ed7427b8b9413cf28a2a4a6da9b56c';

    });

    console.log(Address);
    console.log(Method);
    console.log(Amount);
  })
});
