$(document).ready(function(){

	//for signup area
	if($('#signupform').length){
		$('#signupform').validate();
	}

	//make sure images are formatted properly on posts
	$('body #container .blogbottom p img').css({'max-width':'720px', 'height':'auto'});
	

    $('body').on('click', '.btn.cancel', function(){
    	$('.error-overlay').remove();
    });
    $('body').on('click', '.btn.confirm', function(){
    	var linkurl = $(this).attr('href');
    	window.location(linkurl);
    });
	
    
	
	//init show posts
    show_posts();

	//load more posts by scrolling down
	$(window).scroll(function(){
		show_posts();
    }); 
	
	$('.load-more').click(function(){
		load_more_posts();
	});

    $('body').on('click', '.deletebtn a', function(event){
    	event.preventDefault();
    	//show error box
    	console.log('delete btnclicked');
    	var linkurl = $(this).attr('href');
    	show_delete_dialogue(linkurl);
    });
	
	enable_liking();
    

	

});//end document.ready

clearText = function(field){
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}

//load more posts
var processing = false;
var starting = 3; //start of posts


load_more_posts = function() {
	processing = true;
	$.ajax({
		url: "../get_posts.php",
		datatype: "html",
		data: {start: starting, limit: '3'},
		beforeSend: function() {
		    $('.load-more').html('<img src="/images/loadmorespinner.gif" />');
		},
		success: function(data) {
			if(data == 0){
				$('.load-more').html('No More Posts');
			}else {
				$("#right").append(data);
				starting = starting + 3;
				//console.log('starting: ', starting);
				$('.load-more').html('Load More');
				disable_liking();
				enable_liking();
			}
			processing = false;
		}
	});
}

show_delete_dialogue = function(linkurl){
	$('body').append('<div class="error-overlay"><div class="error-container"><p>Are you sure you want to do that?</p><button class="btn cancel">Cancel</button><button class="btn confirm" href="'+linkurl+'">Confirm</button></div></div>');
};

enable_liking = function() {
	//add likes to a post
	$('.likes').on('click', function(){
		//console.log('clicked');

        var this = $(this);
		var post_id = this.parents('.postitem').find('h3').attr('id');
        //console.log('blogtop h3 id: ', post_id);
		$.ajax({
			url: "like_post.php?postid="+post_id,
			datatype: "html",
			//data: {postid: post_id},
			beforeSend: function() {
				this.html('wait...');
			},
			success: function(data) {
			    this.html(data);
			}
		});
	});
}
disable_liking = function() {
	$('.likes').off();
}

show_posts = function(){
	if(processing){ 
		return;
	}
	// if at bottom of blogwall, add new content:
	if ($(window).scrollTop() >= ($(document).height() - $(window).height())-200 && $('body.posts').length){
		load_more_posts();
	}
}
