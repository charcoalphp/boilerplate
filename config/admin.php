<?php

/** Import elFinder settings */
$this->addFile(__DIR__ . '/elfinder.json');

/** Admin settings */
$this->addFile(__DIR__ . '/admin.json');

/** Import `charcoal-attachment` routes */
$this->addFile(dirname(__DIR__) . '/vendor/charcoal/charcoal/packages/attachment/config/admin.json');
