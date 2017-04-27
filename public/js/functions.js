//inicializuju magnific popup - obrazky a galerie, lightbox - inicializace galerie
$(document).ready(function () {
    $('.popup-gallery').magnificPopup({
        delegate: '.magnific',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
                return item.el.attr('title');
            }
        }
    });
});

/*
 * inicializace magnific-popup - zobrazeni obrazku
 */
$(document).ready(function () {

    $('.magnific-popup').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }

    });

});


/*
 * increase/decrease peaces in cart
 */
$(document).ready(function () {

    $(".cart .cart-item-edit").on('click', function () {
        $('.cart-item-edit').attr("disabled", true);
        $('.cart .cart-item .remove').attr("disabled", true);
        

        var product = $(this).data('product');
        var price = $(this).data('price');
        var set = $(this).data('set');

        $.ajax({
            type: "POST",
            url: "/default/ajax/editcartitem",
            data: {
                product: product,
                price: price,
                set: set
            },
            success: function (result) {

                document.location.reload(true);
            },
            error: function () {
                alert("Někde nastala chyba...");
                $('.cart-item-edit').attr("disabled", false);
                $('.cart .cart-item .remove').attr("disabled", false);

            }

        });
        return false;
    });
});




/*
 * info modal - select variant first
 */
$(document).ready(function () {

    $(".add-to-cart.select-variant").on('click', function () {
        $('.add-to-cart').attr("disabled", true);
        $('#variant-error-modal').modal();
        $('.add-to-cart').attr("disabled", false);

    });
});





/*
 * vlozeni do kosiku
 */
$(document).ready(function () {

    $(".add-to-cart-form").submit(function () {
        $('.add-to-cart').attr("disabled", true);

        $.ajax({
            type: "POST",
            url: "/default/ajax/addtocart",
            data: $(this).serialize(),
            dataType: "json",
            success: function (result) {

                $(".navbar-cart-text span.count").html(result.cart.count);

                $(".navbar-cart-text span.amount").html(result.cart.amount);

                if ($(".navbar-cart .cart.cart-full").hasClass('is-empty-cart')) {
                    $(".navbar-cart .cart.cart-full").removeClass('is-empty-cart');
                    $(".navbar-cart .cart.cart-empty").addClass('is-empty-cart');
                }

                $('#cart-modal-wrap').html(result.view);
                $('#cart-modal').modal();
                $('.add-to-cart').attr("disabled", false);

            },
            error: function (result) {
                alert(result.responseText + "Někde nastala chyba...");
                $('.add-to-cart').attr("disabled", false);
            }

        });
        return false;
    });
});



/*
 * vlozeni do kosiku
 */
$(document).ready(function () {

    $("#order-form #order_is_d_address").on('change', function () {
        $("#order-form .delivery-address").toggleClass('hide-fields')


    });

});




function setDelivery(delivery) {
    
    var payments = dataDeliveryPayment.delivery[delivery].payments;
    var deliveryPriceToDisplay = dataDeliveryPayment.delivery[delivery].price_to_display;
        
    $('.recapitulation .item .delivery-price').html(deliveryPriceToDisplay);

    $(".payment-item input[name=payment]").each(function () {
        var val = parseInt($(this).val());
        if ($.inArray(val, payments) != -1) {
            $(this).attr('disabled', false);
            $(this).parent().removeClass('disabled');
        } else {
            $(this).attr('disabled', true);
            $(this).attr('checked', false);
            $(this).parent().addClass('disabled');
            $('.recapitulation .item .payment-price').html('');
        }
    });

}

function setPayment(payment) {
    
    var paymentPriceToDisplay = dataDeliveryPayment.payment[payment].price_to_display;

    $('.recapitulation .item .payment-price').html(paymentPriceToDisplay);

}


$(document).ready(function () {

    var checkedDelivery = $(".delivery-item input[name='delivery']:checked").val();

    if (checkedDelivery) {
        setDelivery(checkedDelivery);
    }


    var checkedPayment = $(".payment-item input[name='payment']:checked").val();

    if (checkedPayment) {
        setPayment(checkedPayment);
    }

    recalcCartAmount();

});



/*
 * disable a enable - delivery/paymant combination
 */
