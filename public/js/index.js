$(document).ready(function() {
    //Vérification des jours à plus de 1000 réservations
    $( '#booking' ).on( "mousedown", ".tinymce", function() {
        datePicker('{{ disableDate|json_encode() }}');
    });
    $( '#booking' ).on( "change", ".tinymce", function() {
        var birthDate = this.id,
            birth = $('#'+birthDate),
            idParent = $(this).parent().parent().attr('id');
        $.ajax({
            url: 'jquery-price',
            method: 'GET',
            data: "date="+birth.val(),
            success: function (data) {
                if($('#'+idParent+' .tarif').length){
                    $('#'+idParent+' .tarif').remove();
                }
                $('#'+idParent+' .checkbox').after("<p class='tarif'>Tarif: "+data.data+'€</p>');
            }
        })
    });
});