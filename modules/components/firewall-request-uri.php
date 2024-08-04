<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_firewall_request_uri extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Request URI";
                }
                                    
            function get_module_component_settings()
                {
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'firewall_request_uri',
                                                                    'label'         =>  __('Request URI Rules',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Add Firewall rules for Request URI.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Request URI Rules',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The Request-URI is a Uniform Resource Identifier and identifies the resource upon which to apply the request. ",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />" . __(" Typical URI is as follows:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><code>--domain--/over/there/</code>" .
                                                                                                                      
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
                
                
                
            function _callback_saved_firewall_request_uri ( $saved_field_data )
                {
                    $processing_response    =   array();
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
       
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $processing_response['rewrite'] = <<<EOT
                            
# 7G FIREWALL v1.5 20211103
# @ https://perishablepress.com/7g-firewall/
  
# 7G:[REQUEST URI]
<IfModule mod_rewrite.c>
        
    RewriteCond %{REQUEST_URI} (\^|`|<|>|\\\\|\|) [NC,OR]
    RewriteCond %{REQUEST_URI} ([a-z0-9]{2000,}) [NC,OR]
    RewriteCond %{REQUEST_URI} (=?\\\\(\'|%27)/?)(\.) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(\*|\"|\'|\.|,|&|&amp;?)/?$ [NC,OR]
    RewriteCond %{REQUEST_URI} (\.)(php)(\()?([0-9]+)(\))?(/)?$ [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(vbulletin|boards|vbforum)(/)? [NC,OR]
    RewriteCond %{REQUEST_URI} /((.*)header:|(.*)set-cookie:(.*)=) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(ckfinder|fck|fckeditor|fullclick) [NC,OR]
    RewriteCond %{REQUEST_URI} (\.(s?ftp-?)config|(s?ftp-?)config\.) [NC,OR]
    RewriteCond %{REQUEST_URI} (\{0\}|\"?0\"?=\"?0|\(/\(|\.\.\.|\+\+\+|\\\\\\") [NC,OR]
    RewriteCond %{REQUEST_URI} (thumbs?(_editor|open)?|tim(thumbs?)?)(\.php) [NC,OR]
    RewriteCond %{REQUEST_URI} (\.|20)(get|the)(_)(permalink|posts_page_url)(\() [NC,OR]
    RewriteCond %{REQUEST_URI} (///|\?\?|/&&|/\*(.*)\*/|/:/|\\\\\\\\|0x00|%00|%0d%0a) [NC,OR]
    RewriteCond %{REQUEST_URI} (/%7e)(root|ftp|bin|nobody|named|guest|logs|sshd)(/) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(etc|var)(/)(hidden|secret|shadow|ninja|passwd|tmp)(/)?$ [NC,OR]
    RewriteCond %{REQUEST_URI} (s)?(ftp|http|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215)) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(=|\\$&?|&?(pws|rk)=0|_mm|_vti_|cgi(\.|-)?|(=|/|;|,)nt\.) [NC,OR]
    RewriteCond %{REQUEST_URI} (\.)(ds_store|htaccess|htpasswd|init?|mysql-select-db)(/)?$ [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(bin)(/)(cc|chmod|chsh|cpp|echo|id|kill|mail|nasm|perl|ping|ps|python|tclsh)(/)?$ [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(::[0-9999]|%3a%3a[0-9999]|127\.0\.0\.1|localhost|loopback|makefile|pingserver|wwwroot)(/)? [NC,OR]
    RewriteCond %{REQUEST_URI} (\(null\)|\{\\\$itemURL\}|cAsT\(0x|echo(.*)kae|etc/passwd|eval\(|self/environ|\+union\+all\+select) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)?j((\s)+)?a((\s)+)?v((\s)+)?a((\s)+)?s((\s)+)?c((\s)+)?r((\s)+)?i((\s)+)?p((\s)+)?t((\s)+)?(%3a|:) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(awstats|(c99|php|web)shell|document_root|error_log|listinfo|muieblack|remoteview|site((.){0,2})copier|sqlpatch|sux0r) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)((php|web)?shell|crossdomain|fileditor|locus7|nstview|php(get|remoteview|writer)|r57|remview|sshphp|storm7|webadmin)(.*)(\.|\() [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(author-panel|bitrix|class|database|(db|mysql)-?admin|filemanager|htdocs|httpdocs|https?|mailman|mailto|msoffice|mysql|_?php-my-admin(.*)|tmp|undefined|usage|var|vhosts|webmaster|www)(/) [NC,OR]
    RewriteCond %{REQUEST_URI} (base64_(en|de)code|benchmark|child_terminate|curl_exec|e?chr|eval|function|fwrite|(f|p)open|html|leak|passthru|p?fsockopen|phpinfo|posix_(kill|mkfifo|setpgid|setsid|setuid)|proc_(close|get_status|nice|open|terminate)|(shell_)?exec|system)(.*)(\()(.*)(\)) [NC,OR]
    RewriteCond %{REQUEST_URI} (/)(^$|00.temp00|0day|3index|3xp|70bex?|admin_events|bkht|(php|web)?shell|c99|config(\.)?bak|curltest|db|dompdf|filenetworks|hmei7|index\.php/index\.php/index|jahat|kcrew|keywordspy|libsoft|marg|mobiquo|mysql|nessus|php-?info|racrew|sql|vuln|(web-?|wp-)?(conf\b|config(uration)?)|xertive)(\.php) [NC,OR]
    RewriteCond %{REQUEST_URI} (\.)(7z|ab4|ace|afm|ashx|aspx?|bash|ba?k?|bin|bz2|cfg|cfml?|cgi|conf\b|config|ctl|dat|db|dist|dll|eml|engine|env|et2|exe|fec|fla|git|hg|inc|ini|inv|jsp|log|lqd|make|mbf|mdb|mmw|mny|module|old|one|orig|out|passwd|pdb|phtml|pl|profile|psd|pst|ptdb|pwd|py|qbb|qdf|rar|rdf|save|sdb|sql|sh|soa|svn|swf|swl|swo|swp|stx|tar|tax|tgz|theme|tls|tmd|wow|xtmpl|ya?ml|zlib)$ [NC]

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
                            $rewrite_list['description'] =   'Firewall User Agent';
                                      
                            $rewrite_data   =   <<<'EOT'
# 7G FIREWALL - NGINX v1.6
# @ https://perishablepress.com/7g-firewall-nginx/
map $request_uri $bad_request_7g {
    
    default 0;
    
    "~*(\^|`|<|>|\\|\|)" 1;
    "~*([a-z0-9]{2000,})" 2;
    "~*(=?\\\(\'|%27)/?)(\.)" 3;
    "~*(/)(\*|\"|\'|\.|,|&|&amp;?)/?$" 4;
    "~*(\.)(php)(\()?([0-9]+)(\))?(/)?$" 5;
    "~*(/)(vbulletin|boards|vbforum)(/)?" 6;
    "~*(/)((.*)header:|(.*)set-cookie:(.*)=)" 7;
    "~*(/)(ckfinder|fck|fckeditor|fullclick)" 8;
    "~*(\.(s?ftp-?)config|(s?ftp-?)config\.)" 9;
    "~*(\{0\}|\"?0\"?=\"?0|\(/\(|\.\.\.|\+\+\+|\\\")" 10;
    "~*(thumbs?(_editor|open)?|tim(thumbs?)?)(\.php)" 11;
    "~*(\.|20)(get|the)(_)(permalink|posts_page_url)(\()" 12;
    "~*(///|\?\?|/&&|/\*(.*)\*/|/:/|\\\\|0x00|%00|%0d%0a)" 13;
    "~*(/%7e)(root|ftp|bin|nobody|named|guest|logs|sshd)(/)" 14;
    "~*(/)(etc|var)(/)(hidden|secret|shadow|ninja|passwd|tmp)(/)?$" 15;
    "~*(s)?(ftp|http|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215))" 16;
    "~*(/)(=|\$&?|&?(pws|rk)=0|_mm|_vti_|cgi(\.|-)?|(=|/|;|,)nt\.)" 17;
    "~*(\.)(ds_store|htaccess|htpasswd|init?|mysql-select-db)(/)?$" 18;
    "~*(/)(bin)(/)(cc|chmod|chsh|cpp|echo|id|kill|mail|nasm|perl|ping|ps|python|tclsh)(/)?$" 19;
    "~*(/)(::[0-9999]|%3a%3a[0-9999]|127\.0\.0\.1|localhost|makefile|pingserver|wwwroot)(/)?" 20;
    "~*(\(null\)|\{\$itemURL\}|cAsT\(0x|echo(.*)kae|etc/passwd|eval\(|self/environ|\+union\+all\+select)" 21;
    "~*(/)?j((\s)+)?a((\s)+)?v((\s)+)?a((\s)+)?s((\s)+)?c((\s)+)?r((\s)+)?i((\s)+)?p((\s)+)?t((\s)+)?(%3a|:)" 22;
    "~*(/)(awstats|(c99|php|web)shell|document_root|error_log|listinfo|muieblack|remoteview|site((.){0,2})copier|sqlpatch|sux0r)" 23;
    "~*(/)((php|web)?shell|crossdomain|fileditor|locus7|nstview|php(get|remoteview|writer)|r57|remview|sshphp|storm7|webadmin)(.*)(\.|\()" 24;
    "~*(/)(author-panel|class|database|(db|mysql)-?admin|filemanager|htdocs|httpdocs|https?|mailman|mailto|msoffice|_?php-my-admin(.*)|tmp|undefined|usage|var|vhosts|webmaster|www)(/)" 25;
    "~*(base64_(en|de)code|benchmark|child_terminate|curl_exec|e?chr|eval|function|fwrite|(f|p)open|html|leak|passthru|p?fsockopen|phpinfo|posix_(kill|mkfifo|setpgid|setsid|setuid)|proc_(close|get_status|nice|open|terminate)|(shell_)?exec|system)(.*)(\()(.*)(\))" 26;
    "~*(/)(^$|00.temp00|0day|3index|3xp|70bex?|admin_events|bkht|(php|web)?shell|c99|config(\.)?bak|curltest|db|dompdf|filenetworks|hmei7|index\.php/index\.php/index|jahat|kcrew|keywordspy|libsoft|marg|mobiquo|mysql|nessus|php-?info|racrew|sql|vuln|(web-?|wp-)?(conf\b|config(uration)?)|xertive)(\.php)" 27;
    "~*(\.)(7z|ab4|ace|afm|ashx|aspx?|bash|ba?k?|bin|bz2|cfg|cfml?|cgi|conf\b|config|ctl|dat|db|dist|dll|eml|engine|env|et2|exe|fec|fla|git|hg|inc|ini|inv|jsp|log|lqd|make|mbf|mdb|mmw|mny|module|old|one|orig|out|passwd|pdb|phtml|pl|profile|psd|pst|ptdb|pwd|py|qbb|qdf|rar|rdf|save|sdb|sql|sh|soa|svn|swf|swl|swo|swp|stx|tar|tax|tgz|theme|tls|tmd|wow|xtmpl|ya?ml|zlib)$" 28;
    
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
                            $rewrite_list['description'] =   'Firewall User Agent conditionals';
                                      
                            $rewrite_data   =   '


if ($bad_request_7g) {

    set $7g_reason "${7g_reason}:bad_request_${bad_request_7g}:"; 
    set $7g_drop_bad_request 1;

}
if ($7g_drop_bad_request) {

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