$(document).ready(function () {

    //pri zmene dopravy zjistuju dostupne platby
    $(".delivery-item input[name='delivery']").on('change', function () {
        var delivery = $(this).val();
        $(this).attr('checked', 'checked');
        
        setDelivery(delivery)
        
        recalcCartAmount();

        
    });

    //pri zmene platby zjistuju dostupne dopravy
    $(".payment-item input[name='payment']").on('change', function () {

        var payment = $(this).val();
        
        setPayment(payment);

        recalcCartAmount();

    });

});

// recalc order summary amoount
function recalcCartAmount() {
    
    var amount = $('.recapitulation .item .amount span').data('amount');
    var deliveryPrice = 0;
    var paymentPrice = 0;
    
    var checkedDelivery = $(".delivery-item input[name='delivery']:checked").val();
    if (checkedDelivery) {
        deliveryPrice = dataDeliveryPayment.delivery[checkedDelivery].price;
    }
    
    var checkedPayment = $(".payment-item input[name='payment']:checked").val();
    if (checkedPayment) {
        paymentPrice = dataDeliveryPayment.payment[checkedPayment].price;
    }
    
    if (amount <= 0) {
        amount = 0;
    }

    if (deliveryPrice <= 0) {
        deliveryPrice = 0;
    }

    if (paymentPrice <= 0) {
        paymentPrice = 0;
    }

    var amountResult = parseFloat(amount) + parseFloat(deliveryPrice) + parseFloat(paymentPrice);

    $.ajax({
        type: "POST",
        url: "/default/ajax/getprice",
        data: {
            price: amountResult
        },
        dataType: "json",
        success: function (result) {
            $('.recapitulation .item .amount span').text(result.amount);
        }
    });

}




//function recalcCartAmount() {
//    var amount = $('.recapitulation .item .amount span').data('amount');
//    var deliveryPrice = $(".recapitulation .item .delivery-price").data('price');
//    var paymentPrice = $(".recapitulation .item .payment-price").data('price');
//
//    if (amount <= 0) {
//        amount = 0;
//    }
//
//    if (deliveryPrice <= 0) {
//        deliveryPrice = 0;
//    }
//
//    if (paymentPrice <= 0) {
//        paymentPrice = 0;
//    }
//
//    var amountResult = parseInt(amount) + parseInt(deliveryPrice) + parseInt(paymentPrice);
//
//    $.ajax({
//        type: "POST",
//        url: "/default/ajax/getprice",
//        data: {
//            price: amountResult
//        },
//        dataType: "json",
//        success: function (result) {
//            $('.recapitulation .item .amount span').text(result.amount);
//        }
//    });
//
//}



//ochrana emailu proti spamu
$(document).ready(function () {
    $(".email-address").text($(".email-address").data('email') + "@" + $(".email-address").data('domain'));

    //specialni pripad pro ckeditor
    $("span.info-yagga-email-address").text("info" + "@" + "yagga.cz");

});


//pri obrazovce mensi, nez 1040 uprava menu
$(document).ready(function () {

    if ($(window).width() <= 1040) {
        $('.dropdown').has('div.dropdown-menu').find('a.dropdown-toggle').removeClass("disabled");
    }

});


//display filtru
$(document).ready(function () {

    var isIcoActive = false;

    $('.filter-item-button').on('click', function () {

        //kontrola, jestli je nekde aktivni ikona(resp. filter) a kliknu na jiny filter button, u puvodniho musim nastavit default ikonu a vyresetuju isIcoActive na false
        if (($(this).next(".filter-wrap").css('display') == 'none') && (isIcoActive == true)) {
            $('.filter-item-button').removeClass('filter-on');
            isIcoActive = false;
        }

        $('.filter-item-button').not(this).next('.filter-wrap').slideUp();
        $(this).next(".filter-wrap").toggle();


        if (isIcoActive == false) {
            $(this).addClass('filter-on');
            isIcoActive = true;
        } else {
            $(this).removeClass('filter-on');
            isIcoActive = false;
        }

    });
});




/*
 * Skryti a zobrazenia variant po najeti
 */
$(document).ready(function () {

    $('.product-item').hover(
        function () {

            $(this).find('ul.variants-list').stop().fadeIn(100);

        },
        function () { // Mouse out
            $(this).find('ul.variants-list').stop().fadeOut(200);
        }
        );
});






/*
 * BLBINKA - santa pri najeti na add to cart
 */


$(document).ready(function () {

    $('.add-to-cart').hover(
        function () {

            $('.santa-joke-wrap').stop().fadeIn(100);

        },
        function () { // Mouse out
            $('.santa-joke-wrap').stop().fadeOut(200);
        }
        );
});