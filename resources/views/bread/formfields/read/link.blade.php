@php
$icon = (isset($options->icon)) ? "$options->icon" : "";
$type = (isset($options->type)) ? "$options->type" : "";
$result = parse_url($data->{$row->field});
$host = (isset($result['host'])) ? $result['host'] : false;
if($type == "google" && $host){
  $icon = "https://www.google.com/s2/favicons?domain=$host";
}else if($type == "fa" && $host){
  #this looks a little weird but we should really only include the css if it's needed.
  #If you know how to do this without breaking out of this block, please show.
  @endphp
    @section('css')
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @stop
  @php
}
$download = (isset($options->download)) ? "download" : "";
$hreflang = (isset($options->hreflang)) ? "hreflang=$options->hreflang" : "";
$media = (isset($options->media)) ? "media=$options->media" : "";
$ping = (isset($options->ping)) ? "ping=$options->ping" : "";
$rel = (isset($options->rel)) ? "rel=$options->rel" : "";
$atype = (isset($options->atype)) ? "type=$options->atype" : "";
$target = (isset($options->target)) ? "target=$options->target" : "";
$aclass = (isset($options->aclass)) ? "class=$options->aclass" : "";
$imgclass = (isset($options->imgclass)) ? "class=$options->imgclass" : "";
$hideurl = (isset($options->hideurl)) ? true : false;
@endphp
<a name="{{ $row->field }}" href="{{ $data->{$row->field} }}"
  {{ $download }}
  {{ $hreflang }}
  {{ $media }}
  {{ $ping  }}
  {{ $rel }}
  {{ $type }}
  {{ $target }}
  {{ $aclass }}
  >
  @if( !empty($icon) && ($type == "image" || $type == "google") )
    <img src="{{ $icon }}" {{ imgclass }} />
  @elseif( ( !empty($icon) && $type == "fa" ) )
    <span class="fa fa-{{ $icon }}"></span>
  @endif
  @if(!$hideurl)
    {{ $data->{$row->field} }}
  @endif
</a>
