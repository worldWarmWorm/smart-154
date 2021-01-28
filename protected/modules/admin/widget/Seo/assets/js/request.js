$(document).ready(function(){
	$(document).on("click", "#generateSitemap", function(e){
        var $btn=$(e.target), $msg=$('.generateSitemapSavedMsg');
        $btn.button('loading');
        $.post("/cp/ajax/generateMap", function( data ) {
            $btn.button('reset');
		    $msg.show().delay(1000).fadeOut(1000);
		});
	});	
});

