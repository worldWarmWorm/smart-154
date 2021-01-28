/**
 * Nestable widget javascript
 */
var NestableWidget = {
	init: function(id, options) {
		$("#"+id).nestable(options);
	},
	
	getSerialize: function(id) {
		return $("#"+id).nestable("serialize");
	},
	
	getNestedSet: function(id) {
		return NestableWidget.convert(NestableWidget.getSerialize(id));
	},
	
	/**
	 * Конвертация из объекта .nestable("serialize") в объекты NestedSet.
	 * @param data результат .nestable("serialize")
	 */
	convert: function(data) {
		var result=[];
		var _makeNestedSet=function(root, lft, data, level, ordering) {
			$(data).each(function(idx) {
				rgt=((this.children instanceof Array) ? _makeNestedSet(root, lft+1, this.children, level+1) : lft) + 1;
				result.push({
					id: this.id,
					root: root,
					lft: lft,
					rgt: rgt,
					level: level,
					ordering: (typeof(ordering)=='undefined') ? idx+1 : ordering
				});
				lft=rgt+1;
			});
			return rgt;
		};
		$(data).each(function(idx) {
			_makeNestedSet(this.id, 1, this, 1, idx+1); 
		});
		return result;
	},
	
};