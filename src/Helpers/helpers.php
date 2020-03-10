<?php

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return TCG\Voyager\Facades\Voyager::setting($key, $default);
    }
}

if (!function_exists('menu')) {
    function menu($menuName, $type = null, array $options = [])
    {
        return TCG\Voyager\Facades\Voyager::model('Menu')->display($menuName, $type, $options);
    }
}

if (!function_exists('voyager_asset')) {
    function voyager_asset($path, $secure = null)
    {
        return route('voyager.voyager_assets').'?path='.urlencode($path);
    }
}

if (!function_exists('get_file_name')) {
    function get_file_name($name)
    {
        preg_match('/(_)([0-9])+$/', $name, $matches);
        if (count($matches) == 3) {
            return Illuminate\Support\Str::replaceLast($matches[0], '', $name).'_'.(intval($matches[2]) + 1);
        } else {
            return $name.'_1';
        }
    }
}

if (!function_exists('str_limit_html')) {
    function str_limit_html($string, $length, $end = '&hellip;')
    {
        // find all tags
        $tagPattern = '/(<\/?)([\w]*)(\s*[^>]*)>?|&[\w#]+;/i';
        // match html tags and entities
        preg_match_all($tagPattern, $string, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $i = 0;

        // loop through each found tag that is within the $length, add those characters to the len,
        // also track open and closed tags
        // $matches[$i][0] = the whole tag string  --the only applicable field for html enitities
        // IF its not matching an &htmlentity; the following apply
        // $matches[$i][1] = the start of the tag either '<' or '</'
        // $matches[$i][2] = the tag name
        // $matches[$i][3] = the end of the tag
        // $matches[$i][$j][0] = the string
        // $matches[$i][$j][1] = the str offest
        while (!empty($matches[$i]) && $matches[$i][0][1] < $length) {
            $length = $length + mb_strlen($matches[$i][0][0]);
            if (substr($matches[$i][0][0], 0, 1) === '&') {
                $length--;
            }

            // if $matches[$i][2] is undefined then its an html entity, want to ignore those for tag counting
            // ignore empty/singleton tags for tag counting
            if (!empty($matches[$i][2][0]) && !in_array($matches[$i][2][0], ['br', 'img', 'hr', 'input', 'param', 'link'])) {
                // double check
                if (substr($matches[$i][3][0], -1) !== '/' && substr($matches[$i][1][0], -1) !== '/') {
                    $openTags[] = $matches[$i][2][0];
                } elseif (end($openTags) === $matches[$i][2][0]) {
                    array_pop($openTags);
                }
            }

            $i++;
        }

        $closeTagString = '';

        if (!empty($openTags)) {
            $openTags = array_reverse($openTags);
            foreach ($openTags as $t) {
                $closeTagString .= "</$t>";
            }
        }

        if (mb_strlen($string) > $length) {
            // truncate with new len last word
            $truncated_html = rtrim(mb_substr($string, 0, $length));
            // finds last character
            $last_character = mb_substr($truncated_html, -1, 1);
            // trim punctuation
            if (in_array($last_character, ['.', ','])) {
                $truncated_html = mb_substr($truncated_html, 0, -1);
            }
            // add the end text and restore any open tags
            $truncated_html .= $end.$closeTagString;
        } else {
            $truncated_html = $string;
        }

        return $truncated_html;
    }
}
