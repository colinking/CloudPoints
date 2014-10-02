//this forces a reload of the page if the page is loaded from
//the bfcache. If it was loaded from this then it would not
//make the JS calls below.
//apparently just running css/fadein code doesn't work
//window.onpageshow = function(event) {
//    if (event.persisted) {
//        window.location.reload();
//    }
//};
//$(document).ready(function() {
//    $('#innerjumbo').css('display', 'none');
//    $('#innerjumbo').fadeIn(1000);
//    $('.redirect').click(function(event) {
//        event.preventDefault();
//        newLocation = this.href;
//        $('#innerjumbo').animate({opacity: 0}, 300, newpage);
//    });
//    function newpage() {
//        window.location = newLocation;
//    }
//});
