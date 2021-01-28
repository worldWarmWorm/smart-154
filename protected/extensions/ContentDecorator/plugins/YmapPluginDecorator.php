<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 28.12.11
 * Time: 15:20
 * To change this template use File | Settings | File Templates.
 */
use AttributeHelper as A;

class YmapPluginDecorator extends PluginDecorator
{
    public $point = '{ymap}';

    public function processModel($model, $attribute = 'text')
    {
        $result = $this->checkPoint($model->$attribute);

        if (!$result)
            return;

        $html = $this->mapHtml();
        $model->$attribute = $this->replace($model->$attribute, $html);
    }

    private function mapHtml()
    {
    	CmsHtml::js('//api-maps.yandex.ru/2.1/?lang=ru-RU');
    	
    	$geoObjects=Yii::app()->settings->get('ymaps_geo_objects') or array();
    	if(empty($geoObjects)) return;
    	
    	$ymapBounds=Yii::app()->settings->get('ymaps_bounds') or array(
    		'zoom'=>16,
    		'center'=>array('55.04902279997517', '82.91542373974723'),
    		'globalPixelCenter'=>array('12252746.816147964', '5317285.322633277')
    	);
    	ob_start(); 
        ?>
			<div id="myYMap" style="width:100%; height:475px" class="ymap"></div>
			<?foreach($geoObjects as $hash=>$data):?>
			<div id="geoobject_<?=$hash?>" style="display:none !important">
				<balloonContentHeader><?=$data['balloonContentHeader']?></balloonContentHeader>
				<balloonContentBody><?=$data['balloonContentBody']?></balloonContentBody>
				<balloonContentFooter><?=$data['balloonContentFooter']?></balloonContentFooter>
			</div>
			<?endforeach?>
			<script type="text/javascript">
			$(document).ready(function(){
			ymaps.ready(init);
			function init () {
				<?$center=A::get($ymapBounds, 'center', array('55.04902279997517', '82.91542373974723'))?>
				var map = new ymaps.Map("myYMap", {
			        center: [<?=$center[0]?>, <?=$center[1]?>],
			        zoom: <?=A::get($ymapBounds, 'zoom', 16)?>,
			        controls: ["zoomControl"]
			    }, {
			       searchControlProvider: "yandex#search"
			    });
				map.behaviors.disable('scrollZoom');
			    var fAddPlacemark=function(hash, x, y) {
			    	var $data=$("#geoobject_"+hash),
			    		placemark=new ymaps.Placemark([x, y], {
				            balloonContentHeader: $data.find("balloonContentHeader").html(),
				            balloonContentBody: $data.find("balloonContentBody").html(),
				           	balloonContentFooter: $data.find("balloonContentFooter").html()
				        }, {
				    		preset: "islands#nightDotIcon",
				    	    draggable: false
				    	});
			    	map.geoObjects.add(placemark);
				};
			    <?foreach($geoObjects as $hash=>$data) echo "fAddPlacemark('{$hash}','".$data['x']."','".$data['y']."');\n";?>
			}        
			});
			</script>
       	<?php 
        $content = ob_get_contents();
        ob_clean();

        return $content;
    }
}
