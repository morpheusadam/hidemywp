<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_firewall_query_string extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Query String";
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'firewall_query_string',
                                                                    'label'         =>  __('Query String Rules',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Add Firewall rules for Query String.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Query String Rules',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The Query String is the portion of a URL where data is passed to a server application and/or back-end database.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />" . __(" Typical URL containing a query string is as follows:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><code>--domain--/over/there?name=ferret</code>" .
                                                                                                                                "<br /><br />"  . __("The arguments of an URL are the typical way for hackers to attempt to break into a server. This firewall gives your site a super strong layer of protection at the server level. So bad requests are blocked without having to load up PHP, MySQL, and everything else.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /></br />" .
                                                                                                                                "<br /><br />"  . __("The firewall protects against the following type of attacks:",    'wp-hide-security-enhancer') .
                                                                                                                                "<ul><li>" . __("HTTP Response Splitting",    'wp-hide-security-enhancer') . "</li>" .
                                                                                                                                    "<li>" .__("XSS) Cross-Site Scripting",    'wp-hide-security-enhancer') ."</li>" . 
                                                                                                                                    "<li>" .__("Cache Poisoning",    'wp-hide-security-enhancer') ."</li>" .
                                                                                                                                    "<li>" .__("Dual-Header Exploits",    'wp-hide-security-enhancer') ."</li>" .
                                                                                                                                    "<li>" .__("SQL/PHP/Code Injection",    'wp-hide-security-enhancer') ."</li>" .
                                                                                                                                    "<li>" .__("File Injection/Inclusion",    'wp-hide-security-enhancer') ."</li>" .
                                                                                                                                    "<li>" .__("Null Byte Injection",    'wp-hide-security-enhancer') ."</li>" .
                                                                                                                                    "<li>" .__("WordPress exploits such as revslider, timthumb, fckeditor, et al",    'wp-hide-security-enhancer') ."</li>" .
                                                                                                                                    "<li>" .__("Exploits such as c99shell, phpshell, remoteview, site copier, et al",    'wp-hide-security-enhancer') ."</li>" .
                                                                                                                                    "<li>" .__("PHP information leakage",    'wp-hide-security-enhancer') . "</li></ul>" .
                                                                                                                                    "<br /><span class='important'>" . __('After activating the firewall, test everything thoroughly, to ensure none of the rules block the site functionality.', 'wp-hide-security-enhancer') ."</span>",
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/how-wp-hide-pro-firewall-protects-your-site/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    
                                                                    'processing_order'  =>  100
                                                                    
                                                                    );
                                                                    
                    
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _callback_saved_firewall_query_string ( $saved_field_data )
                {
                    $processing_response    =   array();
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
       
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $processing_response['rewrite'] = <<<EOT
                            
# 7G FIREWALL v1.5 20211103
# @ https://perishablepress.com/7g-firewall/
  
# 7G:[QUERY STRING]
<IfModule mod_rewrite.c>

    RewriteCond %{QUERY_STRING} ([a-z0-9]{2000,}) [NC,OR]
    RewriteCond %{QUERY_STRING} (/|%2f)(:|%3a)(/|%2f) [NC,OR]
    RewriteCond %{QUERY_STRING} (order(\s|%20)by(\s|%20)1--) [NC,OR]
    RewriteCond %{QUERY_STRING} (/|%2f)(\*|%2a)(\*|%2a)(/|%2f) [NC,OR]
    RewriteCond %{QUERY_STRING} (`|<|>|\^|\|\\\\|0x00|%00|%0d%0a) [NC,OR]
    RewriteCond %{QUERY_STRING} (ckfinder|fck|fckeditor|fullclick) [NC,OR]
    RewriteCond %{QUERY_STRING} ((.*)header:|(.*)set-cookie:(.*)=) [NC,OR]
    RewriteCond %{QUERY_STRING} (cmd|command)(=|%3d)(chdir|mkdir)(.*)(x20) [NC,OR]
    RewriteCond %{QUERY_STRING} (globals|mosconfig([a-z_]{1,22})|request)(=|\[) [NC,OR]
    RewriteCond %{QUERY_STRING} (/|%2f)((wp-)?config)((\.|%2e)inc)?((\.|%2e)php) [NC,OR]
    RewriteCond %{QUERY_STRING} (thumbs?(_editor|open)?|tim(thumbs?)?)((\.|%2e)php) [NC,OR]
    RewriteCond %{QUERY_STRING} (absolute_|base|root_)(dir|path)(=|%3d)(ftp|https?) [NC,OR]
    RewriteCond %{QUERY_STRING} (localhost|loopback|127(\.|%2e)0(\.|%2e)0(\.|%2e)1) [NC,OR]
    RewriteCond %{QUERY_STRING} (s)?(ftp|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215)) [NC,OR]
    RewriteCond %{QUERY_STRING} (\.|20)(get|the)(_|%5f)(permalink|posts_page_url)(\(|%28) [NC,OR]
    RewriteCond %{QUERY_STRING} ((boot|win)((\.|%2e)ini)|etc(/|%2f)passwd|self(/|%2f)environ) [NC,OR]
    RewriteCond %{QUERY_STRING} (((/|%2f){3,3})|((\.|%2e){3,3})|((\.|%2e){2,2})(/|%2f|%u2215)) [NC,OR]
    RewriteCond %{QUERY_STRING} (benchmark|char|exec|fopen|function|html)(.*)(\(|%28)(.*)(\)|%29) [NC,OR]
    RewriteCond %{QUERY_STRING} (php)([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}) [NC,OR]
    RewriteCond %{QUERY_STRING} (e|%65|%45)(v|%76|%56)(a|%61|%31)(l|%6c|%4c)(.*)(\(|%28)(.*)(\)|%29) [NC,OR]
    RewriteCond %{QUERY_STRING} (/|%2f)(=|%3d|$&|_mm|cgi(\.|-)|inurl(:|%3a)(/|%2f)|(mod|path)(=|%3d)(\.|%2e)) [NC,OR]
    RewriteCond %{QUERY_STRING} (<|%3c)(.*)(e|%65|%45)(m|%6d|%4d)(b|%62|%42)(e|%65|%45)(d|%64|%44)(.*)(>|%3e) [NC,OR]
    RewriteCond %{QUERY_STRING} (<|%3c)(.*)(i|%69|%49)(f|%66|%46)(r|%72|%52)(a|%61|%41)(m|%6d|%4d)(e|%65|%45)(.*)(>|%3e) [NC,OR]
    RewriteCond %{QUERY_STRING} (<|%3c)(.*)(o|%4f|%6f)(b|%62|%42)(j|%4a|%6a)(e|%65|%45)(c|%63|%43)(t|%74|%54)(.*)(>|%3e) [NC,OR]
    RewriteCond %{QUERY_STRING} (<|%3c)(.*)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(.*)(>|%3e) [NC,OR]
    RewriteCond %{QUERY_STRING} (\+|%2b|%20)(d|%64|%44)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(t|%74|%54)(e|%65|%45)(\+|%2b|%20) [NC,OR]
    RewriteCond %{QUERY_STRING} (\+|%2b|%20)(i|%69|%49)(n|%6e|%4e)(s|%73|%53)(e|%65|%45)(r|%72|%52)(t|%74|%54)(\+|%2b|%20) [NC,OR]
    RewriteCond %{QUERY_STRING} (\+|%2b|%20)(s|%73|%53)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(c|%63|%43)(t|%74|%54)(\+|%2b|%20) [NC,OR]
    RewriteCond %{QUERY_STRING} (\+|%2b|%20)(u|%75|%55)(p|%70|%50)(d|%64|%44)(a|%61|%41)(t|%74|%54)(e|%65|%45)(\+|%2b|%20) [NC,OR]
    RewriteCond %{QUERY_STRING} (\\\\x00|(\"|%22|\'|%27)?0(\"|%22|\'|%27)?(=|%3d)(\"|%22|\'|%27)?0|cast(\(|%28)0x|or%201(=|%3d)1) [NC,OR]
    RewriteCond %{QUERY_STRING} (g|%67|%47)(l|%6c|%4c)(o|%6f|%4f)(b|%62|%42)(a|%61|%41)(l|%6c|%4c)(s|%73|%53)(=|\[|%[0-9A-Z]{0,2}) [NC,OR]
    RewriteCond %{QUERY_STRING} (_|%5f)(r|%72|%52)(e|%65|%45)(q|%71|%51)(u|%75|%55)(e|%65|%45)(s|%73|%53)(t|%74|%54)(=|\[|%[0-9A-Z]{2,}) [NC,OR]
    RewriteCond %{QUERY_STRING} (j|%6a|%4a)(a|%61|%41)(v|%76|%56)(a|%61|%31)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(:|%3a)(.*)(;|%3b|\)|%29) [NC,OR]
    RewriteCond %{QUERY_STRING} (b|%62|%42)(a|%61|%41)(s|%73|%53)(e|%65|%45)(6|%36)(4|%34)(_|%5f)(e|%65|%45|d|%64|%44)(e|%65|%45|n|%6e|%4e)(c|%63|%43)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(.*)(\()(.*)(\)) [NC,OR]
    RewriteCond %{QUERY_STRING} (@copy|\\\$_(files|get|post)|allow_url_(fopen|include)|auto_prepend_file|blexbot|browsersploit|(c99|php)shell|curl(_exec|test)|disable_functions?|document_root|elastix|encodeuricom|exploit|fclose|fgets|file_put_contents|fputs|fsbuff|fsockopen|gethostbyname|grablogin|hmei7|input_file|null|open_basedir|outfile|passthru|phpinfo|popen|proc_open|quickbrute|remoteview|root_path|safe_mode|shell_exec|site((.){0,2})copier|sux0r|trojan|user_func_array|wget|xertive) [NC,OR]
    RewriteCond %{QUERY_STRING} (;|<|>|\'|\"|\)|%0a|%0d|%22|%27|%3c|%3e|%00)(.*)(/\*|alter|base64|benchmark|cast|concat|convert|create|encode|declare|delete|drop|insert|md5|request|script|select|set|union|update) [NC,OR]
    RewriteCond %{QUERY_STRING} ((\+|%2b)(concat|delete|get|select|union)(\+|%2b)) [NC,OR]
    RewriteCond %{QUERY_STRING} (union)(.*)(select)(.*)(\(|%28) [NC,OR]
    RewriteCond %{QUERY_STRING} (concat|eval)(.*)(\(|%28) [NC]

    RewriteRule .* - [F,L]

</IfModule>
EOT;


                        }
                            
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            //Not implemented
                        }
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            $rewrite        =   array();
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                                                           
                            if( ! is_multisite() )
                                $rewrite_list['blog_id'] =   '1';
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'firewall';
                            $rewrite_list['description'] =   'Firewall Query String';
                                      
                            $rewrite_data   =   <<<'EOT'
# 7G FIREWALL - NGINX v1.6
# @ https://perishablepress.com/7g-firewall-nginx/
map $query_string $bad_querystring_7g {
    
    default 0;
    
    "~*([a-z0-9]{2000,})" 1;
    "~*(/|%2f)(:|%3a)(/|%2f)" 2;
    "~*(order(\s|%20)by(\s|%20)1--)" 3;
    "~*(/|%2f)(\*|%2a)(\*|%2a)(/|%2f)" 4;
    "~*(ckfinder|fckeditor|fullclick)" 5;
    "~*(`|<|>|\^|\|\\|0x00|%00|%0d%0a)" 6;
    "~*((.*)header:|(.*)set-cookie:(.*)=)" 7;
    "~*(localhost|127(\.|%2e)0(\.|%2e)0(\.|%2e)1)" 8;
    "~*(cmd|command)(=|%3d)(chdir|mkdir)(.*)(x20)" 9;
    "~*(globals|mosconfig([a-z_]{1,22})|request)(=|\[)" 10;
    "~*(/|%2f)((wp-)?config)((\.|%2e)inc)?((\.|%2e)php)" 11;
    "~*(thumbs?(_editor|open)?|tim(thumbs?)?)((\.|%2e)php)" 12;
    "~*(absolute_|base|root_)(dir|path)(=|%3d)(ftp|https?)" 13;
    "~*(s)?(ftp|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215))" 14;
    "~*(\.|20)(get|the)(_|%5f)(permalink|posts_page_url)(\(|%28)" 15;
    "~*((boot|win)((\.|%2e)ini)|etc(/|%2f)passwd|self(/|%2f)environ)" 16;
    "~*(((/|%2f){3,3})|((\.|%2e){3,3})|((\.|%2e){2,2})(/|%2f|%u2215))" 17;
    "~*(benchmark|char|exec|fopen|function|html)(.*)(\(|%28)(.*)(\)|%29)" 18;
    "~*(php)([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})" 19;
    "~*(e|%65|%45)(v|%76|%56)(a|%61|%31)(l|%6c|%4c)(.*)(\(|%28)(.*)(\)|%29)" 20;
    "~*(/|%2f)(=|%3d|$&|_mm|cgi(\.|-)|inurl(:|%3a)(/|%2f)|(mod|path)(=|%3d)(\.|%2e))" 21;
    "~*(<|%3c)(.*)(e|%65|%45)(m|%6d|%4d)(b|%62|%42)(e|%65|%45)(d|%64|%44)(.*)(>|%3e)" 22;
    "~*(<|%3c)(.*)(i|%69|%49)(f|%66|%46)(r|%72|%52)(a|%61|%41)(m|%6d|%4d)(e|%65|%45)(.*)(>|%3e)" 23;
    "~*(<|%3c)(.*)(o|%4f|%6f)(b|%62|%42)(j|%4a|%6a)(e|%65|%45)(c|%63|%43)(t|%74|%54)(.*)(>|%3e)" 24;
    "~*(<|%3c)(.*)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(.*)(>|%3e)" 25;
    "~*(\+|%2b|%20)(d|%64|%44)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(t|%74|%54)(e|%65|%45)(\+|%2b|%20)" 26;
    "~*(\+|%2b|%20)(i|%69|%49)(n|%6e|%4e)(s|%73|%53)(e|%65|%45)(r|%72|%52)(t|%74|%54)(\+|%2b|%20)" 27;
    "~*(\+|%2b|%20)(s|%73|%53)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(c|%63|%43)(t|%74|%54)(\+|%2b|%20)" 28;
    "~*(\+|%2b|%20)(u|%75|%55)(p|%70|%50)(d|%64|%44)(a|%61|%41)(t|%74|%54)(e|%65|%45)(\+|%2b|%20)" 29;
    "~*(\\x00|(\"|%22|\'|%27)?0(\"|%22|\'|%27)?(=|%3d)(\"|%22|\'|%27)?0|cast(\(|%28)0x|or%201(=|%3d)1)" 30;
    "~*(g|%67|%47)(l|%6c|%4c)(o|%6f|%4f)(b|%62|%42)(a|%61|%41)(l|%6c|%4c)(s|%73|%53)(=|\[|%[0-9A-Z]{0,2})" 31;
    "~*(_|%5f)(r|%72|%52)(e|%65|%45)(q|%71|%51)(u|%75|%55)(e|%65|%45)(s|%73|%53)(t|%74|%54)(=|\[|%[0-9A-Z]{2,})" 32;
    "~*(j|%6a|%4a)(a|%61|%41)(v|%76|%56)(a|%61|%31)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(:|%3a)(.*)(;|%3b|\)|%29)" 33;
    "~*(b|%62|%42)(a|%61|%41)(s|%73|%53)(e|%65|%45)(6|%36)(4|%34)(_|%5f)(e|%65|%45|d|%64|%44)(e|%65|%45|n|%6e|%4e)(c|%63|%43)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(.*)(\()(.*)(\))" 34;
    "~*(@copy|\$_(files|get|post)|allow_url_(fopen|include)|auto_prepend_file|blexbot|browsersploit|(c99|php)shell|curl(_exec|test)|disable_functions?|document_root|elastix|encodeuricom|exploit|fclose|fgets|file_put_contents|fputs|fsbuff|fsockopen|gethostbyname|grablogin|hmei7|input_file|open_basedir|outfile|passthru|phpinfo|popen|proc_open|quickbrute|remoteview|root_path|safe_mode|shell_exec|site((.){0,2})copier|sux0r|trojan|user_func_array|wget|xertive)" 35;
    "~*(;|<|>|\'|\"|\)|%0a|%0d|%22|%27|%3c|%3e|%00)(.*)(/\*|alter|base64|benchmark|cast|concat|convert|create|encode|declare|delete|drop|insert|md5|request|script|select|set|union|update)" 36;
    "~*((\+|%2b)(concat|delete|get|select|union)(\+|%2b))" 37;
    "~*(union)(.*)(select)(.*)(\(|%28)" 38;
    "~*(concat|eval)(.*)(\(|%28)" 39;
    
}  
EOT;
                                  
                                  
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            $rewrite[]  =   $rewrite_list;
                            
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                                                           
                            if( ! is_multisite() )
                                $rewrite_list['blog_id'] =   '1';
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'firewall_conditionals';
                            $rewrite_list['description'] =   'Firewall Query String conditionals';
                                      
                            $rewrite_data   =   '

if ($bad_querystring_7g) {

    set $7g_reason "${7g_reason}:bad_querystring_${bad_querystring_7g}:"; 
    set $7g_drop_bad_query_string 1;

}

if ($7g_drop_bad_query_string) {

    set $args "${7g_reason}";
    set $7g_drop 1;

}                           
                            ';
                                  
                                  
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
     
                            $rewrite[]  =   $rewrite_list; 
                            
                            $processing_response['rewrite'] =   $rewrite;
                        }
                                
                    return  $processing_response;   
                }

        }
?>