$(document).ready(function() {
    $('#add_ticket').insertBefore($('.btn-danger'))
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
        var halfDay = $('.radio');
        //console.log(halfDay);
        $.ajax({
            url: 'jquery-ticket',
            method: 'GET',
            data: "date=" + registration_date.val(),
            success: function (data) {
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
})