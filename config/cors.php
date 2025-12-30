<?php

return [

  'paths' => ['api/*'],

  'allowed_methods' => ['*'],

  'allowed_origins' => ['*'], // aman untuk bearer token

  'allowed_headers' => ['*'],

  'exposed_headers' => ['Content-Disposition'],

  'max_age' => 0,

  'supports_credentials' => false, // 🔴 PENTING

];
