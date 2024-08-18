jQuery(document).ready(function($) {
    // Open WhatsApp with contact number
    $('.custom-floating-button.whatsapp').click(function(e) {
        e.preventDefault();
        var number = $(this).data('number');
        window.open('https://wa.me/' + number, '_blank');
    });
});