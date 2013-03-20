    <?php echo CHtml::beginForm(); ?>
    <?php echo CHtml::dropDownList('photosets','', $data, array()); ?>
    <?php echo Chtml::ajaxSubmitButton('Download Set', array('ajax/reqDownloadSet'), array('update'=>'#downloadInfo'), array('id'=>'btnDown')); ?>
    <?php echo CHtml::endForm(); ?>
<div id="downloadInfo" />

