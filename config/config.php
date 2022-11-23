<?php

/** Import core settings */
$this->addFile(__DIR__ . '/config.json');

/** Import middlewares */
$this->addFile(__DIR__ . '/middlewares.json');

/** Import routes */
$this->addFile(__DIR__ . '/routes.json');

/** Import redirections */
$this->addFile(__DIR__ . '/redirections.json');

/** Import templates */
$this->addFile(__DIR__ . '/templates.json');

/** Import attachments */
$this->addFile(__DIR__ . '/attachments.json');

/** Import local settings */
$appEnv  = 'local';
$envFile = __DIR__ . '/config.' . $appEnv . '.json';
if (file_exists($envFile)) {
    $this->addFile($envFile);
}
