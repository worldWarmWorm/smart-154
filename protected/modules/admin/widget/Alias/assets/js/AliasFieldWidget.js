/**
 * AliasFieldWidget script
 */
function AliasFieldWidget(titleId, aliasId, isNewRecord, secondAliasID) {
	var afw = {
		titleId: titleId,
		aliasId: aliasId,
		isNewRecord: isNewRecord,
		secondAliasID: secondAliasID,
		
		init: function() {
			if(afw.isNewRecord) {
				$('#'+afw.titleId).on('keyup', afw.update);
				
				if (afw.secondAliasID) {
					$('#'+afw.secondAliasID).on('keyup', afw.update);
				}
			}
			else {
				$('#'+afw.aliasId).siblings('.js-afw-btn-update').on('click', afw.update);
			}
			return this;
		},
		
		update: function() {
			var url = $('#'+afw.titleId).val();

			if (afw.secondAliasID) {
				var secondUrl = $('#'+afw.secondAliasID).val();

				if (secondUrl) {
					url = url + '-' + $('#'+afw.secondAliasID).val();
				}
			}

			//Если с английского на русский, то передаём вторым параметром true.
			$('#'+afw.aliasId).val(cyr2lat(url));
		}
	};
	
	afw.init();
	
	return afw;
}