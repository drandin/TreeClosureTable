<?php

use \TreeClosureTable\Exception\ExceptionClosureTable;

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {

    $cfg = require 'config.php';

    if (!empty($cfg)) {

        require '../Autoload.php';

        $idUserAuthorized = $cfg['idUserAuthorized'];

        $res = false;
        $textComment = (string)filter_input(INPUT_POST, 'textComment');
        $idSubject = (int)filter_input(INPUT_POST, 'idSubject');

        try {

            $commentator = new \TreeClosureTable\Commentator(
                $cfg['parameters'],
                \TreeClosureTable\DB\DB::getPDO($cfg['db'])
            );

            $res = $commentator->add(
                new \TreeClosureTable\Comments(
                    array(
                        'idSubject' => $idSubject,
                        'idUser' => $idUserAuthorized,
                        'content' => $textComment
                    )
                )
            );

            echo json_encode(array('res' => $res));

        }
        catch(ExceptionClosureTable $e) {
            echo $e->getMessage();
        }
    }
}
