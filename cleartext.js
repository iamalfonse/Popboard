function clearText(field){
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;

}

$(document).ready(function(){  
    //settings on top  
    var domain = 'http://davidwalsh.name/';  
    var initialPosts = <?php echo get_posts(0,$_SESSION['posts_start']); ?>;  
    //function that creates posts  
    var postHandler = function(postsJSON) {  
        $.each(postsJSON,function(i,post) {  
            //post url  
            var postURL = '' + domain + post.post_name;  
            var id = 'post-' + post.ID;  
            //create the HTML  
            $('<div></div>')  
            .addClass('post')  
            .attr('id',id)  
            //generate the HTML  
            .html('<a href="' + postURL + '" class="post-title">' + post.post_title + '</a><p class="post-content">' + post.post_content + '<br /><a href="' + postURL + '" class="post-more">Read more...</a></p>')  
            .click(function() {  
                window.location = postURL;  
            })  
            //inject into the container  
            .appendTo($('#posts'))  
            .hide()  
            .slideDown(250,function() {  
                if(i == 0) {  
                    $.scrollTo($('div#' + id));  
                }  
            });  
        });  
    };  
    //place the initial posts in the page  
    postHandler(initialPosts);  
    //first, take care of the "load more"  
    //when someone clicks on the "load more" DIV  
    var start = <?php echo $_SESSION['posts_start']; ?>;  
    var desiredPosts = <?php echo $number_of_posts; ?>;  
    var loadMore = $('#load-more');  
    //load event / ajax  
    loadMore.click(function(){  
        //add the activate class and change the message  
        loadMore.addClass('activate').text('Loading...');  
        //begin the ajax attempt  
        $.ajax({  
            url: 'load-more.php',  
            data: {  
                'start': start,  
                'desiredPosts': desiredPosts  
            },  
            type: 'get',  
            dataType: 'json',  
            cache: false,  
            success: function(responseJSON) {  
                //reset the message  
                loadMore.text('Load More');  
                //increment the current status  
                start += desiredPosts;  
                //add in the new posts  
                postHandler(responseJSON);  
            },  
            //failure class  
            error: function() {  
                //reset the message  
                loadMore.text('Oops! Try Again.');  
            },  
            //complete event  
            complete: function() {  
                //remove the spinner  
                loadMore.removeClass('activate');  
            }  
        });  
    });  
});