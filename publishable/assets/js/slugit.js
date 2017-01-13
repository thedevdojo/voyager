/** slug + title input **/
if( $('[name="slug"]').length && $('[name="title"]').length ){
    $('[name="title"]').on('keyup', function() {
        $('[name="slug"]').val(getSlug($(this).val())) ;
    });
}
/** slug + name input **/
if( $('[name="slug"]').length && $('[name="name"]').length ){
    $('[name="name"]').on('keyup', function() {
        $('[name="slug"]').val(getSlug($(this).val())) ;
    });
}