<?php
$xpdo_meta_map['ShortlinkMgrClick'] = array(
    'package'   => 'shortlinkmgr',
    'version'   => '1.0',
    'table'     => 'shortlinkmgr_clicks',
    'extends'   => 'xPDOSimpleObject',
    'fields'    => array(
        'link_id'    => 0,
        'clicked_at' => null,
        'ip_address' => null,
        'referrer'   => null,
        'user_agent' => null,
    ),
    'fieldMeta' => array(
        'link_id'    => array('dbtype' => 'int',      'precision' => '10',   'phptype' => 'integer',  'null' => false, 'default' => 0, 'index' => 'index'),
        'clicked_at' => array('dbtype' => 'datetime',                        'phptype' => 'datetime', 'null' => true),
        'ip_address' => array('dbtype' => 'varchar',  'precision' => '45',   'phptype' => 'string',   'null' => true),
        'referrer'   => array('dbtype' => 'varchar',  'precision' => '2048', 'phptype' => 'string',   'null' => true),
        'user_agent' => array('dbtype' => 'varchar',  'precision' => '512',  'phptype' => 'string',   'null' => true),
    ),
    'indexes'   => array(
        'PRIMARY' => array(
            'alias'   => 'PRIMARY', 'primary' => true, 'unique' => true, 'type' => 'BTREE',
            'columns' => array('id' => array('length' => '', 'collation' => 'A', 'null' => false)),
        ),
        'link_id' => array(
            'alias'   => 'link_id', 'primary' => false, 'unique' => false, 'type' => 'BTREE',
            'columns' => array('link_id' => array('length' => '', 'collation' => 'A', 'null' => false)),
        ),
    ),
    'aggregates' => array(
        'Link' => array(
            'class'       => 'ShortlinkMgrLink',
            'local'       => 'link_id',
            'foreign'     => 'id',
            'cardinality' => 'one',
            'owner'       => 'foreign',
        ),
    ),
);
