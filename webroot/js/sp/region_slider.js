$(function(){
	var slider = [];
	for(var i=1;i<=9;i++){
		slider[i] = $('#slider'+i).bxSlider({});
		$('#thumbs'+i+' a').click(function(){
			var thumbIndex = $(this).attr('slide');
			var sliderIndex = $(this).attr('href').replace('#thumbs','');
			if(thumbIndex && sliderIndex){
				slider[sliderIndex].goToSlide(thumbIndex-1);
				$('#thumbs'+sliderIndex+' a').removeClass('pager-active');
				$(this).addClass('pager-active');
			}
			return false;
		});
		$('#thumbs'+i+' a:first').addClass('pager-active');
	}
/*
  var slider = $('#slider1').bxSlider({
  });
  $('.pager-link').wrapInner('<span></span>');
  $('#thumbs1 a').click(function(){
   var thumbIndex = $('#thumbs1 a').index(this);
    slider.goToSlide(thumbIndex);
    $('#thumbs1 a').removeClass('pager-active');
    $(this).addClass('pager-active');
    return false;
  });
  $('#thumbs1 a:first').addClass('pager-active');
});

$(function(){
  var slider = $('#slider2').bxSlider({
  });
  $('#thumbs2 a').click(function(){
   var thumbIndex = $('#thumbs2 a').index(this);
    slider.goToSlide(thumbIndex);
    $('#thumbs2 a').removeClass('pager-active');
    $(this).addClass('pager-active');
    return false;
  });
  $('#thumbs2 a:first').addClass('pager-active');
});

$(function(){
  var slider = $('#slider3').bxSlider({
  });
  $('#thumbs3 a').click(function(){
   var thumbIndex = $('#thumbs3 a').index(this);
    slider.goToSlide(thumbIndex);
    $('#thumbs3 a').removeClass('pager-active');
    $(this).addClass('pager-active');
    return false;
  });
  $('#thumbs3 a:first').addClass('pager-active');
});

$(function(){
  var slider = $('#slider4').bxSlider({
  });
  $('#thumbs4 a').click(function(){
   var thumbIndex = $('#thumbs4 a').index(this);
    slider.goToSlide(thumbIndex);
    $('#thumbs4 a').removeClass('pager-active');
    $(this).addClass('pager-active');
    return false;
  });
  $('#thumbs4 a:first').addClass('pager-active');
});

$(function(){
  var slider = $('#slider5').bxSlider({
  });
  $('#thumbs5 a').click(function(){
   var thumbIndex = $('#thumbs5 a').index(this);
    slider.goToSlide(thumbIndex);
    $('#thumbs5 a').removeClass('pager-active');
    $(this).addClass('pager-active');
    return false;
  });
  $('#thumbs5 a:first').addClass('pager-active');
});

$(function(){
  var slider = $('#slider6').bxSlider({
  });
  $('#thumbs6 a').click(function(){
   var thumbIndex = $('#thumbs6 a').index(this);
    slider.goToSlide(thumbIndex);
    $('#thumbs6 a').removeClass('pager-active');
    $(this).addClass('pager-active');
    return false;
  });
  $('#thumbs6 a:first').addClass('pager-active');
*/
});

$(function() {
    $(".page-cont-area span").click(function(){
          if($(this).prev(".page-cont-area .text").hasClass("open")){ 
            $(this).prev(".page-cont-area .text").removeClass("open");
            $(this).html("続きを読む<img src='/rentacar/img/sp/plus.png'>");
            $(this).next(".page-cont-area span img").css("vertical-align","top");
          }else{
            $(this).prev(".page-cont-area .text").addClass("open");
            $(this).html("閉じる<img src='/rentacar/img/sp/minus.png'>");
            $(this).next(".page-cont-area span img").css("vertical-align","middle");
          }
    });
});