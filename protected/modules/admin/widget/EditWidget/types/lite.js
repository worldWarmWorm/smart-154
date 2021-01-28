tinymce.init({
    selector : "#<?=$editorSelectorId?>",
  //  plugins : "paste, table, -gismap, autolink", //-cmsbuttons
	plugins : "code, visualchars, wordcount, link, autolink, lists, media, contextmenu, visualchars,nonbreaking,spellchecker",
    extended_valid_elements: "span",
    valid_children : "+body[style]",
    invalid_elements: "script",
    extended_valid_elements: '*[*]',
    valid_elements: '*[*]',
	convert_urls: false,
    insert_width: 200,
    mode : "textareas",
    theme : "modern",
    language : "ru",
    height : <?=$height?>,
    menubar: "", /*format */
    image_advtab: true,
    contextmenu: "",
    content_css: '<?php echo $assets; ?>/css/editor.css',
    toolbar1: "bold italic | link unlink | removeformat |<?php if(!$this->disableToolbarCode) echo ' code  |';?> spellchecker",
    init_instance_callback: function (editor) {
    	<?php if($this->initInstanceCallback) echo $this->initInstanceCallback; ?>
    },
    spellchecker_languages: "Russian=ru,English=en",
    spellchecker_language: "ru",  // default language
    spellchecker_rpc_url: "//speller.yandex.net/services/tinyspell"
});


