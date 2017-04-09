$(document).ready(function(){
	$(".form-edit-add").submit(function(e){
		e.preventDefault();

		var url = $(this).attr('action');
		var form = $(this);
		var data = new FormData(this);

		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: data,
			processData: false,
			contentType: false,
			beforeSend: function(){
				$("body").css("cursor", "progress");
				$("div").removeClass("has-error");
				$(".help-block").remove();
			},
			success: function(d){
				$("body").css("cursor", "auto");

				$.each(d.errors, function(key, row){
					$("[name='"+key+"']").parent().addClass("has-error");
					$("[name='"+key+"']").parent().append("<span class='help-block' style='color:#f96868'>"+row+"</span>")
				});
			},
			error: function(){
				$(form).unbind("submit").submit();
			}
		});
	});
});