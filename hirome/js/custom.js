$(document).ready(function() {

  // declare variable
  var scrollTop = $(".scrollTop");

  $(window).scroll(function() {
    // declare variable
    var topPos = $(this).scrollTop();
    // if user scrolls down - show scroll to top button
    if (topPos > 100) {
      $(scrollTop).css("opacity", "1");

    } else {
      $(scrollTop).css("opacity", "0");
    }

  });
  $(scrollTop).click(function() {
    $('html, body').animate({
      scrollTop: 0
    }, 800);
    return false;

  });
});

//Update Header Style and Scroll to Top
/**
 * add the nicescroll plugin
 * @param  {Element} html document
 */
//$("html").niceScroll({
//    cursorcolor: 'rgba(152, 166, 173, 0.5)',
//    cursorwidth: '6px',
//    cursorborderradius: '5px'
//});


// fixed nav bar in jquery
$(window).scroll(function () {
  if ($(this).scrollTop() > 200) {
    $('.nav_custom').addClass('fixed');
  } else {
    $('.nav_custom').removeClass('fixed');
  };
});



$(document).ready(function () {
  // Add smooth scrolling to all links
  $("#footer_link").on('click', function (event) {

    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
      // Prevent default anchor click behavior
      event.preventDefault();

      // Store hash
      var hash = this.hash;

      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
      $('html, body').animate({
        scrollTop: $(hash).offset().top
      }, 800, function () {

        // Add hash (#) to URL when done scrolling (default click behavior)
        // window.location.hash = hash;
      });
    } // End if
  });
});