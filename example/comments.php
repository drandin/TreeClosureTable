<?php

use \TreeClosureTable\Exception\ExceptionClosureTable;

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {

    $cfg = require 'config.php';

    if (!empty($cfg)) {

        require '../Autoload.php';

        $idUserAuthorized = $cfg['idUserAuthorized'];
        $idSubject = $cfg['idSubject'];

        try {

            $commentator = new \TreeClosureTable\Commentator(
                $cfg['parameters'],
                \TreeClosureTable\DB\DB::getPDO($cfg['db'])
            );

            $comments = $commentator->setIdSubject($idSubject)->getTree();

            require 'view/comments.php';

        }
        catch(ExceptionClosureTable $e) {
            echo $e->getMessage();
        }
    }
}