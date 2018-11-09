
        @if(property_exists($row->details, 'options') && !empty($row->details->options->{$dataTypeContent->{$row->field}}))
            <?php echo $row->details->options->{$dataTypeContent->{$row->field}};?>
        @endif
