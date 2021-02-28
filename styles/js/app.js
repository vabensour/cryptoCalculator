$(document).ready(function(){
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
});

$('.goStep').click(function(){
    var beginYear = $('#beginYear').is(':checked');

    var step = $(this).data('step');
    var fileName = $(this).data('file-name') ?  $(this).data('file-name') : $('#fileName').val();
    var typeBroker = $(this).data('type-broker') ?  $(this).data('type-broker') : $('#typeBroker').val();
    var previousFileName = $(this).data('previous-file-name') ?  $(this).data('previous-file-name') : ($('#previousFileName').val() ? $('#previousFileName').val() : '');
    var previousCashIn = $(this).data('previous-cash-in') ?  $(this).data('previous-cash-in') : ($('#previousCashIn').val() ? $('#previousCashIn').val() : 0);

    var url = 'index.php?step=' + step + '&fileName=' + fileName + '&typeBroker=' + typeBroker;

    if(!beginYear) {
        url += '&previousFileName=' + previousFileName + '&previousCashIn=' + previousCashIn
    }

    document.location.href = url; 
});

$('.btn-detail').click(function(){
    var detail = $(this).data('detail');
    $('.detail-calculate[data-detail=' + detail + ']').toggle();
});

$('#beginYear').click(function(){
    if (!$(this).is(':checked')) {
        $('.container-previous-year').removeClass('d-none');
    } else {
        $('.container-previous-year').addClass('d-none');
    }
});