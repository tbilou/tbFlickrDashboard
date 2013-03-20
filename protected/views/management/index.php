<?php
$this->breadcrumbs=array(
	'Management',
);?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>



<p><?php echo CHtml::ajaxLink('Download Set', array('ajax/reqSetList'), array('update'=>'#downloadPhotoset')); ?></p>

<div id="downloadPhotoset">
    
</div>

