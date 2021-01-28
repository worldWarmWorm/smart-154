$(document).ready(function(){
	
	$( ".filter-button" ).on( "click", function( event ) {
		$( "#form-filter" ).attr('data-reset', '0');	
	});

	$( ".reset-filter" ).on( "click", function( event ) {
		$( "#form-filter" ).attr('data-reset', '1')
	});

	$( "#form-filter" ).on( "submit", function( event ) {
		var reset = $( "#form-filter" ).attr('data-reset');
		event.preventDefault();
		var frm = $( this );
		var price_from = $('#price_from').val();
		var price_to = $('#price_to').val();
		
		var _data = [];
		frm.serializeArray().forEach(function(i) { if(i.name != 'category_id') _data.push(i); });
		var data = JSON.stringify(_data);
		
		var $category_id=$(frm).find("[name='category_id']")
		if($category_id.length && ($category_id.val().length > 0) && ($category_id.val() != '-')) {
			window.location.href=$category_id.val() + '?data='+data;
		}
			
		var data = {
			data : data, price_from : price_from, price_to : price_to
		};

		if(reset==1){
			data = {
				data : {}, price_from : price_from, price_to : price_to
			}
		}

		$.fn.yiiListView.update(
			'ajaxListView',
			{
				data: data,
				complete: function() {

					var url = $.fn.yiiListView.getUrl('ajaxListView');
				 	if(reset==1){
				 		url = document.location.pathname;
				 	}
				 	window.history.pushState(null, document.title, url);
				}
			}
		);

	});



});