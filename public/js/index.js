$(document).ready(function() {
    //Ajout d'un ticket
    $('#add_ticket').insertBefore($('.btn-danger'))
    //Event sur l'input date de naissance
    $('#booking').on("change", ".datepickerBirthday", function () {
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
            Day = $('.radio');
        console.log($('#booking_registrationDate'));
        //console.log(halfDay);
        //Methode ajax vers le ControllerPrice
        $.ajax({
            url: 'jquery-ticket',
            method: 'GET',
            data: "date=" + registration_date.val(),
            success: function (data) {
                //Réponse du controller, si date du jour et heure passée 14H
                if (data['data'] === true) {
                    $.each(Day, function () {
                        //Si le bouton radio = journée
                        if(this['textContent'] === ' Journée'){
                            $( this ).hide();
                        }
                    })
                    //Sinon affiche bouton jour
                }else{
                    Day.each(function() {
                        if(this['textContent'] === ' Journée'){
                            $( this ).show();
                        }
                    });
                }
            }
        });
    });

    //Event sur le bouton réduction
    $('#booking').on("change", ".reduced", function () {
        //Variables
        var checkbox = $(this).parents('div')[0],
            booking = $(this).parents()[3].id,
            registration_date = $("#"+booking+'_birthDate').val(),
            reduced = $('#'+booking+'_reduced').prop('checked')
        //Methode ajax vers le ControllerPrice
        $.ajax({
            url: 'jquery-reduced',
            method: 'GET',
            data: {'date':registration_date, 'reduced':reduced},
            success: function (data) {
                //Retourne le prix en fonction de la réduction
                $("#"+booking+"_tarif .prix" ).text(data['data']);
            }
        });
        //Si le bouton est coché, affiche message
        if($('#'+booking+'_reduced').prop('checked') === true){
            $(checkbox).after("<div id="+booking+"_reduced_advice>Merci d'apporter vote justificatif (étudiant, employé du musée, d’un service du Ministère de la Culture, militaire…)</div>");
            //Sinon le cache
        }else{
            $("#"+booking+"_reduced_advice").hide();
        }

    });
})