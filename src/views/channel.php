<?php

$channelId = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$channel = DataManager::getChannelById($channelId);
if ($channel === null || !AuthenticationManager::isAuthenticated())
    Util::redirect('/');

?>

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?php echo $channel->getName() ?>
        </h1>
    </div>
</div>
<!-- /.row -->

<div id="messages" class="row"></div>

<div class="row">
    <div class="col-lg-8">
        <form class="form-horizontal" id="addNewPost">
            <div>
                <input type="text" class="form-control" name="title" placeholder="Title" id="postTitle" required>
                <textarea class="form-control" rows="3" placeholder="Text" name="text" id="postText"
                          required></textarea>
                <button type="submit" class="btn btn-default">Submit</button>
                <input type="hidden" name="channelId" value="<?php echo $channelId ?>">
            </div>
        </form>
    </div>
</div>
<!-- /.row -->

<script type="text/javascript">
    function addPosts(type, posts) {
        for (i = 0; i < posts.length; ++i) {
            var favoriteClass = posts[i].isFavorite ? "fa-star" : "fa-star-o";
            var panelClass = posts[i].isUnread ? "panel-danger" : "panel-primary";

            $("#messages").append(
                "<div class='col-lg-8'><div class='panel " + panelClass + "'>" +
                    "<div class='panel-heading'>" +
                        "<input type='hidden' value='" + posts[i].id + "'>" +
                        "<h3 class='panel-title'>" +
                            "<button type='button' id='btnFavorite' class='btn btn-link'>" +
                                "<i class='fa " + favoriteClass + "'></i>" +
                            "</button> " +
                            posts[i].title +
                            "<span class='align-right'>"
                                + posts[i].user.firstName + " " + posts[i].user.lastName + ", " + posts[i].datetime +
                                " <a href='?view=editPost&id=" + posts[i].id + "'>" +
                                    "<i class='fa fa-pencil'></i>" +
                                "</a>" +
                                " <a href='?view=deletePost&id=" + posts[i].id + "'>" +
                                    " <i class='fa fa-trash'></i>" +
                                "</a>" +
                            "</span>" +
                        "</h3>" +
                    "</div>" +
                    "<div class='panel-body'>" + posts[i].text.replace(/(?:\r\n|\r|\n)/g, '<br />') + "</div>" +
                "</div></div>"
            );
        }
    }

    var lastSeenPost = 0;
    function loadData() {
        $.ajax({
            type: "GET",
            url: "api/posts.php?lastSeenPost=" + lastSeenPost + "&channelId=<?php echo $channelId ?>",

            async: true,
            cache: false,
            timeout: 50000,

            success: function (data) {
                if (data != null && $.isArray(data)) {
                    lastSeenPost = data[data.length - 1].id;
                    addPosts("new", data);
                }
                setTimeout(
                    loadData,
                    1000
                );
            },
            error: function () {
                setTimeout(
                    loadData, /* Try again after.. */
                    15000);
            }
        });
    }

    $(document).ready(function () {
        loadData();
    });


    $(document).on('click', "#btnFavorite", function () {
        $(this).find(">:first-child").toggleClass("fa-star-o");
        $(this).find(">:first-child").toggleClass("fa-star");

        var data = {
            "id": $(this).parent().parent().find(">:first-child").val(),
            "value": $(this).find(">:first-child").hasClass("fa-star") ? 1 : 0
        };

        var requestToggleFav = $.ajax({
            url: "/api/posts.php?<?php echo Controller::ACTION . '=' . Controller::ACTION_SETFAVORITE ?>",
            type: "post",
            data: data
        });
    });


    // Variable to hold request
    var requestAddPost;
    // Bind to the submit event of our form
    $("#addNewPost").submit(function (event) {

        // Abort any pending requestAddPost
        if (requestAddPost) {
            requestAddPost.abort();
        }
        // setup some local variables
        var $form = $("#addNewPost");

        // Let's select and cache all the fields
        var $inputs = $form.find("input, select, button, textarea");

        // Serialize the data in the form
        var serializedData = $form.serialize();

        // Let's disable the inputs for the duration of the Ajax requestAddPost.
        // Note: we disable elements AFTER the form data has been serialized.
        // Disabled form elements will not be serialized.
        $inputs.prop("disabled", true);

        // Fire off the requestAddPost
        requestAddPost = $.ajax({
            url: "/api/posts.php?<?php echo Controller::ACTION . '=' . Controller::ACTION_NEWPOST ?>",
            type: "post",
            data: serializedData
        });

        // Callback handler that will be called regardless
        // if the requestAddPost failed or succeeded
        requestAddPost.always(function () {
            // Reenable the inputs
            $inputs.prop("disabled", false);
            $("#postText").val("");
            $("#postTitle").val("");
        });

        // Prevent default posting of form
        event.preventDefault();
    });
</script>