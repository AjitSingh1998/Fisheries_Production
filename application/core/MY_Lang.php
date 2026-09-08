<?php  
(defined('BASEPATH')) OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class MY_Lang extends CI_Lang
{
    function __construct() {

        global $URI, $CFG, $IN;

        $config =& $CFG->config;

        $index_page    = $config['index_page'];
        $lang_ignore   = $config['lang_ignore'];
        $default_abbr  = $config['language_abbr'];
        $lang_uri_abbr = $config['lang_uri_abbr'];
        #exit('my_lang');   
        #print_r($URI); 
        /*if($index_page=='es')
        {
            #$config['index_page'] = 'es';
            #$config['lang_uri_abbr'] = 'es';
            #$IN->set_cookie('user_lang', 'es', $config['sess_expiration']);
            #$URI->uri_string = str_replace('es','en',$URI->uri_string);
            }
        else{
            #$config['index_page'] = 'en';
            #$config['lang_uri_abbr'] = 'en';
            #$IN->set_cookie('user_lang', 'en', $config['sess_expiration']);
            }
        /* get the language abbreviation from uri */
       $uri_abbr = $URI->segment(1);
        #$uri_abbr='es';    
        /* adjust the uri string leading slash */
        #print $URI->uri_string;
        $URI->uri_string = preg_replace("|^\/?|", '/', $URI->uri_string);



        if ($lang_ignore) {

            if (isset($lang_uri_abbr[$uri_abbr])) {

                /* set the language_abbreviation cookie */
                $IN->set_cookie('user_lang', $uri_abbr, $config['sess_expiration']);

            } else {

                /* get the language_abbreviation from cookie */
                 $lang_abbr = $IN->cookie($config['cookie_prefix'].'user_lang');

            }

            if (is_string($uri_abbr) && strlen($uri_abbr) == 2) {

                /* reset the uri identifier */
                 $index_page .= empty($index_page) ? '' : '/';
              //  exit('654');
                /* remove the invalid abbreviation */
                $URI->uri_string = preg_replace("|^\/?$uri_abbr\/?|", '', $URI->uri_string);

                /* redirect */
                header('Location: '.$config['base_url'].$index_page.$URI->uri_string);
                exit;
            }

        } else {

            /* set the language abbreviation */
            $lang_abbr = $uri_abbr;
        }

        /* check validity against config array */
        if (isset($lang_uri_abbr[$lang_abbr])) {


           /* reset uri segments and uri string */
           //$URI->_reindex_segments(array_shift($URI->segments)); # this is commented becasue this is giving error : @$hok : 09/August/2015
           $URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);

           /* set config language values to match the user language */
           $config['language'] = $lang_uri_abbr[$lang_abbr];
           $config['language_abbr'] = $lang_abbr;


           /* if abbreviation is not ignored */
           if ( ! $lang_ignore) {

                   /* check and set the uri identifier */
                   $index_page .= empty($index_page) ? $lang_abbr : "/$lang_abbr";

                /* reset the index_page value */
                $config['index_page'] = $index_page;
           }

           /* set the language_abbreviation cookie */               
           $IN->set_cookie('user_lang', $lang_abbr, $config['sess_expiration']);

        } else {

            /* if abbreviation is not ignored */   
            if ( ! $lang_ignore) {                   

                   /* check and set the uri identifier to the default value */    
                $index_page .= empty($index_page) ? $default_abbr : "/$default_abbr";

                if (is_string($lang_abbr) && strlen($lang_abbr) == 2) {

                    /* remove invalid abbreviation */
                    $URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);
                }
                /*echo '<pre>';
                print_r($_SERVER);
                print_r($config['base_url'].$index_page.$URI->uri_string);
                exit;*/
                $q = $_SERVER['QUERY_STRING'];
                if($q)
                    $q = "/?".$q;
                /* redirect */
                header('Location: '.$config['base_url'].$index_page.$URI->uri_string.$q);
                exit;
            }

            /* set the language_abbreviation cookie */                
            $IN->set_cookie('user_lang', $default_abbr, $config['sess_expiration']);
        }

        log_message('debug', "Language_Identifier Class Initialized");
    }
}

/* translate helper */
function t($line) {
    global $LANG;
//print_r($LANG);
//  exit;
    return ($t = $LANG->line($line)) ? $t : $line;
} 
function _t($line,$params=array()) {
    global $LANG;
    if($params){
        echo str_replace(array_keys($params),array_values($params),($t = $LANG->line($line)) ? $t : $line);
    }
    else
        echo ($t = $LANG->line($line)) ? $t : $line;
} 
?>