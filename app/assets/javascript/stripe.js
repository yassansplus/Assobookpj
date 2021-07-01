document.querySelectorAll('.price').forEach((item) => {
    item.addEventListener('click',function(){
        document.getElementById("cagnotte_montant").value = '';
        let montant = 0;
        if(montant !== "other"){
            montant = this.dataset.montant;
        }else{
            montant = document.getElementById("cagnotte_montant").value;
        }
        document.getElementById("cagnotte_montant").value = montant;
        document.getElementById("cagnotte_montant").setAttribute("value",montant);
    })
})

document.getElementById("cagnotte_submit").addEventListener('click',function(e){
    e.preventDefault();
    const errorMsg = document.querySelector('.errorMsg');
    const divChoose = document.querySelector('.choose-montant');
    errorMsg !== null ? errorMsg.remove() : '';
    const montantFinal = document.getElementById("cagnotte_montant");
    if(montantFinal.value === ''){
        createError("Vous devez entrer un montant");
    }else if(Number(montantFinal.value) === 0){
        createError("Un don ne peut pas valoir 0 €");
    }else{
        divChoose.classList.add('d-none');
        document.getElementById("cagnotte_montant").setAttribute("value",montantFinal.value);
        divChoose.parentNode.innerHTML += stripeForm(Number(montantFinal.value));
        stripe(montantFinal.value,document.getElementsByName("name-assoc")[0].value);
    }
})

const createError = (message) => {
    const p = document.createElement('p');
    const form = document.querySelector('form[name=cagnotte]');
    p.style.color = "red";
    p.style.fontSize = "13px";
    p.textContent = message;
    p.setAttribute('class','errorMsg');
    form.parentNode.insertBefore(p,form);

}
const stripeForm = function (som) {
    return `
    <form id="payment-form" class="mt-4" style="padding: 0 230px;">
        <div class="shadow" style="border-bottom-left-radius:10px;border-bottom-right-radius:10px;margin-bottom:20px;">
            <div class="headerRecap text-uppercase">Paiement sécurisé</div>
            <div class="container" style="padding:10px">
                <label for="card-element" style="font-size:13px;margin-top:10px;">Détail de la carte</label>
                <div class="card-div" id="card-number-element"></div>
                <div class="d-flex">
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

const stripe = function (price, assoc) {
    var stripe = Stripe("pk_test_51J6zl2BYvgiERdxUFiZ9CYHE6yrGhnmvHxMaApj9S6WycqFpU7iyr9xir4rWxi22dYZj1l3fk0cQ5YjbiVY9k18Z00ekOeegMT");
    const purchase = {
        items: [{
            price: price,
            name: `Association : ${assoc}`
        }]
    };

    fetch("/stripe", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": 'application/json'
        },
        body: JSON.stringify(purchase)
    }).then(function (result) {
        return result.json();
    }).then(function (data) {
        var elements = stripe.elements();
        var style = {
            base: {
                color: "#32325d",
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                    color: "#cfcfcf",
                }},
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
                    orderComplete(result.paymentIntent.id);
                }
            });
    };

    /* ------- UI helpers ------- */
    // Shows a success message when the payment is complete
    var orderComplete = function (paymentIntentId) {
        loading(false);
        document.querySelector(".result-message").classList.remove("hidden");
        document.querySelector("button").disabled = true;
        document.getElementsByName("cagnotte")[0].submit();
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