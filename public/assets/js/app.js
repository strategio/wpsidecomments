$ = jQuery;
jQuery(document).ready(function ($) {

	// First require it.
	SideComments = require('side-comments');

	// Insert attribs to <p>
	$(wpSideComments.contentSelector + ' p').each(function(i, el){
		$(el).addClass('commentable-section').attr('data-section-id', i);
	});

	// Remove original comments area
	$(wpSideComments.commentSelector).hide();


	// Then, create a new SideComments instance, passing in the wrapper element and the optional the current user and any existing comments.
	sideComments = new SideComments(wpSideComments.contentSelector, wpSideComments.currentUser, wpSideComments.existingComments);

	// Translations
	$('.commentable-section .add-comment').text(wpSideComments.translations.leaveAComment);
	$('.commentable-section .comment-box').attr('data-placeholder-content', wpSideComments.translations.leaveAComment + '...');
	$('.commentable-section .action-link.post').text(wpSideComments.translations.post);
	$('.commentable-section .action-link.cancel').text(wpSideComments.translations.cancel);

	// Case undefined user
	if(wpSideComments.currentUser == null) {
		$('.commentable-section .add-comment').text(wpSideComments.translations.logInOrSignInToComment).click(function(e) {
				e.preventDefault();
				window.location = wpSideComments.loginLink;
			});
	}

	// Listen to "commentPosted", and send a request to your backend to save the comment.
	// More about this event in the "docs" section.
	sideComments.on('commentPosted', function( comment ) {
		comment.wpNonce = wpSideComments.wpNonce;
		comment.postId = wpSideComments.postId;
	    $.ajax({
	        url: wpSideComments.url + '?action=addWPSideComment',
	        type: 'POST',
	        data: comment,
	        success: function( commentId ) {
	            // Once the comment is saved, you can insert the comment into the comment stream with "insertComment(comment)".
	            if(!isNaN(commentId)) {
	            	comment.commentId = commentId;
	            	//console.log(comment);
	            	sideComments.insertComment(comment);
	            } else {
	            	alert(commentId);
	            }
	            	
	        }
	    });
	});
	 
	// Listen to "commentDeleted" and send a request to your backend to delete the comment.
	// More about this event in the "docs" section.
	// sideComments.on('commentDeleted', function( comment ) {
	// 	comment.wpNonce = wpSideComments.wpNonce;
	// 	comment.postId = wpSideComments.postId;
	//     $.ajax({
	//         url: wpSideComments.url + '?action=removeWPSideComment',
	//         type: 'POST',
	//         data: comment,
	//         success: function( success ) {
	//             //removeComment(sectionId, commentId)
	//         }
	//     });
	// });
	

});