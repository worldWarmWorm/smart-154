/**
 * Feedback admin  
 */

var CustomerFieldsAdmin = {
	/**
	 * Initialize
	 * @param string feedbackId feedback id.
	 * @returns
	 */
	init: function() {
		var box = $("#order-fields-list");
		box.on("click", ".order-field-btn-remove", function() {
			if(confirm("Подтвердите удаление.")) {
				$.post(
					"/cp/shop/orderFieldDelete",
					{id: $(this).data('item')}, 
					function(json) {
						$("#order-field-row-" + json.id).remove();
					}, 
					"json"
				); 
			}
		});
        box.on("click", ".order-field-btn-modify", function() {
        	$(this).closest('tr').attr('style', 'border-top: 2px #808080 solid; border-left: 2px #808080 solid; border-right: 2px #808080 solid;');
        	$("#order-field-modify").remove();
			$.post(
				"/cp/shop/orderFieldModify ",
				{id: $(this).data('item')},
				function(json) {
					$("#order-field-row-" + json.id).after('<tr id="order-field-modify" style="border-bottom: 2px #808080 solid; border-left: 2px #808080 solid; border-right: 2px #808080 solid;"><td colspan="9">' + json.content + '</td></tr>');
				},
				"json"
			);
        });
	}
};