var Sweetalert = function() {
	"use strict";

	var initSweetAlert = function() {

		$(".basic-message").on("click", function(e) {
			swal({
				title: "Here's a message!",
				confirmButtonColor: "#007AFF"
			});
			e.preventDefault
		});

		$(".message-text-under").on("click", function(e) {
			swal({
				title: "Here's a message!",
				text: "It's pretty, isn't it?",
				confirmButtonColor: "#007AFF"
			});
			e.preventDefault
		});

		$(".success-message").on("click", function(e) {
			swal({
				title: "Good job!",
				text: "You clicked the button!",
				type: "success",
				confirmButtonColor: "#007AFF"
			});
			e.preventDefault
		});

		$(".warning-message").on("click", function(e) {
			swal({
				title: "Are you sure?",
				text: "You will not be able to recover this imaginary file!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#007AFF",
				confirmButtonText: "Yes, delete it!",
				closeOnConfirm: false
			}, function() {
				swal("Deleted!", "Your imaginary file has been deleted.", "success");
			});

			e.preventDefault
		});
		$(".warning-message-parameter").on("click", function(e) {
			swal({
				title: "Are you sure?",
				text: "You will not be able to recover this imaginary file!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				cancelButtonText: "No, cancel plx!",
				closeOnConfirm: false,
				closeOnCancel: false
			}, function(isConfirm) {
				if(isConfirm) {
					swal("Deleted!", "Your imaginary file has been deleted.", "success");
				} else {
					swal("Cancelled", "Your imaginary file is safe :)", "error");
				}
			});

			e.preventDefault
		});

		$(".message-custom-icon").on("click", function(e) {
			swal({
				title: "Sweet!",
				text: "Here's a custom image.",
				confirmButtonColor: "#007AFF",
				imageUrl: "http://i.imgur.com/4NZ6uLY.jpg"
			});

			e.preventDefault
		});
	};

	return {
		init: function() {
			initSweetAlert();
		}
	};
}();

jQuery(document).ready(function() {
	Sweetalert.init();
});