$(document).ready(function () {
	$.extend($.expr[':'], {
		unchecked: function (obj) {
			return ((obj.type == 'checkbox' || obj.type == 'radio') && !$(obj).is(':checked'));
		}
	});

	$(".menu_tree_view input:checkbox").on('change', function () {
		//alert('hello');
		$(this).closest('li').find('ul').find('input:checkbox').prop('checked', $(this).prop("checked"));

		/*for (var i = $('#tree').find('ul').length - 1; i >= 0; i--) {
			$('#tree').find('ul:eq(' + i + ')').prev('input:checkbox').prop('checked', function () {
				return $(this).next('ul').find('input:unchecked').length === 0 ? true : false;
			});
		}*/
	});
});

$(function () {
    $('.treeview ul').hide(600);
 
    $('.plus').on('click', function (e) {
        e.stopPropagation();
		var attribute_class=$(this).find('span').attr('class');
		if(attribute_class=='glyphicon glyphicon-minus-sign'){
			$(this).find('span').removeClass('glyphicon-minus-sign');
			$(this).find('span').addClass('glyphicon-plus-sign');
		}else{
			$(this).find('span').removeClass('glyphicon-plus-sign');
			$(this).find('span').addClass('glyphicon-minus-sign');
			
		}
        $(this).parent().children('ul').slideToggle();
    });
});