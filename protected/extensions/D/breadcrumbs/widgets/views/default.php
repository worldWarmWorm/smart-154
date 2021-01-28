<?php
echo \CHtml::openTag('ul', $this->htmlOptions);

if($this->homeTitle) {
	$linkOptions=isset($this->homeHtmlOptions['linkOptions']) ? $this->homeHtmlOptions['linkOptions'] : array();
	echo \CHtml::tag('li', $this->homeHtmlOptions, ($this->homeUrl ? CHtml::link($this->homeTitle, $this->homeUrl, $linkOptions) : $this->homeTitle));
}
	
foreach($this->breadcrumbs as $b) {
	$linkOptions=isset($b['htmlOptions']['linkOptions']) ? $b['htmlOptions']['linkOptions'] : array();
	$htmlOptions=isset($b['htmlOptions']) && is_array($b['htmlOptions']) ? $b['htmlOptions'] : array();

	if($b === end($this->breadcrumbs) && !isset($htmlOptions['class'])) {
		$htmlOptions['class'] = 'active';
	}
	
	echo \CHtml::tag('li', $htmlOptions, ($b['url'] ? CHtml::link($b['title'], $b['url'], $linkOptions) : $b['title']));
}

echo \CHtml::closeTag('ul');
?>