$("#tab1").click(function() {
    $('html, body').animate({
        scrollTop: $("body").offset().top
    }, 200);
});

$("#tab2").click(function() {
    $('html, body').animate({
        scrollTop: $("#about").offset().top
    }, 200);
});

$("#tab3").click(function() {
    $('html, body').animate({
        scrollTop: $("#what").offset().top
    }, 200);
});

$("#tab4").click(function() {
    $('html, body').animate({
        scrollTop: $("#projects").offset().top
    }, 200);
});

$("#tab5").click(function() {
    $('html, body').animate({
        scrollTop: $("#contact").offset().top
    }, 200);
});


function MainLoop() {
    
    if( $(window).scrollTop() < $("#about").position().top -50 ){
        $('#tab1').addClass('active');
        $('#tab2').removeClass('active');
        $('#tab3').removeClass('active');
        $('#tab4').removeClass('active');
        $('#tab5').removeClass('active');
    }

    if( $(window).scrollTop() > $("#about").position().top -50 && $(window).scrollTop()  <= $("#what").position().top -50) {
        $('#tab1').removeClass('active');
        $('#tab2').addClass('active');
        $('#tab3').removeClass('active');
        $('#tab4').removeClass('active')
        $('#tab5').removeClass('active');
    }

    if( $(window).scrollTop() > $("#what").position().top -50 && $(window).scrollTop()  <= $("#projects").position().top -50 ){
        $('#tab1').removeClass('active');
        $('#tab2').removeClass('active');
        $('#tab3').addClass('active');
        $('#tab4').removeClass('active')
        $('#tab5').removeClass('active');
    }
    if ( $(window).scrollTop() > $("#projects").position().top -50 &&  $(window).scrollTop() <= $("#contact").position().top - 50){
        $('#tab1').removeClass('active');
        $('#tab2').removeClass('active');
        $('#tab3').removeClass('active');
        $('#tab4').addClass('active');
        $('#tab5').removeClass('active');
    }
    if ( $(window).scrollTop() > $("#contact").position().top -50){
        $('#tab1').removeClass('active');
        $('#tab2').removeClass('active');
        $('#tab3').removeClass('active');
        $('#tab4').removeClass('active');
        $('#tab5').addClass('active')
    }
    setTimeout(MainLoop, 10);
}

function sendMail() {
    $.get('contactus.php');
}