(function() {

    function TreeComments(obj) {

        var idSubject = +obj['idSubject'];
        var methodAddComment = obj['methodAddComment'];
        var methodReplyToComment = obj['methodReplyToComment'];
        var methodGetComments = obj['methodGetComments'];
        var comments = $('#comments');

        this.start = function() {

            if (idSubject > 0 && comments) {

                loadComments();

                $('#buttonNewComment').click(function () {
                    var textComment = $('#comment').val().trim();
                    if (textComment.length > 0 && /^\w+.*$/.test(textComment)) {
                        $.post(methodAddComment, {
                            textComment: textComment,
                            idSubject: idSubject
                        }, function (response) {
                            addComment(response, function() {
                                $('#comment').val('');
                            });
                        });
                    }
                    else {
                        $('#commentError').
                            html('<small>You should input text of comment!</small>')
                            .slideDown(0)
                            .delay(3000)
                            .slideUp(600);
                    }
                });

                var formReply = function (idEntry) {
                    var htmlReply = '<textarea class="form-control textareaReply" rows="3" placeholder="Text your answer" id="reply' + idEntry + '"></textarea>';
                    htmlReply += '<div class="replyError" id="replyError'+ idEntry +'"></div>';
                    htmlReply += '<div class="commentButtonReplyArea">';
                    htmlReply += '<button type="button" class="btn btn-primary" id="buttonReply" idComment="' + idEntry + '">Reply</button>';
                    htmlReply += '</div>';
                    htmlReply += '<br>';
                    return htmlReply;
                };

                comments.on('click', '.linkReply', function () {

                    var idEntry = +this['id'].replace(/linkReply/g, '');

                    if (idEntry > 0) {
                        $('.commentReply').empty();
                        $('#commentReply' + idEntry).html(formReply(idEntry));

                    }

                    return false;
                });

                comments.on('click', '#buttonReply', function (e) {
                    var idEntry = +e.currentTarget.getAttribute("idComment");

                    if (idEntry > 0) {
                        var textComment = $('#reply' + idEntry).val().trim();
                        if (textComment.length > 0 && /^\w+.*$/.test(textComment)) {
                            $.post(methodReplyToComment, {
                                textComment: textComment,
                                idSubject: idSubject,
                                idEntry: idEntry
                            }, function (response) {
                                addComment(response);
                            });
                        }
                        else {
                            $('#replyError' + idEntry)
                                .html('<small>You should input text for answer to comment!</small>')
                                .slideDown(0)
                                .delay(3000)
                                .slideUp(600);
                        }
                    }
                });
            }
            else {
                console.log('Object of configurations was defined wrong!');
            }
        };

        /**
         * @param response
         * @param callback
         */
        var addComment = function(response, callback) {

            try {
                var responseJSON = JSON.parse(response);

                if (responseJSON['res'] === true) {
                    loadComments();

                    if (typeof callback === "function") {
                        callback();
                    }
                }
                else {
                    console.log('Error add comment!!');
                }
            }
            catch (e) {
                console.log('error JSON!');
            }
        };


        function loadComments() {
            $('#comments').load(methodGetComments + '?idSubject=' + idSubject);
        }

        if(!String.prototype.trim){
            String.prototype.trim = function(){
                return this.replace(/^\s+|\s+$/g,'');
            };
        }
    }

    window.TreeComments = TreeComments;

}());
