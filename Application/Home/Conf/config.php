<?php
return array(
    'HTML_CACHE_ON' => true,
    'HTML_CACHE_RULES' => array(
        'Index:index' => array('{:module}_{:action}_{id}',0),
    ),
);