/**
 * Nestable widget javascript
 * @param object options массив параметров. 
 * Может принимать следующие параметры:
 * "selector": выражение выборки элемента, для которого применяется nestable;
 * "saveUrl": URL сохранения nestedset структуры;
 * "options": параметры для плагина Nestable;
 * "rootAttribute": имя атрибута root, по умолчанию "root"; 
 * "leftAttribute": имя атрибута root, по умолчанию "left"; 
 * "rightAttribute": имя атрибута root, по умолчанию "right"; 
 * "levelAttribute": имя атрибута root, по умолчанию "level"; 
 * "orderingAttribute": имя атрибута root, по умолчанию "ordering"; 
 */
var common_ext_nestedset_widgets_BaseNestable = function(options) 
{
	var _this = this;
	
	/**
	 * Получить значение параметра
	 * @param string name имя параметра
	 * @param mixed def значение по умолчанию 
	 * @return mixed
	 */
	function o(name, def) {
		if(typeof(options[name]) == "undefined") {
			if(typeof(def) == "undefined") return null;
			return def;
		}
		return options[name];
	}
	
	/**
	 * Инициализация
	 */
	this.init = function() {
		$(document).ready(function() {
			// fix: при стандартных настройках плагина, ссылки внутри элементов становяться не кликабельными.
			$(o("selector")).on('mousedown', function(e) {
				if($(e.target).is('a, a span')) {
					e.stopImmediatePropagation(); 
					if(!$(e.target).hasClass("js-active-mark")) {
						$(e.target).trigger("click");
					}
					return false;
				}
			});
			if(o("saveUrl")) {
				$(o("selector")).on('change', _this.save);
			}
			$(o("selector")).nestable(o("options", {}));
		});
	};
	
	this.getSerialize = function() {
		return $(o("selector")).nestable("serialize");
	};
	
	this.getNestedSet = function() {
		return _this.convert(_this.getSerialize());
	};
	
	/**
	 * Конвертация из объекта .nestable("serialize") в объекты NestedSet.
	 * @param data результат .nestable("serialize")
	 */
	this.convert = function(data) {
		var result=[];
		var _makeNestedSet=function(root, lft, data, level, ordering) {
			$(data).each(function(idx) {
				rgt=((this.children instanceof Array) ? _makeNestedSet(root, lft+1, this.children, level+1) : lft) + 1;
				var itemData = {id: this.id};
				itemData[o("rootAttribute", "root")] = root;
				itemData[o("leftAttribute", "left")] = lft;
				itemData[o("rightAttribute", "right")] = rgt;
				itemData[o("levelAttribute", "level")] = level;
				itemData[o("orderingAttribute", "ordering")] = (typeof(ordering)=='undefined') ? idx+1 : ordering;
				result.push(itemData);
				lft=rgt+1;
			});
			return rgt;
		};
		$(data).each(function(idx) {
			_makeNestedSet(this.id, 1, this, 1, idx+1); 
		});
		return result;
	};
	
	/**
	 * Сохранение данных
	 */
	this.save = function(e) {
		e.preventDefault();
		if(o("saveUrl")) {
			$.post(o("saveUrl"), {data: JSON.stringify(_this.getNestedSet())}, function(data) {}, "json");
		}
	}
	
	this.init();
};