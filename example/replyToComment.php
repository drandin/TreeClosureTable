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
        $idEntry = (int)filter_input(INPUT_POST, 'idEntry');

        try {

            $commentator = new \TreeClosureTable\Commentator(
                $cfg['parameters'],
                \TreeClosureTable\DB\DB::getPDO($cfg['db'])
            );

            $comment = $commentator
                ->setIdSubject($idSubject)
                ->getOneItem($idEntry);

            if (is_object($comment) && $comment->getIdSubject() === $idSubject) {

                if ($comment->getIdUser() != $idUserAuthorized) {

                    $res = $commentator->add(
                        new \TreeClosureTable\Comments(
                            array(
                                'idSubject' => $idSubject,
                                'idUser' => $idUserAuthorized,
                                'content' => $textComment
                            )
                        ),
                        $idEntry
                    );
                }
            }

            echo json_encode(array('res' => $res));

        }
        catch(ExceptionClosureTable $e) {
            echo $e->getMessage();
        }
    }
}