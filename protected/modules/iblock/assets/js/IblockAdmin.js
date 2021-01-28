/**
 * Iblock admin
 */

var IblockAdmin = {
	/**
	 * Initialize
	 * @returns
	 */
	init: function() {
		var properties_box = $("#iblock-properties-table").find('tbody').first();
		var add_property_btn = $("#add-iblock-property");
        add_property_btn.on('click', function(){
			var t = $(this);
            t.attr('disabled', 'disabled');
            var next_id = properties_box.find('.new_prop').size();
            var content = properties_box.find('tr').last().html();

            content = content.replace(new RegExp('_' + (next_id-1) + '_', 'gi'), '_' + next_id + '_');
            content = content.replace(new RegExp('\\[' + (next_id-1) + '\\]', 'gi'), '[' + next_id + ']');


            properties_box.append(
                '<tr id="iblock-new-prop-row-' + next_id + '" class="new_prop row' + (next_id % 2 ? '0' : '1') + '" valign="top">' +
                content +
                '</tr>'
            );


            t.removeAttr('disabled');
            return false;
		});
	}
};