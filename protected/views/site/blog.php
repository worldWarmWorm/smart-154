<h1><?php echo $blog->title; ?></h1>

<?php if (count($posts)) $this->renderPartial('_posts', compact('posts', 'pages')); else echo '<p>Нет статей</p>' ?>
 
