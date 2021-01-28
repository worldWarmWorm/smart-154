<?php
/**
 * Яндекс.Карта
 *
 * @var array $ymapBounds массив основных параметров карты. 
 * @var array $geoObjects массив геообъектов.
 */
use AttributeHelper as A;
CmsHtml::jquery();
?>
<style type="text/css">
.ymaps-marker-form-wrapper .row {
    margin-bottom: 8px;
}
.ymaps-marker-form-wrapper .row input {
    width: 216px;
}
.ymaps-marker-form-wrapper .row textarea {
    width: 216px;
    height: 80px;
}
.ymaps-marker-form-wrapper [type=button] {
    float: right;
}
.ymaps-marker-form-wrapper .form-control {
  display: block;
  width: 100%;
  height: 18px;
  padding: 6px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
  -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
       -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
          transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
.ymaps-marker-form-wrapper .form-control:focus {
  border-color: #66afe9;
  outline: 0;
  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
          box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}
.ymaps-marker-form-wrapper .form-control::-moz-placeholder {
  color: #999;
  opacity: 1;
}
.ymaps-marker-form-wrapper .form-control:-ms-input-placeholder {
  color: #999;
}
.ymaps-marker-form-wrapper .form-control::-webkit-input-placeholder {
  color: #999;
}
.btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: normal;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
      touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}
.btn:focus {
  outline: thin dotted;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}
.btn:hover,
.btn:focus {
  color: #333;
  text-decoration: none;
}
.btn-primary {
  color: #fff;
  background-color: #337ab7;
  border-color: #2e6da4;
}
.btn-primary:focus,
.btn-primary.focus {
  color: #fff;
  background-color: #286090;
  border-color: #122b40;
}
.btn-primary:hover {
  color: #fff;
  background-color: #286090;
  border-color: #204d74;
}
.btn-danger {
  color: #fff;
  background-color: #d9534f;
  border-color: #d43f3a;
}
.btn-danger:focus,
.btn-danger.focus {
  color: #fff;
  background-color: #c9302c;
  border-color: #761c19;
}
.btn-danger:hover {
  color: #fff;
  background-color: #c9302c;
  border-color: #ac2925;
}
</style>
<div id="myYMap" style="width:100%; height:475px" class="2gismap"></div>

<?if(!empty($geoObjects)): foreach($geoObjects as $hash=>$data):?>
<div id="geoobject_<?=$hash?>" style="display:none !important">
	<balloonContentHeader><?=$data['balloonContentHeader']?></balloonContentHeader>
	<balloonContentBody><?=$data['balloonContentBody']?></balloonContentBody>
	<balloonContentFooter><?=$data['balloonContentFooter']?></balloonContentFooter>
</div>
<?endforeach; endif;?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU"></script>
<script type="text/javascript">
/**
 * Calculate a 32 bit FNV-1a hash
 * Found here: https://gist.github.com/vaiorabbit/5657561
 * Ref.: http://isthe.com/chongo/tech/comp/fnv/
 *
 * @param {string} str the input value
 * @param {boolean} [asString=false] set to true to return the hash value as 
 *     8-digit hex string instead of an integer
 * @param {integer} [seed] optionally pass the hash of the previous chunk
 * @returns {integer | string}
 */
function hashFnv32a(str, asString, seed) {
    /*jshint bitwise:false */
    var i, l,
        hval = (seed === undefined) ? 0x811c9dc5 : seed;

    for (i = 0, l = str.length; i < l; i++) {
        hval ^= str.charCodeAt(i);
        hval += (hval << 1) + (hval << 4) + (hval << 7) + (hval << 8) + (hval << 24);
    }
    if( asString ){
        // Convert to 8 digit hex string
        return ("0000000" + (hval >>> 0).toString(16)).substr(-8);
    }
    return hval >>> 0;
}

// @var string YMMF_SAVE_URL адрес ссылки для сохранения данных маркера.
var YMMF_SAVE_URL="/cp/default/ymapSaveGeoObject";
// @var string YMMF_REMOVE_URL адрес ссылки для удаления маркера.
var YMMF_REMOVE_URL="/cp/default/ymapRemoveGeoObject";
// @var string YMMF_BOUNDSCHANGE_URL адрес ссылки для сохранения изменений позиционирования и масштаба карты.
var YMMF_BOUNDSCHANGE_URL="/cp/default/ymapBoundsChangeGeoObject";
// @var string YMMF_WRAPPER_CLASS имя класса враппера формы редактирования маркера.
var YMMF_WRAPPER_CLASS="ymaps-marker-form-wrapper";

