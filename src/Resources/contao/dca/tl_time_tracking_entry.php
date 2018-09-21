<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

$table = 'tl_time_tracking_entry';

$GLOBALS['TL_DCA'][$table] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['id'],
            'flag' => 1,
            'panelLayout' => 'filter,search;sort,limit'
        ],
        'label' => [
            'fields' => ['id'],
            'showColumns' => true,
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ],
            'pdf' => [
                'label' => &$GLOBALS['TL_LANG'][$table]['pdf'],
                'href' => '&key=pdf',
                'class' => 'headerpdf'
            ]
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG'][$table]['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ],
            'copy' => [
                'label' => &$GLOBALS['TL_LANG'][$table]['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif'
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG'][$table]['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"'
            ]
        ]
    ],

    'palettes' => [
        'default' => '{time_legend},from,to;'
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'length' => 10, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => '0'],
        ],
        'start' => [
            'label' => &$GLOBALS['TL_LANG'][$table]['start'],
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
                'rgxp' => 'datim',
                'datepicker' => true
            ],
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => '0']
        ],
        'end' => [
            'label' => &$GLOBALS['TL_LANG'][$table]['end'],
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
                'rgxp' => 'datim',
                'datepicker' => true
            ],
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => '0']
        ],
        'trackingId' => [
            'label' => &$GLOBALS['TL_LANG'][$table]['trackingId'],
            'inputType' => 'select',
            'foreignKey' => 'tl_time_tracking.description',
            'eval' => [
                'tl_class' => 'w50',
                'rgxp' => 'datim',
                'datepicker' => true
            ],
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => '0'],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy']
        ]
    ]
];
