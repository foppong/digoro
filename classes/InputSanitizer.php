<?php
class InputSanitizer {

    public static function sanitizeData(&$data)
    {
        //regular expressions to strip out of all input
        $searches = array(
                            '/(style)(\s?)+(=)(\s?)+(.*)/i',
                            '/(expression)(\s?)+(\().*\)/i',
                            '/(alert)(\s?)+(\()/i',
                            '/(javascript)(\s?)+(\:)(\s?)+(.*)/i',
                            '/<script(.*?)>(.*?)<\/script>/is',
                            '/<script(.*?)>/is'
                    );

        if(is_array($data)) { //for an array clean the entire array of data
            foreach($data as $k => $value) {
                if(get_magic_quotes_gpc()) {
                    $value = stripslashes($value);
                }

                foreach($searches as $search) {
                    while(preg_match($search, $value)) {
                        $value = preg_replace($search, '', $value);
                    }
                }

                $data[$k] = str_replace('</script>', '', $value);
            }
        }
        else { //for a single value, clean the value
            if(get_magic_quotes_gpc()) {
                $data = stripslashes($data);
            }

            foreach($searches as $search) {
                while(preg_match($search, $data)) {
                    $data = preg_replace($search, '', $data);
                }
            }

            $data = str_replace('</script>', '', $data);
        }
    }
}