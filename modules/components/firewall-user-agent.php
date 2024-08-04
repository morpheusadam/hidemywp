<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_firewall_user_agent extends WPH_module_component
        {
            function get_component_title()
                {
                    return "User Agent";
                }
                                    
            function get_module_component_settings()
                {
                                                                    
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'firewall_user_agent',
                                                                    'label'         =>  __('User Aget Rules',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Add Firewall rules for User Agent.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('User Aget Rules',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The User-Agent request header is a characteristic string that lets servers and network peers identify the application, operating system, vendor, and/or version of the requesting user agent.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />" . __(" Typical legit user agent is:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><code>User-Agent: &ltproduct&gt / &ltproduct-version&gt &ltcomment&gt</code>" .
                                                                                                                      
                                                                                                                                "<br /><br />"  . __("Common format for legit user agent on web browsers:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><code>User-Agent: Mozilla/5.0 (&ltsystem-information&gt) &ltplatform&gt (&ltplatform-details&gt) &ltextensions&gt</code>" .
                                                                                                                                "<br />"  . __("Many malware sites, boots and hack scanners use their user agent. Using this firewall type, they get blocked before reaching your site.",    'wp-hide-security-enhancer') ,
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
                
                
                
                          
                
            function _callback_saved_firewall_user_agent ( $saved_field_data )
                {
                    $processing_response    =   array();
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
       
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $processing_response['rewrite'] = <<<EOT
                            
# 7G FIREWALL v1.5 20211103
# @ https://perishablepress.com/7g-firewall/
  
# 7G:[HTTP_USER_AGENT]
<IfModule mod_rewrite.c>
        
    RewriteCond %{HTTP_USER_AGENT} ([a-z0-9]{2000,}) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} (&lt;|%0a|%0d|%27|%3c|%3e|%00|0x00) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} (ahrefs|alexibot|majestic|mj12bot|rogerbot) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} ((c99|php|web)shell|remoteview|site((.){0,2})copier) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} (econtext|eolasbot|eventures|liebaofast|nominet|oppo\sa33) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} (base64_decode|bin/bash|disconnect|eval|lwp-download|unserialize|\\\\\\x22) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} (acapbot|acoonbot|asterias|attackbot|backdorbot|becomebot|binlar|blackwidow|blekkobot|blexbot|blowfish|bullseye|bunnys|butterfly|careerbot|casper|checkpriv|cheesebot|cherrypick|chinaclaw|choppy|clshttp|cmsworld|copernic|copyrightcheck|cosmos|crescent|cy_cho|datacha|demon|diavol|discobot|dittospyder|dotbot|dotnetdotcom|dumbot|emailcollector|emailsiphon|emailwolf|extract|eyenetie|feedfinder|flaming|flashget|flicky|foobot|g00g1e|getright|gigabot|go-ahead-got|gozilla|grabnet|grafula|harvest|heritrix|httrack|icarus6j|jetbot|jetcar|jikespider|kmccrew|leechftp|libweb|linkextractor|linkscan|linkwalker|loader|masscan|miner|mechanize|morfeus|moveoverbot|netmechanic|netspider|nicerspro|nikto|ninja|nutch|octopus|pagegrabber|petalbot|planetwork|postrank|proximic|purebot|pycurl|python|queryn|queryseeker|radian6|radiation|realdownload|scooter|seekerspider|semalt|siclab|sindice|sistrix|sitebot|siteexplorer|sitesnagger|skygrid|smartdownload|snoopy|sosospider|spankbot|spbot|sqlmap|stackrambler|stripper|sucker|surftbot|sux0r|suzukacz|suzuran|takeout|teleport|telesoft|true_robots|turingos|turnit|vampire|vikspider|voideye|webleacher|webreaper|webstripper|webvac|webviewer|webwhacker|winhttp|wwwoffle|woxbot|xaldon|xxxyy|yamanalab|yioopbot|youda|zeus|zmeu|zune|zyborg) [NC]  
    
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
map $http_user_agent $bad_bot_7g {
    
    default 0;
    
    "~*([a-z0-9]{2000,})" 1;
    "~*(&lt;|%0a|%0d|%27|%3c|%3e|%00|0x00)" 2;
    "~*(ahrefs|alexibot|majestic|mj12bot|rogerbot)" 3;
    "~*((c99|php|web)shell|remoteview|site((.){0,2})copier)" 4;
    "~*(econtext|eolasbot|eventures|liebaofast|nominet|oppo\sa33)" 5;
    "~*(base64_decode|bin/bash|disconnect|eval|lwp-download|unserialize|\\\x22)" 6;
    "~*(acapbot|acoonbot|asterias|attackbot|backdorbot|becomebot|binlar|blackwidow|blekkobot|blexbot|blowfish|bullseye|bunnys|butterfly|careerbot|casper|checkpriv|cheesebot|cherrypick|chinaclaw|choppy|clshttp|cmsworld|copernic|copyrightcheck|cosmos|crescent|cy_cho|datacha|demon|diavol|discobot|dittospyder|dotbot|dotnetdotcom|dumbot|emailcollector|emailsiphon|emailwolf|extract|eyenetie|feedfinder|flaming|flashget|flicky|foobot|g00g1e|getright|gigabot|go-ahead-got|gozilla|grabnet|grafula|harvest|heritrix|httrack|icarus6j|jetbot|jetcar|jikespider|kmccrew|leechftp|libweb|linkextractor|linkscan|linkwalker|loader|masscan|miner|mechanize|morfeus|moveoverbot|netmechanic|netspider|nicerspro|nikto|ninja|nutch|octopus|pagegrabber|petalbot|planetwork|postrank|proximic|purebot|pycurl|python|queryn|queryseeker|radian6|radiation|realdownload|scooter|seekerspider|semalt|siclab|sindice|sistrix|sitebot|siteexplorer|sitesnagger|skygrid|smartdownload|snoopy|sosospider|spankbot|spbot|sqlmap|stackrambler|stripper|sucker|surftbot|sux0r|suzukacz|suzuran|takeout|teleport|telesoft|true_robots|turingos|turnit|vampire|vikspider|voideye|webleacher|webreaper|webstripper|webvac|webviewer|webwhacker|winhttp|wwwoffle|woxbot|xaldon|xxxyy|yamanalab|yioopbot|youda|zeus|zmeu|zune|zyborg)" 7;
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


if ($bad_bot_7g) {

    set $7g_reason "${7g_reason}:bad_bot_${bad_bot_7g}:"; 
    set $7g_drop_bad_bot 1;

} 

if ($7g_drop_bad_bot) {

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