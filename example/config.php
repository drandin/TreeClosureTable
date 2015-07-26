<?php

return array(

    /**
     * It is parameters needed for connect to DB
     */
    'db' => array(
        'host' => 'localhost',
        'dbname' => 'TreeComments',
        'user' => 'drandin',
        'password' => '1224996',
        'charset' => 'utf8',
    ),

    'parameters' => array(

        /**
         * It is the name of table in DB where stored comments
         */
        'tableData' => 'comments',

        /**
         * It is the name of table in db where stored hierarchic structure of tree
         */
        'tableTree' => 'commentsTree',

        /**
         * Is is name of class, that describes entity 'Comments'
         */
        'entity' => 'TreeClosureTable\Comments',

        /**
         * It is name of field which is ID of comment
         */
        'idTableData' => 'idEntry'
    ),

    /**
     * ID user, who is authorized on site on this moment (for example)
     */
    'idUserAuthorized' => 100,

    /**
     * Code of news on site, for example
     */
    'idSubject' => 160
);