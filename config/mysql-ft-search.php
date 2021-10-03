<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Mysql Search FullText Engine Settings
     |--------------------------------------------------------------------------
     |
     */

    /**
     * Schema
     */
    'schema_ft_enabled' => true,

    /**
     * Search builder engine
     */
    'scout_driver_name' => 'mysql',
    'remove_symbols' => ['-', '+', '<', '>', '@', '(', ')', '~'],
    'prefix_word_operator' => '+',
    'suffix_word_operator' => '*',
];
