$(document).ready(function() {
    //Vérification des jours à plus de 1000 réservations
    $('#booking').on("mousedown", ".date", function () {
        datePicker('{{ disableDate|json_encode() }}');
    });
    $('#booking').on("change", ".date", function () {
        var birthDate = this.id,
            birth = $('#' + birthDate),
            idParent = $(this).parent().parent().attr('id');
        $.ajax({
            url: 'jquery-price',
            method: 'GET',
            data: "date=" + birth.val(),
            success: function (data) {
                if ($('#' + idParent + ' .tarif').length) {
                    $('#' + idParent + ' .tarif').remove();
                }
                $('#' + idParent + ' .checkbox').after("<p class='tarif'>Tarif: " + data.data + '€</p>');
            }
        })
    });
    $('#booking').on("change", ".datepickerBooking", function () {
        var registration_date = $('#booking_registrationDate');
        var halfDay = $('.radio').eq(0);
        $.ajax({
            url: 'jquery-ticket',
            method: 'GET',
            data: "date=" + registration_date.val(),
            success: function (data) {
                if (data['data'] === true) {
                    halfDay.hide();
                }else{
                    halfDay.show();
                }
            }
        });
    });
})