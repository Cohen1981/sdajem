<?php
$wa = $this->document->getWebAssetManager();
$wa->useScript('modal-content-select');

$this->document->addScriptOptions('content-select-on-load', $this->items, false);
