[1mdiff --git a/resources/views/bread/browse.blade.php b/resources/views/bread/browse.blade.php[m
[1mindex 4c8a0c58..e2f7745d 100644[m
[1m--- a/resources/views/bread/browse.blade.php[m
[1m+++ b/resources/views/bread/browse.blade.php[m
[36m@@ -111,6 +111,20 @@[m
                                                                 {{ $item->{$row->field} }}[m
                                                             @endif[m
                                                         @endforeach[m
[32m+[m[32m                                                            {{--MULTIPLE CHECKBOX STARTS--}}[m
[32m+[m[32m                                                        @elseif($row->type == 'multiple-checkbox' && property_exists[m
[32m+[m[32m                                                        ($options,[m
[32m+[m[32m                                                        'options'))[m
[32m+[m[32m                                                            @if (@count(json_decode($data->{$row->field})) > 0)[m
[32m+[m[32m                                                                @foreach(json_decode($data->{$row->field}) as $item)[m
[32m+[m[32m                                                                    @if (@$options->options->{$item})[m
[32m+[m[32m                                                                        {{ $options->options->{$item} . (!$loop->last ? ', ' : '') }}[m
[32m+[m[32m                                                                    @endif[m
[32m+[m[32m                                                                @endforeach[m
[32m+[m[32m                                                            @else[m
[32m+[m[32m                                                                {{ __('voyager::generic.none') }}[m
[32m+[m[32m                                                            @endif[m
[32m+[m[32m                                                            {{--/MULTIPLE CHECKBOX STARTS--}}[m
 [m
                                                     @elseif(property_exists($options, 'options'))[m
                                                         @if (count(json_decode($data->{$row->field})) > 0)[m
[1mdiff --git a/src/Http/Controllers/Controller.php b/src/Http/Controllers/Controller.php[m
[1mindex 2be83977..c1c24ffb 100644[m
[1m--- a/src/Http/Controllers/Controller.php[m
[1m+++ b/src/Http/Controllers/Controller.php[m
[36m@@ -187,6 +187,9 @@[m [mabstract class Controller extends BaseController[m
             /********** CHECKBOX TYPE **********/[m
             case 'checkbox':[m
                 return (new Checkbox($request, $slug, $row, $options))->handle();[m
[32m+[m[32m            /********** Multiple CHECKBOX TYPE **********/[m
[32m+[m[32m            case 'multiple-checkbox':[m
[32m+[m[32m                return (new MultipleCheckbox($request, $slug, $row, $options))->handle();[m
             /********** FILE TYPE **********/[m
             case 'file':[m
                 return (new File($request, $slug, $row, $options))->handle();[m
[1mdiff --git a/src/VoyagerServiceProvider.php b/src/VoyagerServiceProvider.php[m
[1mindex d5fc037c..9c567fbc 100644[m
[1m--- a/src/VoyagerServiceProvider.php[m
[1m+++ b/src/VoyagerServiceProvider.php[m
[36m@@ -281,6 +281,7 @@[m [mclass VoyagerServiceProvider extends ServiceProvider[m
     {[m
         $formFields = [[m
             'checkbox',[m
[32m+[m[32m            'multiple_checkbox',[m
             'color',[m
             'date',[m
             'file',[m
