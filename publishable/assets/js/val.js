$(document).ready(function(){
	$("[type='submit']").on('click', function(e){
		$($(this).parents('form')).submit(function(e){
			e.preventDefault();
			var form = $(this);
			var method = $(form).find('[name="_method"]').attr('value');

			if(method != 'DELETE' || method == undefined)
			{
				var url = $(this).attr('action');
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
						$(".form-group").removeClass("has-error");
						$(".help-block").empty();
					},
					success: function(d){
						$("body").css("cursor", "auto");
						if($(".help-block").length > 0)
						{
							$.each(d.errors, function(key, row){
								$("[name='"+key+"']").parents(".form-group").addClass("has-error").find(".help-block").text(row);
							});
						}
						else
						{
							$.each(d.errors, function(key, row){
								$("[name='"+key+"']").parents(".form-group").addClass("has-error").append("<span class='help-block'>"+row+"</span>");
							});
						}
					},
					error: function(){
						$(form).unbind("submit").submit();
					}
				});
			}
			else
			{
				$(form).unbind("submit").submit();
			}
		});
	});
});
