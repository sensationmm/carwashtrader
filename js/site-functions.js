
	
	/**********************************
	/* Function to restrict listings by type
	/**********************************/
	function setFilter(target) {
		if(document.getElementById('filterForm')) {
			if(typeof target !== 'undefined') {
				document.getElementById('filterID').value = target;
			}
			document.getElementById('filterForm').submit();
		}
	}
	
	function clearFilter() {
		document.getElementById('filterClearForm').submit();
	}
	
	function deleteListing(target) {
		document.getElementById('listingID').value = target;
		document.getElementById('deleteListing').submit();
	}
	
	function showLocation(lat,lng) {
		window.open('/wp-content/themes/carwashtrader/viewLocation.php?lat='+lat+'&lng='+lng,'','width=600, height=500,location=false,toolbar=false,menubar=false,resizable=no');
	}

	function selectOrder(target) {
		document.getElementById(target).checked = true;
	}


	


$(document).ready(function() {

	$("input").keypress(function(event) {
	    if (event.which == 13) {
	        event.preventDefault();
	        /*$("form[name=piereg_loginform]").submit(); */

	        if($("form[name='loginform']")) {
	        	$("form[name='loginform']").submit();
	        }
	        setFilter();
	    }
	});
	
	$('.listingInfoReadMore').click(function() {
		if($(this).parent().hasClass('expanded')) {
			$(this).parent().removeClass('expanded');
			$(this).parent().find('.listingHidden').hide();
			$(this).parent().find('.listingShown').show();
			$(this).html('Read More');
		} else {
			$(this).parent().addClass('expanded');
			$(this).parent().find('.listingHidden').show();
			$(this).parent().find('.listingShown').hide();
			$(this).html('Read Less');
			
			var mapCanvas = $(this).parent().find('.mapThumb').attr('id');
			var mapLoc = $(this).parent().find('.mapLoc').html();
			initialize(mapLoc, mapCanvas, true);
		}
	});
	
	$('.listingInfo h3, .listing-photoInner').click(function() {
		if($(this).parent().parent().hasClass('expanded')) {
			$(this).parent().parent().removeClass('expanded');
			$(this).parent().parent().find('.listingHidden').hide();
			$(this).parent().parent().find('.listingShown').show();
			$(this).parent().parent().find('.listingInfoReadMore').html('Read More');
		} else {
			$(this).parent().parent().addClass('expanded');
			$(this).parent().parent().find('.listingHidden').show();
			$(this).parent().parent().find('.listingShown').hide();
			$(this).parent().parent().find('.listingInfoReadMore').html('Read Less');
			
			var mapCanvas = $(this).parent().parent().find('.mapThumb').attr('id');
			var mapLoc = $(this).parent().parent().find('.mapLoc').html();
			initialize(mapLoc, mapCanvas, true);
		}
	});
	
	$('.listing-photo > img').click(function() {
		if($(this).parent().parent().hasClass('expanded')) {
			$(this).parent().parent().removeClass('expanded');
			$(this).parent().parent().find('.listingHidden').hide();
			$(this).parent().parent().find('.listingShown').show();
			$(this).parent().parent().find('.listingInfoReadMore').html('Read More');
		} else {
			$(this).parent().parent().addClass('expanded');
			$(this).parent().parent().find('.listingHidden').show();
			$(this).parent().parent().find('.listingShown').hide();
			$(this).parent().parent().find('.listingInfoReadMore').html('Read Less');
		}
	});
	
	
   $('input[name=orderby]').change(function(){
        $('#filterForm').submit();
   });
   
   $('input[name=search_postcode]').on('input',function(e){
   		$('#order_postcode').css('display','inline');
   		$('#order_postcode_label').css('display','inline');
	   	var $radios = $('input:radio[name=orderby]');
	  	$radios.filter('[value=distance]').prop('checked', true); 
		document.getElementById('order_asc').checked = true;
   });

   $('.catsControl').click(function() {
   		var target = $(this).attr('rel');
   		if($('#'+target).css('display') == 'block') {
	   		$('#'+target).slideUp('fast');
	   		$(this).html('(show subcategories)');
	   	} else {
	   		$('#'+target).slideDown('fast');
	   		$(this).html('(hide subcategories)');
	   	}
   });

   $('.navToggle').click(function() {
   		$('nav.account').css('right','-600px').removeClass('open');
   		if($('nav.mobile').hasClass('open')) {
   			$('.mobileMask').fadeOut('fast');
   			$('nav.mobile').animate({'right' : '-600px'}, 200);
   			$('nav.mobile').removeClass('open');
   			$('body').css('position','relative');
   		} else {
   			$('.mobileMask').fadeIn('fast');
   			$('nav.mobile').animate({'right' : '0px'}, 200);
   			$('nav.mobile').addClass('open');
   			$('body').css('position','fixed');
   		}
   });

   $('.accountToggle').click(function() {
   		$('nav.mobile').css('right','-600px').removeClass('open');
   		if($('nav.account').hasClass('open')) {
   			$('.mobileMask').fadeOut('fast');
   			$('nav.account').animate({'right' : '-600px'}, 200);
   			$('nav.account').removeClass('open');
   			$('body').css('position','relative');
   		} else {
   			$('.mobileMask').fadeIn('fast');
   			$('nav.account').animate({'right' : '0px'}, 200);
   			$('nav.account').addClass('open');
   			$('body').css('position','fixed');
   		}
   });

	$('.mobileMask').click(function() {
   		$('body').css('position','relative');
		$('nav.mobile').css('right','-600px').removeClass('open');
   		$('nav.account').css('right','-600px').removeClass('open');
   		$('.mobileMask').fadeOut('fast');
	});



	$('.filterMobile .header').click(function() {
		if($(this).parent().find('ul').css('display') == 'none')
			$(this).parent().find('ul').slideDown('fast');
		else
			$(this).parent().find('ul').slideUp('fast');
	});
	
});