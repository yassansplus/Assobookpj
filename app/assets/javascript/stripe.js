const stripeForm = function (som) {
    return `
    <form id="payment-form">
        <div class="shadow" style="border-bottom-left-radius:10px;border-bottom-right-radius:10px;margin-bottom:20px;">
            <div class="headerRecap text-uppercase">Paiement sécurisé</div>
            <div class="container" style="padding:10px">
                <label for="card-element" style="font-size:13px;margin-top:10px;">Détail de la carte</label>
                <div class="card-div" id="card-number-element"></div>
                <div class="flex">
                    <div class="card-div" id="card-expiry-element"></div>
                    <div class="card-div" id="card-cvc-element"></div>
                </div>
                <label for="client" style="font-size:13px;margin-top:10px;">Nom du titulaire de la carte</label>
                <input type="text" name="client" placeholder="Mme DUBREUIL Jeanne" required>
                <button id="submit" class="myData" data-btn="1">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="button-text">Payer ${som.toFixed(2) + '€'}</span>
                </button>
                                
                <p id="card-error" role="alert"></p>
                <p class="result-message hidden">
                    Paiement réussi
                </p>
            </div>
        </div>
    </form>
`;
}

const stripe = function (price, url, assoc) {
    var stripe = Stripe("pk_test_51Hviy9GhKoVppERQKeknz9yXG48II3jLKvgL8DqxG7Rup3IjKxy0YrLBNkjEho41qAWMNPFrtvdqfKd1nLgMhRyj00nEbZ1M6I");
    const purchase = {
        items: [{
            price: price,
            name: assoc
        }]
    };

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": 'application/json'
        },
        body: JSON.stringify(purchase)
    }).then(function (result) {
        return result.json();
    })
        .then(function (data) {
            var elements = stripe.elements();
            var style = {
                base: {
                    color: "#32325d",
                    fontFamily: 'Arial, sans-serif',
                    fontSmoothing: "antialiased",
                    fontSize: "16px",
                    "::placeholder": {
                        color: "#cfcfcf",
                    }
                },
                invalid: {
                    fontFamily: 'Arial, sans-serif',
                    color: "#fa755a",
                    iconColor: "#fa755a"
                }
            };
            var cardNumber = elements.create("cardNumber", {
                style: style,
                showIcon: true,
            });

            var cardExpiry = elements.create("cardExpiry", {
                style: style
            });

            var cardCvc = elements.create("cardCvc", {
                style: style,
            });

            // Stripe injects an iframe into the DOM
            cardNumber.mount("#card-number-element");
            cardExpiry.mount("#card-expiry-element");
            cardCvc.mount("#card-cvc-element");

            cardNumber.on("change", function (event) {
                // Disable the Pay button if there are no card details in the Element
                document.querySelector("button").disabled = event.empty;
                document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
            });

            cardExpiry.on("change", function (event) {
                // Disable the Pay button if there are no card details in the Element
                document.querySelector("button").disabled = event.empty;
                document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
            });

            var form = document.getElementById("payment-form");
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                // Complete payment when the submit button is clicked
                payWithCard(stripe, cardNumber, data.clientSecret);
            });
        });

    var payWithCard = function (stripe, card, clientSecret) {
        loading(true);
        stripe
            .confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: {
                        "name": $('input[name=client]').val(),
                    }
                }
            })
            .then(function (result) {
                if (result.error) {
                    // Show error to your customer
                    showError(result.error.message);
                } else if (result.paymentIntent.status === "succeeded") {
                    // The payment succeeded!
                    orderComplete(result.paymentIntent.id, multi);
                }
            });
    };

    /* ------- UI helpers ------- */
    // Shows a success message when the payment is complete
    var orderComplete = function (paymentIntentId) {
        loading(false);
        document.querySelector(".result-message").classList.remove("hidden");
        document.querySelector("button").disabled = true;
        document.querySelector(".form-stripe").submit();
    };
    // Show the customer the error from Stripe if their card fails to charge
    var showError = function (errorMsgText) {
        loading(false);
        var errorMsg = document.querySelector("#card-error");
        errorMsg.textContent = errorMsgText;
        setTimeout(function () {
            errorMsg.textContent = "";
        }, 4000);
    };
    // Show a spinner on payment submission
    var loading = function (isLoading) {
        if (isLoading) {
            // Disable the button and show a spinner
            document.querySelector("button").disabled = true;
            document.querySelector("#spinner").classList.remove("hidden");
            document.querySelector("#button-text").classList.add("hidden");
        } else {
            document.querySelector("button").disabled = false;
            document.querySelector("#spinner").classList.add("hidden");
            document.querySelector("#button-text").classList.remove("hidden");
        }
    };
}