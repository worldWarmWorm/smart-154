<?php
    /**
     * File: index.php
     * User: Mobyman
     * Date: 28.01.13
     * Time: 12:26
     */
     $this->breadcrumbs = array('Отзывы на товар'=>array('review/index'));
?>



<style type="text/css">
  span.star-view {background: url(<?php echo Yii::app()->baseUrl; ?>/images/marks/star.png); height:16px; display: inline-block;vertical-align: top;}
  .star-1 {width:18px;}
  .star-2 {width:36px;}
  .star-3 {width:54px;}
  .star-4 {width:72px;}
  .star-5 {width:90px;}
</style>
<script type="text/javascript">
  $(function() {
    $("#reviews").on('click', '.mark', function(){
      t = $(this);
      $.ajax({
        type: "POST",
        url: "<?php echo Yii::app()->createUrl("/admin/review/ajax"); ?>",
        data: {item: $(this).data('item'), action: "publish"},
        dataType: "json",
        success: function(data) {
          if(!data.status) {
              $(t).removeClass('unmarked');
          } else {
              $(t).addClass('unmarked');
          }
          $('.review-count, #site-menu a[href$="review/index"] .notify').text(data.count);
        }
      });
    })
  });
</script>
<h1>Отзывы</h1>
<?php if(!$model): ?>
  Отзывы пока отсутствуют...
<?php else: ?>
<table id="reviews" class="table table-striped table-bordered">
  <tr class="head">
    <th style="width:40px;">Имя</th>
    <th style="width:445px;">Отзыв</th>
    <th style="width:110px;">Оценка</th>
    <th style="width:80px;">Обработан</th>
    <th style="width:70px;"></th>
    <th style="width:70px;">Перейти</th>
  </tr>
  <?php foreach($model as $item): ?>
    <tr class="order review" data-item="<?php echo $item->id; ?>">
      <td><span title="<?php echo long2ip($item->ip); ?>"><b><?php echo $item->username; ?></b></span></td>
      <td><?php echo $item->text; ?></td>
      <td><span class="star-view star-<?php echo $item->mark; ?>"></span></td>
      <td><div class="mark <?php echo !$item->published ? 'marked' : 'unmarked'; ?>" data-item="<?php echo $item->id; ?>" title="Опубликовать"></div></td>
	    <td><?php echo CHtml::ajaxLink('Удалить', $this->createUrl('/admin/review/delete', array('id'=>$item->id)),
        array(
			    'type'=>'post',
          'data'=>array('ajax'=>1),
          'beforeSend'=>'function() { return confirm("Подтвердите удаление"); }',
          'success'=>'function() {$(".review[data-item='. $item->id .']").remove();}'
  		  ),
        array('class'=>'btn btn-danger')
        ); ?></td>
      <td><?php echo CHtml::link('К отзыву', array('/shop/product', 'id' => $item->product_id), array('style' => 'text-decoration:none;', 'target' => '_blank', 'class'=>'btn btn-success')); ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
)); ?>

<?php endif; ?>