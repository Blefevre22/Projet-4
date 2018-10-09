$(document).ready(function() {
    //Ajout d'un ticket
    $('#add_ticket').insertBefore($('.btn-danger'))
    //Event sur l'input date de naissance
    $('#booking').on("change", ".date", function () {
        //Variables
        var birthDate = this.id,
            birth = $('#' + birthDate),
            booking = $(this).parent().parent().attr('id'),
            reduced = $('#'+booking+'_reduced').prop('checked');
        //Methode ajax vers le ControllerPrice
        $.ajax({
            url: 'jquery-reduced',
            method: 'GET',
            data: {'date':birth.val(), 'reduced':reduced},
            success: function (data) {
                //Si un tarif est déja affiché, le supprimer
                if ($('#' + booking + ' .tarif').show()) {
                    $('#' + booking + '_tarif ').remove();
                }
                //Affiche le tarif
                $('#' + booking + ' .checkbox').after("<div id="+booking+"_tarif><p>Tarif: <span class='prix'>"+ data.data + '</span>€</p></div>');
            }
        })
    });
    //Event sur la date de réservation
    $('#booking').on("change", ".datepickerBooking", function () {
        //Variables
        var registration_date = $('#booking_registrationDate'),
            halfDay = $('.radio');
        //console.log(halfDay);
        //Methode ajax vers le ControllerPrice
        $.ajax({
            url: 'jquery-ticket',
            method: 'GET',
            data: "date=" + registration_date.val(),
            success: function (data) {
                //Réponse du controller, si date du jour et ap
                if (data['data'] === true) {
                    $.each(halfDay, function () {
                        if(this['textContent'] === ' Journée'){
                            $( this ).hide();
                        }else{
                            $( '#booking_customer_0_ticket_1' ).attr('checked',true);
                        }
                    })
                }else{
                    halfDay.each(function() {
                        if(this['textContent'] === ' Journée'){
                            $( this ).show();
                        }
                    });
                }
            }
        });
    });
    $('#booking').on("change", ".reduced", function () {
        var checkbox = $(this).parents('div')[0],
            booking = $(this).parents()[3].id,
            registration_date = $("#"+booking+'_birthDate').val(),
            reduced = $('#'+booking+'_reduced').prop('checked');
        $.ajax({
            url: 'jquery-reduced',
            method: 'GET',
            data: {'date':registration_date, 'reduced':reduced},
            success: function (data) {
                $("#"+booking+"_tarif .prix" ).text(data['data']);
            }
        });
        if($('#'+booking+'_reduced').prop('checked') === true){
            $(checkbox).after("<div id="+booking+"_reduced_advice>Merci d'apporter vote justificatif (étudiant, employé du musée, d’un service du Ministère de la Culture, militaire…)</div>");
        }else{
            $("#"+booking+"_reduced_advice").hide();
        }

    });
})