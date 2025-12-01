<?php
$wa = $this->document->getWebAssetManager();
$wa->useScript('modal-content-select');

if ($this->item) {
	$data['id']    = $this->item->id;
	$data['title'] = $this->item->title;
}
$this->document->addScriptOptions('content-select-on-load', $data, false);