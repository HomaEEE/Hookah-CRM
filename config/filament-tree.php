<?php

return [
    /**
     * Tree model fields
     */
    'column_name' => [
        'order' => 'sort_order',
        'parent' => 'parent_category',
        'title' => 'category_name',
    ],
    /**
     * Tree model default parent key
     */
    'default_parent_id' => 0,
    /**
     * Tree model default children key name
     */
    'default_children_key_name' => 'category_id',
];
