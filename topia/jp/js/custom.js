$(document).ready(function(){
window.onscroll = function() {stickyFunction()};

var navbar = document.getElementById("navbar");
var sticky = navbar.offsetTop;

function stickyFunction() {
  if (window.pageYOffset > sticky) {
    navbar.classList.add("sticky")
  } else {
    navbar.classList.remove("sticky");
  }
}



})

function myFunction(x) {
  x.classList.toggle("change");
  //conole.log("om");
}

var click = 0;
function openNav() {
if (click == 0) {
  document.getElementById("mySidenav").style.right = "0";
  click = 1;
  } else {
  document.getElementById("mySidenav").style.right = "-400px";
	click = 0;
  }
}


