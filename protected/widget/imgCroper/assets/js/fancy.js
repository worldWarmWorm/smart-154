$(document).ready(function(){
	$(document).on('click', '.product-image-resize', function(){
	    var id = $(this).attr('data-id');
	    $.fancybox({
	    	autoScale: true,
    		autoSize : true,
    		centerOnScroll: true,
	    	//width: "80%",
	    	//'scrolling' : 'no',
	        'content': $('[data-edit='+id+']'),
		    helpers     : { 
		        // prevents closing when clicking OUTSIDE fancybox
		        overlay : {closeClick: false} 
		    },
		    keys : {
		        // prevents closing when press ESC button
		        close  : null
		    },
		    closeClick  : false,
			afterClose : function(){
				if($('#ajaxListView')) 
					$.fn.yiiListView.update('ajaxListView');
				else
					location.reload(); 
			}
	    });
	});
});

