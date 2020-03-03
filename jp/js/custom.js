$(document).ready(function(){
		$("#hr1").css("width", "10%");
		$("#hr2").stop().animate({width: "10%"}, 6000);
		var timer = null;
		startSetInterval();
	var lastSection =1;
	//localStorage.setItem("lastSection", "0");
	$(".leftSection").mouseenter(function(){
	//	//console.log("in2");
	$('.slider3-img').css("width", "33.333vw");
	$('.slider2-img').css("width", "33.333vw");
		 clearInterval(timer);
		 if(lastSection !=1){
		 $('.slider1-img').css("width", "0vw");
		$('.slider1-img').css("height", "100vh");
		 }
		 $('.img_bg_3').removeClass("active");
		 $('.img_bg_2').removeClass("active");
		 $('.img_bg_1').addClass("active");
		  //console.log(lastSection);
		 if(lastSection ==2){
			$('.img_bg_2').css('z-index', 2);
			$('.img_bg_3').css('z-index', 1);
		 }else if(lastSection ==3){
			$('.img_bg_2').css('z-index', 1);
			$('.img_bg_3').css('z-index', 2); 
		 }
		 $('.img_bg_1').css('z-index', 3);
		 
		 $("#subHeading2").stop().slideUp();
		 $("#subHeading2").css('opacity', 1);
		$("#subHeading3").stop().slideUp();
		$("#subHeading3").css('opacity', 1);
		$("#subHeading1").stop().fadeIn("slow");
		
		//$("#headerChange").text("MAKE IT");
		 $("#headerChange2").stop().slideUp();
		$("#headerChange3").stop().slideUp();
		$("#headerChange2").css('opacity', 1);
		$("#headerChange3").css('opacity', 1);
		$("#headerChange1").stop().fadeIn("slow");
		// $('#headerChange').animate({'opacity': 0}, 400, function(){
			// $(this).text('MAKE IT').animate({'opacity': 1}, 400);    
		// });
		
		$("#hr2").css("width", "0");
		$("#hr3").css("width", "0");
		 $("#hr1").stop().animate({width: "10%"}, 500);
		//$("#hr2").stop().animate({width: "10%"}, 6000);
		if(lastSection !=1){
		$('.active .slider-img').css("background-position-x", "-10px");
		$('.active .slider-img').stop().animate({
			'background-position-x': '0px',
			 width: '33.333vw'
		  },200);
		}
		  lastSection =1;
		 // localStorage.setItem("lastSection", "1");
	}).mouseout(function(){
		////console.log("leftSection_out");
		//$('.slider-img').css("height", "100vh");
		 $('slider-img').css("width", "33.333vw");
		clearInterval(timer);
		timer = null;
		startSetInterval();
		
	})
	
	$(".middleSection").mouseenter(function(){
		////console.log("in");
		$('.slider3-img').css("width", "33.333vw");
		$('.slider1-img').css("width", "33.333vw");
		 clearInterval(timer);
		 if(lastSection !=2){
			$('.slider2-img').css("width", "0vw");
			$('.slider2-img').css("height", "100vh");
		 }
		 $('.img_bg_1').removeClass("active");
		 $('.img_bg_3').removeClass("active");
		 $('.img_bg_2').addClass("active");
		 //console.log(lastSection);
		  if(lastSection ==1){
			$('.img_bg_1').css('z-index', 2);
			$('.img_bg_3').css('z-index', 1);
		 }else if(lastSection ==3){
			$('.img_bg_1').css('z-index', 1);
			$('.img_bg_3').css('z-index', 2); 
		 }
		 $('.img_bg_2').css('z-index', 3);
		
		 $("#subHeading1").stop().slideUp();
		$("#subHeading3").stop().slideUp();
		$("#subHeading1").css('opacity', 1);
		$("#subHeading3").css('opacity', 1);
		$("#subHeading2").stop().fadeIn("slow");
		
		//$("#headerChange").text("HOW TO");
		 $("#headerChange1").stop().slideUp();
		$("#headerChange3").stop().slideUp();
		$("#headerChange1").css('opacity', 1);
		$("#headerChange3").css('opacity', 1);
		$("#headerChange2").stop().fadeIn("slow");
		// $('#headerChange').animate({'opacity': 0}, 400, function(){
			// $(this).text('HOW TO').animate({'opacity': 1}, 400);    
		// });
		$("#hr1").css("width", "0");
		$("#hr3").css("width", "0");
		$("#hr2").stop().animate({width: "10%"}, 500);
		if(lastSection !=2){
		$('.active .slider-img').css("background-position-x", "-10px");
		$('.active .slider-img').stop().animate({
			'background-position-x': '0px',
			 width: '33.333vw'
		  },200);
		}
		  lastSection =2;
		  //localStorage.setItem("lastSection", "2");
	}).mouseout(function(){
		////console.log("middleSection_out");
		// $('.active .slider-img').css("width", "0vw");
		//$('.slider-img').css("height", "100vh");
		$('.slider2-img').css("width", "33.333vw");
		clearInterval(timer);
		timer = null;
		startSetInterval();
	})
	
	$(".rightSection").mouseenter(function(){
		////console.log("in");
	$('.slider1-img').css("width", "33.333vw");
	$('.slider2-img').css("width", "33.333vw");
		clearInterval(timer);
		if(lastSection !=3){
		 $('.slider3-img').css("width", "0vw");
			$('.slider3-img').css("height", "100vh");
		}
		 $('.img_bg_1').removeClass("active");
		 $('.img_bg_2').removeClass("active");
		 $('.img_bg_3').addClass("active");
		 //console.log(lastSection);
		if(lastSection ==2){
			$('.img_bg_2').css('z-index', 2);
			$('.img_bg_1').css('z-index', 1);
		 }else if(lastSection ==1){
			$('.img_bg_1').css('z-index', 2);
			$('.img_bg_2').css('z-index', 1); 
		 }
		 $('.img_bg_3').css('z-index', 3);
		 $("#subHeading1").stop().slideUp();
		$("#subHeading2").stop().slideUp();
		$("#subHeading1").css('opacity', 1);
		$("#subHeading2").css('opacity', 1);
		$("#subHeading3").stop().fadeIn("slow");
		
		//$("#headerChange").text("RESULTS");
		$("#headerChange1").stop().slideUp();
		$("#headerChange2").stop().slideUp();
		$("#headerChange1").css('opacity', 1);
		$("#headerChange2").css('opacity', 1);
		$("#headerChange3").stop().fadeIn("slow");
		// $('#headerChange').animate({'opacity': 0}, 300, function(){
			// $(this).text('RESULTS').animate({'opacity': 1}, 300);    
		// });
		$("#hr2").css("width", "0");
		$("#hr3").stop().animate({width: "10%"}, 500);
		$("#hr1").css("width", "0");
		if(lastSection !=3){
		$('.active .slider-img').css("background-position-x", "-10px");
		$('.active .slider-img').stop().animate({
			'background-position-x': '0px',
			 width: '33.333vw'
		  },200);
		}
		   lastSection =3;
		  // localStorage.setItem("lastSection", "3");
	}).mouseout(function(){
		////console.log("rightSection_out");
		// $('.active .slider-img').css("width", "0vw");
		//$('.slider-img').css("height", "100vh");
		$('.slider3-img').css("width", "33.333vw");
		clearInterval(timer);
		timer = null;
		startSetInterval();
	})
	// start function on page load

function startSetInterval() {
   timer = setInterval(function(){
		
     if($('.active').data('slider')==1){
				////console.log($('.active').data('slider'));
				$('.img_bg_1').removeClass("active");
				 $('.img_bg_2').addClass("active");
				 $('.img_bg_3').css('z-index', 1);
				 $('.img_bg_1').css('z-index', 2);
				 $('.img_bg_2').css('z-index', 3);
				
				
				 $("#subHeading1").stop().slideUp();
				 $("#subHeading2").stop().fadeIn("slow");
				 
				  $("#hr1").stop().animate({width: "0"}, 2000);
				  $("#hr3").stop().animate({width: "10%"}, 6000);
				  
				  //$("#headerChange").stop().slideUp();
				 // $("#headerChange").text('HOW TO');
				 //$("#headerChange").stop().fadeIn("slow");
				 
				 $("#headerChange1").stop().slideUp();
				 $("#headerChange2").stop().fadeIn("slow");
				 lastSection =2;
				 //localStorage.setItem("lastSection", "2");
				 //console.log(lastSection);
				  // $('#headerChange').animate({'opacity': 0}, 300, function(){
						// $(this).text('HOW TO').animate({'opacity': 1}, 300);    
					// });
				 
				// $('#hr1').css('width', 0);
				// $('#hr1').stop().animate({ width: '100%' },5000);
				// $('#hr2').removeClass('grow');
				// $('#hr1').addClass('grow');
				
			}else if($('.active').data('slider')==2){
				////console.log($('.active').data('slider'));
				 $('.img_bg_2').removeClass("active");
				 $('.img_bg_3').addClass("active");
				 $('.img_bg_1').css('z-index', 1);
				 $('.img_bg_2').css('z-index', 2);
				 $('.img_bg_3').css('z-index', 3);
				  $("#subHeading2").stop().slideUp();
				  $("#subHeading3").stop().fadeIn("slow");
				  $("#hr2").stop().animate({width: "0"}, 2000);
				$("#hr1").stop().animate({width: "10%"}, 6000);
				//$("#headerChange").text('RESULTS');
				 $("#headerChange2").stop().slideUp();
				  $("#headerChange3").stop().fadeIn("slow");
				  lastSection =3;
				  //localStorage.setItem("lastSection", "3");
				  //console.log(lastSection);
				// $('#headerChange').animate({'opacity': 0}, 300, function(){
					// $(this).text('RESULTS').animate({'opacity': 1}, 300);    
				// });
				// $('#hr2').css('width', 0);
				 //$('#hr1').removeClass('grow');
				// $('#hr2').addClass('grow');
				// $('#hr2').stop().animate({ width: '100px' },5000);
			}else if($('.active').data('slider')==3){
				////console.log($('.active').data('slider'));
				 $('.img_bg_3').removeClass("active");
				 $('.img_bg_1').addClass("active");
				 $('.img_bg_1').css('z-index', 3);
				 $('.img_bg_2').css('z-index', 1);
				 $('.img_bg_3').css('z-index', 2);
				 $("#subHeading3").stop().slideUp();
				 $("#subHeading1").stop().fadeIn("slow");
				 $("#hr3").stop().animate({width: "0"}, 2000);
				$("#hr2").stop().animate({width: "10%"}, 6000);
				//$("#headerChange").text('MAKE IT');
				$("#headerChange3").stop().slideUp();
				 $("#headerChange1").stop().fadeIn("slow");
				 lastSection =1;
				 //localStorage.setItem("lastSection", "1");
				 //console.log(lastSection);
				// $('#headerChange').animate({'opacity': 0}, 300, function(){
					// $(this).text('MAKE IT').animate({'opacity': 1}, 300);    
				// });
				// $('#hr2').css('width', 0);
				 //$('#hr1').removeClass('grow');
				 //$('#hr2').addClass('grow');
				// $('#hr2').stop().animate({ width: '100px' },5000);
			}
 // }, 900);
		//$('.active .slider-img').css("opacity", "0");
		//$('.active .slider-img').css("background-position", "left 0 top 0");
		$('.active .slider-img').css("width", "0vw");
		$('.active .slider-img').css("height", "100vh");
		
		if($( window ).width()>767){
			$('.active .slider-img').css("background-position-x", "-50px");
			$('.active .slider-img').animate({
				//opacity: '1',
				 'background-position-x': '0px',
				 width: '33.333vw'
			  },200, function() {
				//$('.active .slider-img').css("background-position", "center center");
				//$(this).hide();
		   });
	   }else{
		   //$('.active .slider-img').css("background-position-x", "-50px");
		   $('.active .slider-img').animate({
				//opacity: '1',
				// 'background-position-x': '0px',
				 width: '33.333vw'
			  },200, function() {
				//$('.active .slider-img').css("background-position", "center center");
				//$(this).hide();
			});
	   }
		 		  
	}, 6000);
}
})



