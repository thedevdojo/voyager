$(document).ready(function(){
	$(".form-edit-add").submit(function(e){
		e.preventDefault()

		var url = $(this).attr('action');
		var data = $(this).serializeArray();

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data,
			beforeSend: function(){
				$("body").css("cursor", "progress");
				$(".form-group").removeClass("has-error");
				$(".form-group").find(".help-block").text("");
			},
			success: function(d){
				$("body").css("cursor", "auto");
				$.each(d.errors, function(key, row){
					$("[name='"+key+"']").parents(".form-group").addClass("has-error").append("<span class='help-block'>"+row+"</span>");
				});
			},
			error: function(){
				$(".form-edit-add").unbind("submit").submit();
			}
		});
	});
});
