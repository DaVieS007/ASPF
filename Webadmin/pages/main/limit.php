<?php
	$widget->head(8,L("YOUR_SUBSCRIPTION_LIMIT_REACHED"));
	$widget->lead(8,L("YOUR_SUBSCRIPTION_LIMIT_REACHED_DESC"));

	$CONTENT = $widget->row();
	$widget->add($widget->button("danger",L("CANCEL"),$url->write(array())));
	$CONTENT .= $widget->row();
?>