/**
 * Класс формы добавления/изменения данных балуна/метки. 
 */
function ymapsMarkerForm(rootElement) {
	var _this={
		// @var mixed root корневой элемент, в котором находится форма.
		root: null,
		/**
		 * Инициализация
		 */
		init: function(rootElement) {
			_this.root=$(rootElement);
		},
		reset: function() {
			_this.root.find("textarea").html("");
			_this.root.find(":text,:hidden").attr("value", "");
		}, 
		get: function(name) {
			return _this.root.find("[name='"+name+"']").val();
		},
		set: function(root, value) {
			var $elm=_this.root.find("[name='"+name+"']");
			if($elm.is("textarea")) $elm.html(value);
			else $elm.attr("value", value);
		}
	};
	
	_this.init(rootElement);
			
	return _this;
}

/**
 * Инициализация Яндекс.Карты
 */
ymaps.ready(init);
function init () {
	// @var Map map объект Яндекс.Карты 
	<?$center=A::get($ymapBounds, 'center', array('55.04902279997517', '82.91542373974723'))?>
	var map = new ymaps.Map("myYMap", {
        center: [<?=$center[0]?>, <?=$center[1]?>],
        zoom: <?=A::get($ymapBounds, 'zoom', 16)?>,
        controls: ["zoomControl", "searchControl", "geolocationControl"]
    }, {
       searchControlProvider: "yandex#search"
    });

	var BalloonContentLayout=ymaps.templateLayoutFactory.createClass(
        '<div class="'+YMMF_WRAPPER_CLASS+'">'
        + '<form>'
        + '<input type="hidden" name="hash" value="$[properties.hash]" />'
        + '<input type="hidden" name="x" value="$[properties.x]" />'
        + '<input type="hidden" name="y" value="$[properties.y]" />'
        + '<input type="hidden" name="saved" value="$[properties.saved]" />'
        + '<div class="row"><input  class="form-control" name="balloonContentHeader" value="$[properties.balloonContentHeader]" placeholder="Введите название..."/></div>'
        + '<div class="row"><textarea class="form-control" name="balloonContentBody" placeholder="Введите описание...">$[properties.balloonContentBody]</textarea></div>'
        + '<div class="row"><input  class="form-control" name="balloonContentFooter" value="$[properties.balloonContentFooter]" placeholder="Введите дополнительный текст..."/></div>'
        + '<div class="buttons"><input class="btn btn-primary" type="submit" value="$[properties.submitText]">&nbsp;'
        + '<input type="button" class="btn btn-danger" name="$[properties.buttonName1]" value="$[properties.buttonText1]"></div>'
        + '</form>'
        + '</div>', {
        build: function () {
            BalloonContentLayout.superclass.build.call(this);

            // обработчик на кнопку отправки формы добавления/изменения балуна/метки.
            $(document).on("click", "."+YMMF_WRAPPER_CLASS+" :submit", function(e) {
            	e.stopImmediatePropagation();
            	
                var geoObject,
            		frm=new ymapsMarkerForm($(e.target).parents("."+YMMF_WRAPPER_CLASS+":first")),
            		$elements=frm.root.find(":hidden,:text,textarea");

            	geoObject=fGetGeoObjectByHash(frm.get("hash"));
        		if(!geoObject) 
            		return fError("Geo object not found");
        		
       			if(frm.get("saved") != 1) {
       				geoObject.properties.set("submitText", "Сохранить");
       				geoObject.properties.set("buttonName1", "remove");
       				geoObject.properties.set("buttonText1", "Удалить");
       			}
       			$elements.each(function() {
       				geoObject.properties.set($(this).attr("name"), frm.get($(this).attr("name")));
       			});
       			geoObject.properties.set("saved", 1);
            	
    			fSaveGeoObject(geoObject);
            	map.balloon.close();

        		return false;
            });

            // обработчик на действие удаления
            $(document).on("click", "."+YMMF_WRAPPER_CLASS+" [name='remove']", function(e) {
            	e.stopImmediatePropagation();
            	var frm=new ymapsMarkerForm($(e.target).parents("."+YMMF_WRAPPER_CLASS+":first"));
            	fRemoveGeoObject(fGetGeoObjectByHash(frm.get("hash")));
			});  
         	// обработчик на действие отмена
            $(document).on("click", "."+YMMF_WRAPPER_CLASS+" [name='cancel']", function(e) {
            	e.stopImmediatePropagation();
            	map.balloon.close();
			});  
        }
	});
	    
	// @var function fError выброс ошибки. 
	var fError=function(msg) { alert(msg); return false; };

	// @var function getGeoObjectByHash() поиск геообъекта по hash
	var fGetGeoObjectByHash=function(hash) {
        var geoObject=null;
        map.geoObjects.each(function(_geoObject) {
            if(_geoObject.properties.get("hash") == hash) {
                geoObject=_geoObject;
                return;
            }
        });
        return geoObject;
    };
    
    // @var function fSaveGeoObject сохранить геообъект.
    var fSaveGeoObject=function(geoObject) {
        var properties=geoObject.properties,
        	coords=geoObject.geometry.getCoordinates();
    	
        $.post(YMMF_SAVE_URL, {
            hash: properties.get("hash"),
            x: coords[0],
            y: coords[1],
            balloonContentHeader: properties.get("balloonContentHeader"),
            balloonContentBody: properties.get("balloonContentBody"),
            balloonContentFooter: properties.get("balloonContentFooter")
        }, "json");

        geoObject.properties.set("saved", 1);
    };

    // @var function fRemoveGeoObject удалить геообъект.
    var fRemoveGeoObject=function(geoObject) {
        $.post(YMMF_REMOVE_URL, {hash: geoObject.properties.get("hash")}, "json");
        map.geoObjects.remove(geoObject);
    };

    // @var function fCreatePlacemark создание объекта маркера.
    var fCreatePlacemark=function(data) {
    	var defaultData={
    		submitText: "Сохранить", 
    		buttonName1: "remove",
    		buttonText1: "Удалить"
    	};
        for(key in defaultData)  
        	if(!data[key]) data[key]=defaultData[key];

        var placemark = new ymaps.Placemark([data.x, data.y], data, {
    	    balloonMinWidth: 220,
    	    balloonMinHeight: 135,
        	balloonContentLayout: BalloonContentLayout,
    		preset: "islands#nightDotIcon",
    	    draggable: true
    	});

        placemark.events.add("dragend", function(e) {
        	fSaveGeoObject(e.get("target"));
        });
        placemark.events.add("balloonclose", function(e) {
            if(e.get("target").properties.get("saved") != 1) { 
            	map.geoObjects.remove(e.get("target"));
            }
        });

        return placemark;
    };
    
	// @var function fAddPlacemark добавление маркера на карту.
    var fAddPlacemark=function(data) {
        var placemark=fCreatePlacemark(data);
		map.geoObjects.add(placemark);
		return placemark;
    };

    <?if(!empty($geoObjects)):?>
    // @var function fAddSavedPlacemark добавление на карту сохраненных геообъектов.
    var fAddSavedPlacemark=function(hash, x, y) {
    	var $data=$("#geoobject_"+hash);
        fAddPlacemark({
            hash: hash,
            saved: 1,
            x: x,
            y: y,
            balloonContentHeader: $data.find("balloonContentHeader").html(),
            balloonContentBody: $data.find("balloonContentBody").html(),
           	balloonContentFooter: $data.find("balloonContentFooter").html()
        });
	};
    <?foreach($geoObjects as $hash=>$data) echo "fAddSavedPlacemark('{$hash}','".$data['x']."','".$data['y']."');\n";?>
    <?endif;?>

	// Открытие формы добавления балуна/метки
    map.events.add("click", function (e) {
        if (map.balloon.isOpen()) {
        	map.balloon.close();
        }
        else {
        	var coords=e.get("coords"),
        		x=coords[0].toPrecision(6),
        		y=coords[1].toPrecision(6);
        	var placemark=fAddPlacemark({
        		hash: hashFnv32a(x+" "+y, true),
	            x: x,
	            y: y,
	            saved: 0,
	            submitText: "Добавить", 
	            buttonName1: "cancel",
	        	buttonText1: "Отмена"
            });
            placemark.events.fire('click', {
                coordPosition: placemark.geometry.getCoordinates(),
                target: placemark
            });
        }
    });

    map.events.add("boundschange", function(e) {
        $.post(YMMF_BOUNDSCHANGE_URL, {
        	zoom: e.get("newZoom"),
        	center: e.get("newCenter"),
        	globalPixelCenter: e.get("newGlobalPixelCenter")
        }, "json");
    });
}
</script>

