<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']))) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

$userid = $_SESSION['user']['id'];
$username = $_SESSION['user']['name'];

?>

<head>
    <title>Chatbot | Home</title>
</head>

<body>
    <br>
    <div class="container">
        <div class="row justify-content-md-center mb-0">
            <div class="col-md-6">
                <?php
                $res = mysqli_query($con, "SELECT h.*, TRIM(content) <> '' As hascontent FROM history h LEFT JOIN articles a ON h.articleid = a.id WHERE userid = $userid;");
                echo '<div style="height:38px;"> <a class="clear-chat-history" ' . (mysqli_num_rows($res) == 0 ? 'style="display:none;"' : "") . ' onclick="clear_chat_history()"><strong> Clear Chat History </strong></a></div>';
                ?>

                <div class="card">
                    <div class="card-body messages-box">
                        <ul class="list-unstyled messages-list">
                            <?php

                            function get_source_message_start($source, $date)
                            {
                                $strtotime = strtotime($date);
                                $time = date('M d h:i A', $strtotime);

                                if ($source === 'USER') {
                                    $class = "messages-me";
                                    $imgAvatar = "user_avatar.png";
                                    $name = "You";
                                } else {
                                    $class = "messages-you";
                                    $imgAvatar = "bot_avatar.png";
                                    $name = "Chatbot";
                                }

                                return '<li class="' . $class . ' clearfix">
                                <span class="message-img">
                                <img src="assets/images/' . $imgAvatar . '" class="avatar-sm rounded-circle">
                                </span>
                                <div class="message-body clearfix">
                                <div class="message-header">
                                <strong class="messages-title"> ' . $name . '</strong> 
                                <small class="time-messages text-muted">
                                <span class="fas fa-time"></span>
                                <span class="minutes">' . $time . '</span>
                                </small> 
                                </div>';
                            }

                            if (mysqli_num_rows($res) > 0) {
                                $html = '';
                                $source = '';
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $message = $row['message'];
                                    $articleid = $row['articleid'];

                                    if ($row['hascontent']) {
                                        $confidence = $row['confidence'] > 0 ? '&nbsp;&nbsp;&nbsp;&nbsp;' . str_repeat("*", $row['confidence']) : "";
                                        $display = '<a target="_blank" href="view_article.php?id=' . $articleid . '"> ' . $message . $confidence . ' </a>';
                                    } else {
                                        $display = $message;
                                        $infoMessage = '';
                                    }

                                    $display = "<p>$display</p>";

                                    if ($source !== $row['source']) {
                                        $source = $row['source'];

                                        $start = get_source_message_start($source, $row['date']);
                                        $end = '</li>';
                                        $html .= $end . $start . $display;
                                    } else {
                                        $html .= $display;
                                    }
                                }

                                $html .= $end . '<script>jQuery(".messages-box").scrollTop(jQuery(".messages-box")[0].scrollHeight);</script>';
                                echo $html;
                            }

                            echo "<br>" . get_source_message_start('BOT', date('c')) . "<p>Welcome " . explode(" ", $username)[0] . ", How may I help you today ? </p></li>";

                            ?>

                        </ul>
                    </div>
                    <div class="card-header">
                        <div class="input-group">
                            <input id="message" type="text" name="messages" class="form-control input-sm" placeholder="Type your question here..." />
                            <span class="input-group-append">
                                <input type="button" class="btn btn-success" value="Ask" onclick="ask()">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#message').keypress(function(e) {
            var key = e.which;
            if (key == 13) {
                ask();
                return false;
            }
        });

        $(document).ready(function() {
            jQuery('.messages-box').outerHeight($(window).height() * 0.65);

            $(window).resize(function() {
                jQuery('.messages-box').outerHeight($(window).height() * 0.65);
            });
        });

        function clear_chat_history() {
            jQuery.ajax({
                url: 'clear_chat_history.php',
                type: 'get',
                success: function(response) {
                    var html = `<li class="messages-you clearfix"><span class="message-img"><img src="assets/images/bot_avatar.png" class="avatar-sm rounded-circle"></span><div class="message-body clearfix"><div class="message-header"><strong class="messages-title">Chatbot</strong> <small class="time-messages text-muted"><span class="fas fa-time"></span> <span class="minutes">${formatTime()}</span></small></div><p>Welcome <?= explode(" ", $username)[0] ?>, How may I help you today ?</p></div>`;
                    jQuery('.messages-list').html(html);
                    jQuery('.clear-chat-history').css('display', 'none');
                    jQuery('.messages-box').scrollTop(jQuery('.messages-box')[0].scrollHeight);
                },
                error: function(xhr, exception) {
                    alert('An error occurred while deleting the chat history. Please try again');
                }
            });
        }

        function ask() {
            var question = jQuery('#message').val().trim();
            jQuery('#message').val('');

            if (question.length === 0) {
                return;
            }

            jQuery('.start_chat').hide();

            var html = `<li class="messages-me clearfix"><span class="message-img"><img src="assets/images/user_avatar.png" class="avatar-sm rounded-circle"></span><div class="message-body clearfix"><div class="message-header"><strong class="messages-title">You</strong> <small class="time-messages text-muted"><span class="fas fa-time"></span> <span class="minutes">${formatTime()}</span></small> </div><p>${question}</p></div></li>`;
            jQuery('.messages-list').append(html);

            jQuery.ajax({
                url: 'ask_bot.php',
                type: 'post',
                data: `q=${question}`,
                success: function(response) {
                    var messages = JSON.parse(response);

                    var html = `<li class="messages-you clearfix"><span class="message-img"><img src="assets/images/bot_avatar.png" class="avatar-sm rounded-circle"></span><div class="message-body clearfix"><div class="message-header"><strong class="messages-title">Chatbot</strong> <small class="time-messages text-muted"><span class="fas fa-time"></span> <span class="minutes">${formatTime()}</span></small> </div>`;

                    for (msg of messages) {
                        var confidence = msg['confidence'] > 0 ? `&nbsp;&nbsp;&nbsp;&nbsp;${'*'.repeat(msg['confidence'])}` : '';
                        if (msg['id'] > 0) {
                            html += `<p><a target="_blank" href="view_article.php?id=${msg.id}"> ${msg.title + confidence}</a></p>`;
                        } else {
                            html += `<p>${msg.title + confidence}</p>`;
                        }
                    }

                    html += '</div></li>';
                    jQuery('.messages-list').append(html);
                    jQuery('.clear-chat-history').css('display', 'inherit');
                    jQuery('.messages-box').scrollTop(jQuery('.messages-box')[0].scrollHeight);
                },
                error: function(xhr, exception) {
                    var msg = '';
                    if (xhr.status === 0) {
                        msg = 'Not connected. Verify Network.';
                    } else if (xhr.status == 401) {
                        msg = 'Authorization Required, please refresh the page [401]';
                    } else if (xhr.status == 404) {
                        msg = 'Requested page not found [404]';
                    } else if (xhr.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = `Unknown Error: ${xhr.responseText}`;
                    }

                    var html = `<li class="messages-you clearfix"><span class="message-img"><img src="assets/images/bot_avatar.png" class="avatar-sm rounded-circle"></span><div class="message-body clearfix"><div class="message-header"><strong class="messages-title">Chatbot</strong> <small class="time-messages text-muted"><span class="fas fa-time"></span> <span class="minutes">${formatTime()}</span></small> </div><p>${msg}</p></div></li>`;
                    jQuery('.messages-list').append(html);
                    jQuery('.messages-box').scrollTop(jQuery('.messages-box')[0].scrollHeight);
                }
            });
        }
    </script>
</body>

</html>

<?php

include('footer.php');

?>