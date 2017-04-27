/*
 *vlozeni nove varianty
 */
$(document).ready(function() {
    
    $("#new-variant-form-wrap").on('click', '#edit-variant-ajax',function(){
        $("#edit-variant-ajax").prop("disabled", true);
        var product_id = $("#add-variant").data("product");
        
        $.ajax({
            type: "POST",
            url: "/admin/ajax/editvariant",
            data: {
                product_id: product_id,
                data: $('#add-variant-form-ajax').serialize()
            },
            success: function(result) {
                //              pokud vraci true, validace probehla uspesne, pokud ne, generuju znovu formular
                if(result == true){
                    $('#myModal').modal('hide');
                    document.location.reload(true);
                }else{
                    $("#new-variant-form-wrap").html(result);
                    $("#edit-variant-ajax").prop("disabled", false);
                }
            },
            error: function() {
                alert("Někde nastala chyba...");
                $("#edit-variant-ajax").prop("disabled", false);
            }
        
        });
        return false;
    });
});


//upload obrazku
$(document).ready(function() {
    $("#add-photography").on("click", function(){
        if($("#file").val()){
            $("#add-photography").attr("disabled", "disabled");
            
            var product_id = $("#add-photography").data('product');
            
            $("#add-photography-form").ajaxSubmit({
                url: '/admin/ajax/imageupload',
                data:  {
                    product_id: product_id,
                    data: $('#add-photography-form').serialize()
                },
                success: function(data){
                    
                    if(data == true){
                        $('#photgraphy-Modal').modal('hide');
                        document.location.reload(true);
                    }else{
                        $("#file").val("");
                        $("#add-photography").removeAttr("disabled");
                    }
                },
                error: function(){
                    $("#file").val("");
                    $("#add-photography").removeAttr("disabled");
                
                }
            });
        
        
        }
    });
});


//inicializuju magnific popup - obrazky a galerie, lightbox - inicializace galerie
$(document).ready(function() {
    $('.popup-gallery').magnificPopup({
        delegate: '.magnific',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function(item) {
                return item.el.attr('title');
            }
        }
    });
});

/*
 * inicializace magnific-popup - zobrazeni obrazku
 */
$(document).ready(function() {
    
    $('.magnific-popup').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }
    
    });

});


//inicializuji tooltip - bootstrap framework javascript feature
$(document).ready(function(){
    $('.tooltip-init').tooltip();
});


/*
 * pridani produktu - polozky do objednavky - zobrazeni varianty pokud existuje
 */
$(document).ready(function() {
    
    $("#order-item-product").on('change',function(){
        
        var product_id = $(this).val();
        
        $.ajax({
            type: "POST",
            url: "/admin/ajax/addorderitemvariant",
            data: {
                product_id: product_id
            },
            success: function(result) {
                if(result){
                    $('#order-item-variant').append(result);
                }else{
                    $('#order-item-variant').children().remove();    
                }
            },
            error: function() {
                alert("Někde nastala chyba...");
            }
        
        });
        return false;
    });
});


//ochrana emailu proti spamu
$(document).ready(function() {
    $(".email-address").text($(".email-address").data('email') + "@" + $(".email-address").data('domain'));
});




//display filtru
$(document).ready(function() {
    
    var isIcoActive = false;
    
    $('.filter-item-button').on('click', function() {

        //kontrola, jestli je nekde aktivni ikona(resp. filter) a kliknu na jiny filter button, u puvodniho musim nastavit default ikonu a vyresetuju isIcoActive na false
        if(($(this).next(".filter-wrap").css('display') == 'none') && (isIcoActive == true)){
            $('.filter-item-button').removeClass('filter-on');
            isIcoActive = false;
        }

        $('.filter-item-button').not(this).next('.filter-wrap').slideUp();
        $(this).next(".filter-wrap").toggle();


        if(isIcoActive == false){
            $(this).addClass('filter-on');
            isIcoActive = true;
        }else{
            $(this).removeClass('filter-on');
            isIcoActive = false;
        }
    
    });
});


/*
 * zobrazovani polozek ve formulari pridani/editace parametru
 */

$(document).ready(function() {
    
    
    //overeni hodnot a nastaveni pri nacteni stranky
    if($('input[name=parameter_type][value!="2"]').is(':checked')) {
        $('#parameter-dial').css('display', 'none');
    }
    
    if($('input[name=parameter_type][value="3"]').is(':checked')) {
        $('#parameter_unit').css('display', 'none');
    }
    
    
    //zobrazeni a skryti jednotek - jednotky je mozne nastavit jen u ciselniku a hodnoty
    $('input[name=parameter_type]').on('change', function() {
        if(this.value == 3){
            $('#parameter_unit').css('display', 'none');
        }else{
            $('#parameter_unit').css('display', 'block');
        }
       
        if(this.value != 2){
            $('#parameter-dial').css('display', 'none');
        }else{
            $('#parameter-dial').css('display', 'block');
        }
       
    });
});



/*
 * pridani form elementu text pro pridani dalsi hodnoty ciselniku
 */
$(document).ready(function() {
    
    
    
    $("#add_dial_btn").on('click',function(){
    
        $("#add_dial_btn").attr('disabled', true);
    
        var valueArray = $('.parameter_dial_value').map(function() {
            return $(this).attr('data-num');
        }).get();

        var parameterDialNum = 0;
        if(valueArray.length > 0){
            parameterDialNum = Math.max.apply(Math, valueArray);

        }
        parameterDialNum = parameterDialNum + 1;

        $.ajax({
            type: "POST",
            url: "/admin/ajax/addparameterdialvalue",
            data: {
                parameterDialNum: parameterDialNum
            },
            success: function(result) {
                if(result){
                    //                    $('#parameter-dial').append(result);
                    $('#parameter-dial-items').append(result);

                    $("#add_dial_btn").attr('disabled', false);

                }
            },
            error: function() {
                alert("Někde nastala chyba...");
                $("#add_dial_btn").attr('disabled', false);
            }
        
        });
        return false;
    });
});