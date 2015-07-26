<?php

if ($comments instanceof \TreeClosureTable\ClosureTableCollection && isset($idUserAuthorized)) {
    foreach ($comments as $comment) {
        if ($comment instanceof \TreeClosureTable\Comments) {

            $paddingLeft = $comment->getLevel() * 60;
            $paddingLeft = $paddingLeft === 0 ? $paddingLeft : $paddingLeft.'px';

            $arrUser = array();
            $idUser = $comment->getIdUser();

            $date = date_format(date_create($comment->getDateCreate()), 'd-m-Y, H:i:s');

            ?>
            <div id="commentItem<?=$comment->getIdEntry();?>" class="commentItem" style="padding-left: <?=$paddingLeft;?>">
                <div class="commentBody">
                    <div class="commentAvatar">&nbsp;</div>
                    <div class="commentBase">
                        <div>
                            <span class="commentName">User #<?=$comment->getIdUser();?></span>
                            <span class="commentDate"><?=$date;?></span>
                        </div>
                        <div class="commentContent"><?=$comment->getContent();?></div>
                        <div class="commentControl">
                            <?php if ($idUserAuthorized != $comment->getIdUser()):  ?>
                                <a href="#" class="linkReply" id="linkReply<?=$comment->getIdEntry();?>">Ответить</a>
                            <?php else: ?>&nbsp;<?php endif; ?>
                        </div>
                        <div class="commentReply" id="commentReply<?=$comment->getIdEntry();?>"></div>
                    </div>
                </div>
            </div>
        <?php
        }
    }
}