<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();


/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_pafco_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ( $filearea === 'headerlogo1' ||
                                                      $filearea === 'headerlogo2' ||
                                                      $filearea === 'headerlogo3' ||
                                                      $filearea === 'headerlogo4' ||
                                                      $filearea === 'headerlogo_mobile' ||
                                                      $filearea === 'footerlogo1' ||
                                                      $filearea === 'heading_bg' ||
                                                      $filearea === 'login_bg' ||
                                                      $filearea === 'preloader_image' ||
                                                      $filearea === 'favicon' ||
                                                      $filearea === 'upload_font_eot' ||
                                                      $filearea === 'upload_font_woff2' ||
                                                      $filearea === 'upload_font_woff' ||
                                                      $filearea === 'upload_font_ttf' ||
                                                      $filearea === 'upload_font_svg' ||
                                                      $filearea === 'upload_font_secondary_eot' ||
                                                      $filearea === 'upload_font_secondary_woff2' ||
                                                      $filearea === 'upload_font_secondary_woff' ||
                                                      $filearea === 'upload_font_secondary_ttf' ||
                                                      $filearea === 'upload_font_secondary_svg' ||
                                                      $filearea === 'videoposter' ||
                                                      $filearea === 'videofile' ||
                                                      $filearea === 'banner'
                                                    )) {
        $theme = theme_config::load('pafco');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

// /**
//  * Returns the main SCSS content.
//  *
//  * @param theme_config $theme The theme config object.
//  * @return string
//  */
// function theme_pafco_get_main_scss_content($theme) {
//     global $CFG;
//
//     $scss = '';
//     $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
//     $fs = get_file_storage();
//
//     $context = context_system::instance();
//     // if ($filename == 'default.scss') {
//     //     // We still load the default preset files directly from the boost theme. No sense in duplicating them.
//     //     $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
//     // } else if ($filename == 'plain.scss') {
//     //     // We still load the default preset files directly from the boost theme. No sense in duplicating them.
//     //     $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
//     //
//     // } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_pafco', 'preset', 0, '/', $filename))) {
//     //     // This preset file was fetched from the file area for theme_pafco and not theme_boost (see the line above).
//     //     $scss .= $presetfile->get_content();
//     // } else {
//     //     // Safety fallback - maybe new installs etc.
//     //     $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
//     // }
//
//
//     // $scss .= file_get_contents($CFG->dirroot . '/theme/pafco/scss/pafco-boost-preprocess.scss');
//     $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/moodle.scss');
//     // $scss .= file_get_contents($CFG->dirroot . '/theme/pafco/scss/pafco-boost-postprocess.scss');
//
//     // // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
//     // $pre = file_get_contents($CFG->dirroot . '/theme/pafco/scss/pre.scss');
//     // // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
//     // $post = file_get_contents($CFG->dirroot . '/theme/pafco/scss/post.scss');
//
//     // Combine them together.
//     // return $pre . "\n" . $scss . "\n" . $post;
//     return $scss;
// }

/**
 * Copy the updated theme image to the correct location in dataroot for the image to be served
 * by /theme/image.php. Also clear theme caches.
 *
 * @param $settingname
 */
function theme_pafco_update_settings_images($settingname) {
    global $CFG, $OUTPUT;

    // The setting name that was updated comes as a string like 's_theme_pafco_loginbackgroundimage'.
    // We split it on '_' characters.
    $parts = explode('_', $settingname);
    // And get the last one to get the setting name..
    $settingname = end($parts);

    // Admin settings are stored in system context.
    $syscontext = context_system::instance();
    // This is the component name the setting is stored in.
    $component = 'theme_pafco';


    // This is the value of the admin setting which is the filename of the uploaded file.
    $filename = get_config($component, $settingname);
    // We extract the file extension because we want to preserve it.
    $extension = substr($filename, strrpos($filename, '.') + 1);

    // This is the path in the moodle internal file system.
    $fullpath = "/{$syscontext->id}/{$component}/{$settingname}/0{$filename}";

    // This location matches the searched for location in theme_config::resolve_image_location.
    $pathname = $CFG->dataroot . '/pix_plugins/theme/pafco/' . $settingname . '.' . $extension;

    // This pattern matches any previous files with maybe different file extensions.
    $pathpattern = $CFG->dataroot . '/pix_plugins/theme/pafco/' . $settingname . '.*';

    // Make sure this dir exists.
    @mkdir($CFG->dataroot . '/pix_plugins/theme/pafco/', $CFG->directorypermissions, true);

    // Delete any existing files for this setting.
    foreach (glob($pathpattern) as $filename) {
        @unlink($filename);
    }

    // Get an instance of the moodle file storage.
    $fs = get_file_storage();
    // This is an efficient way to get a file if we know the exact path.
    if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
        // We got the stored file - copy it to dataroot.
        $file->copy_content_to($pathname);
    }

    // Reset theme caches.
    theme_reset_all_caches();
}

function theme_pafco_process_css($css, $theme) {
    global $CFG;

    $tag = '[[pafco:pafco]]';
    $css = str_replace($tag, $CFG->wwwroot . '/theme/pafco', $css);

    $tag = '[[string:ccn_settings_menu]]';
    $css = str_replace($tag, get_string('ccn_settings_menu', 'theme_pafco'), $css);

    $tag = '[[string:ccn_page_settings_menu]]';
    $css = str_replace($tag, get_string('ccn_page_settings_menu', 'theme_pafco'), $css);

    $tag = '[[string:hidden]]';
    $css = str_replace($tag, get_string('hidden', 'theme_pafco'), $css);

    $setting = $theme->settings->color_gradient_start;
    $tag = '[[setting:color_gradient_start]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ff1053';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_gradient_end;
    $tag = '[[setting:color_gradient_end]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#3452ff';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_primary;
    $tag = '[[setting:color_primary]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#2441e7';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_primary_alternate;
    $tag = '[[setting:color_primary_alternate]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#192675';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_secondary;
    $tag = '[[setting:color_secondary]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ff1053';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_tertiary;
    $tag = '[[setting:color_tertiary]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#6c757d';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_background;
    $tag = '[[setting:color_background]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#f6f7f9';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_side_menu;
    $tag = '[[setting:color_side_menu]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#faece1';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_accent;
    $tag = '[[setting:color_accent]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#e35a9a';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_accent_2;
    $tag = '[[setting:color_accent_2]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#c75533';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_accent_3;
    $tag = '[[setting:color_accent_3]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#192675';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_accent_4;
    $tag = '[[setting:color_accent_4]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#f0d078';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_parallax;
    $tag = '[[setting:color_parallax]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#2441e7';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_header_style_2_top;
    $tag = '[[setting:color_header_style_2_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#000';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_header_style_2_bottom;
    $tag = '[[setting:color_header_style_2_bottom]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#141414';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_header_style_3_top;
    $tag = '[[setting:color_header_style_3_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#051925';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_header_style_4_top;
    $tag = '[[setting:color_header_style_4_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#3452ff';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_header_style_5;
    $tag = '[[setting:color_header_style_5]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ffffff';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_header_style_6_top;
    $tag = '[[setting:color_header_style_6_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#3452ff';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_1_top;
    $tag = '[[setting:color_footer_style_1_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#151515';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_1_bottom;
    $tag = '[[setting:color_footer_style_1_bottom]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#0a0a0a';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_2_top;
    $tag = '[[setting:color_footer_style_2_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#f9fafc';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_2_bottom;
    $tag = '[[setting:color_footer_style_2_bottom]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ebeef4';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_3_top;
    $tag = '[[setting:color_footer_style_3_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#f9fafc';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_3_middle;
    $tag = '[[setting:color_footer_style_3_middle]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ffffff';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_3_bottom;
    $tag = '[[setting:color_footer_style_3_bottom]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#fafafa';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_5_top;
    $tag = '[[setting:color_footer_style_5_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#0d2f81';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_5_bottom;
    $tag = '[[setting:color_footer_style_5_bottom]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#072670';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_6_all;
    $tag = '[[setting:color_footer_style_6_all]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#3f4449';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_7_top;
    $tag = '[[setting:color_footer_style_7_top]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ffffff';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->color_footer_style_7_bottom;
    $tag = '[[setting:color_footer_style_7_bottom]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ffffff';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->dashboard_tablet_1_color;
    $tag = '[[setting:dashboard_tablet_1_color]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#2441e7';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->dashboard_tablet_2_color;
    $tag = '[[setting:dashboard_tablet_2_color]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ff1053';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->dashboard_tablet_3_color;
    $tag = '[[setting:dashboard_tablet_3_color]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#00a78e';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->dashboard_tablet_4_color;
    $tag = '[[setting:dashboard_tablet_4_color]]';
    $replacement = $setting;
    if(is_null($replacement)){$replacement = '#ecd06f';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->heading_bg;
    $tag = '[[setting:heading_bg]]';
    $replacement = $theme->setting_file_url('heading_bg', 'heading_bg');
    if(is_null($replacement)){$replacement = $CFG->wwwroot . '/theme/pafco/images/background/inner-pagebg.jpg';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->login_bg;
    $tag = '[[setting:login_bg]]';
    $replacement = $theme->setting_file_url('login_bg', 'login_bg');
    if(is_null($replacement)){$replacement = $CFG->wwwroot . '/theme/pafco/images/background/inner-pagebg.jpg';}
    $css = str_replace($tag, $replacement, $css);

    $setting = $theme->settings->preloader_image;
    $tag = '[[setting:preloader_image]]';
    $replacement = $theme->setting_file_url('preloader_image', 'preloader_image');
    if(is_null($replacement)){$replacement = $CFG->wwwroot . '/theme/pafco/images/preloader.gif';}
    $css = str_replace($tag, $replacement, $css);

    if($theme->settings->primary_font == 1) {
        $font_src = '@font-face {
                     font-family: "IransSansBold";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSansBold';
    } else if($theme->settings->primary_font == 2){
        $font_src = '@font-face {
                     font-family: "IransSansFaNum";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSansFaNum';
    }else if($theme->settings->primary_font == 3){
        $font_src = '@font-face {
                     font-family: "IransSansFaNumBold";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSansFaNumBold';
    }else if($theme->settings->primary_font == 4){
        $font_name = 'Nunito';
        $font_src = 'https://fonts.googleapis.com/css?family=Nunito:400,500,600,700';
    }else if($theme->settings->primary_font == 5){
        $font_name = 'Dosis';
        $font_src = 'https://fonts.googleapis.com/css?family=Dosis:400,500,600,700';
    }else if($theme->settings->primary_font == 6){
        $font_name = 'Lato';
        $font_src = 'https://fonts.googleapis.com/css?family=Lato:400,500,600,700';
    } else if($theme->settings->primary_font == 7){
      $font_name = 'Maven Pro';
      $font_src = 'https://fonts.googleapis.com/css?family=Maven+Pro:400,500,600,700';
    } else if($theme->settings->primary_font == 8){
      $font_name = 'Montserrat';
      $font_src = 'https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700';
    } else if($theme->settings->primary_font == 9){
      $font_name = 'Open Sans';
      $font_src = 'https://fonts.googleapis.com/css?family=Open+Sans:400,500,600,700';
    } else if($theme->settings->primary_font == 10){
      $font_name = 'Playfair Display';
      $font_src = 'https://fonts.googleapis.com/css?family=Playfair+Display:400,500,600,700';
    } else if($theme->settings->primary_font == 11){
      $font_name = 'Poppins';
      $font_src = 'https://fonts.googleapis.com/css?family=Poppins:400,500,600,700';
    } else if($theme->settings->primary_font == 12){
      $font_name = 'Raleway';
      $font_src = 'https://fonts.googleapis.com/css?family=Raleway:400,500,600,700';
    } else if($theme->settings->primary_font == 13){
      $font_name = 'Roboto';
      $font_src = 'https://fonts.googleapis.com/css?family=Roboto:400,500,600,700';
    } else if($theme->settings->primary_font == 14){
      $font_name = 'Lora';
      $font_src = 'https://fonts.googleapis.com/css?family=Lora:400,500,600,700';
    } else if($theme->settings->primary_font == 15){
      $font_name = 'Oxygen';
      $font_src = 'https://fonts.googleapis.com/css?family=Oxygen:400,500,600,700';
    } else {
        $font_src = '@font-face {
                     font-family: "IransSans";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSans';
    }
    if( !empty($theme->settings->upload_font_eot) ||
        !empty($theme->settings->upload_font_woff2) ||
        !empty($theme->settings->upload_font_woff) ||
        !empty($theme->settings->upload_font_ttf) ||
        !empty($theme->settings->upload_font_svg)
      ) {
      $font_src = '@font-face {
                     font-family: "pafcoCustomPrimary";
                     src: url("'.$theme->setting_file_url('upload_font_eot', 'upload_font_eot').'");
                     src: url("'.$theme->setting_file_url('upload_font_eot', 'upload_font_eot').'#iefix") format("embedded-opentype"),
                          url("'.$theme->setting_file_url('upload_font_woff2', 'upload_font_woff2').'") format("woff2"),
                          url("'.$theme->setting_file_url('upload_font_woff', 'upload_font_woff').'") format("woff"),
                          url("'.$theme->setting_file_url('upload_font_ttf', 'upload_font_ttf').'") format("truetype"),
                          url("'.$theme->setting_file_url('upload_font_svg', 'upload_font_svg').'") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';

       $font_name = 'pafcoCustomPrimary';
    } else if (!in_array($font_name,['IransSans', 'IransSansBold', 'IransSansFaNum','IransSansFaNumBold',])){
      $font_src = '@import url('.$font_src.');';
    }

    $tag = '[[setting:primary_font]]';
    $replacement = $font_name;
    if(is_null($replacement)){$replacement = 'Nunito';}
    $css = str_replace($tag, $replacement, $css);

    $tag_src = '[[setting:primary_font_src]]';
    $replacement_src = $font_src;
    if(is_null($replacement_src)){$replacement_src = '@import url("https://fonts.googleapis.com/css?family=Nunito:400,500,600,700");';}
    $css = str_replace($tag_src, $replacement_src, $css);

    if($theme->settings->secondary_font == 1) {
        $font_src = '@font-face {
                     font-family: "IransSansBold";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_Bold.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSansBold';
    } else if($theme->settings->secondary_font == 2){
        $font_src = '@font-face {
                     font-family: "IransSansFaNum";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSansFaNum';
    }else if($theme->settings->secondary_font == 3){
        $font_src = '@font-face {
                     font-family: "IransSansFaNumBold";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb_FaNum.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSansFaNumBold';
    }else if($theme->settings->secondary_font == 4){
        $font_name = 'Nunito';
        $font_src = 'https://fonts.googleapis.com/css?family=Nunito:400,500,600,700';
    }else if($theme->settings->secondary_font == 5){
        $font_name = 'Dosis';
        $font_src = 'https://fonts.googleapis.com/css?family=Dosis:400,500,600,700';
    }else if($theme->settings->secondary_font == 6){
        $font_name = 'Lato';
        $font_src = 'https://fonts.googleapis.com/css?family=Lato:400,500,600,700';
    } else if($theme->settings->secondary_font == 7){
        $font_name = 'Maven Pro';
        $font_src = 'https://fonts.googleapis.com/css?family=Maven+Pro:400,500,600,700';
    } else if($theme->settings->secondary_font == 8){
        $font_name = 'Montserrat';
        $font_src = 'https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700';
    } else if($theme->settings->secondary_font == 9){
        $font_name = 'Open Sans';
        $font_src = 'https://fonts.googleapis.com/css?family=Open+Sans:400,500,600,700';
    } else if($theme->settings->secondary_font == 10){
        $font_name = 'Playfair Display';
        $font_src = 'https://fonts.googleapis.com/css?family=Playfair+Display:400,500,600,700';
    } else if($theme->settings->secondary_font == 11){
        $font_name = 'Poppins';
        $font_src = 'https://fonts.googleapis.com/css?family=Poppins:400,500,600,700';
    } else if($theme->settings->secondary_font == 12){
        $font_name = 'Raleway';
        $font_src = 'https://fonts.googleapis.com/css?family=Raleway:400,500,600,700';
    } else if($theme->settings->secondary_font == 13){
        $font_name = 'Roboto';
        $font_src = 'https://fonts.googleapis.com/css?family=Roboto:400,500,600,700';
    } else if($theme->settings->secondary_font == 14){
        $font_name = 'Lora';
        $font_src = 'https://fonts.googleapis.com/css?family=Lora:400,500,600,700';
    } else if($theme->settings->secondary_font == 15){
        $font_name = 'Oxygen';
        $font_src = 'https://fonts.googleapis.com/css?family=Oxygen:400,500,600,700';
    } else {
        $font_src = '@font-face {
                     font-family: "IransSans";
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.eot");
                     src: url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.eot#iefix") format("embedded-opentype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.woff2") format("woff2"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.woff") format("woff"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.ttf") format("truetype"),
                          url("'.$CFG->wwwroot.'/theme/pafco/fonts/IRANSansWeb.svg") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';
        $font_name = 'IransSans';
    }
    if( !empty($theme->settings->upload_font_secondary_eot) ||
        !empty($theme->settings->upload_font_secondary_woff2) ||
        !empty($theme->settings->upload_font_secondary_woff) ||
        !empty($theme->settings->upload_font_secondary_ttf) ||
        !empty($theme->settings->upload_font_secondary_svg)
      ) {
      $font_src = '@font-face {
                     font-family: "pafcoCustomSecondary";
                     src: url("'.$theme->setting_file_url('upload_font_secondary_eot', 'upload_font_secondary_eot').'");
                     src: url("'.$theme->setting_file_url('upload_font_secondary_eot', 'upload_font_secondary_eot').'#iefix") format("embedded-opentype"),
                          url("'.$theme->setting_file_url('upload_font_secondary_woff2', 'upload_font_secondary_woff2').'") format("woff2"),
                          url("'.$theme->setting_file_url('upload_font_secondary_woff', 'upload_font_secondary_woff').'") format("woff"),
                          url("'.$theme->setting_file_url('upload_font_secondary_ttf', 'upload_font_secondary_ttf').'") format("truetype"),
                          url("'.$theme->setting_file_url('upload_font_secondary_svg', 'upload_font_secondary_svg').'") format("svg");
                     font-weight: normal;
                     font-style: normal;
                   }';

       $font_name = 'pafcoCustomSecondary';
    } else if (!in_array($font_name,['IransSans', 'IransSansBold', 'IransSansFaNum','IransSansFaNumBold',])){
      $font_src = '@import url('.$font_src.');';
    }
    $tag = '[[setting:secondary_font]]';
    $replacement = $font_name;
    if(is_null($replacement)){$replacement = 'Open Sans';}
    $css = str_replace($tag, $replacement, $css);

    $tag_src = '[[setting:secondary_font_src]]';
    $replacement_src = $font_src;
    if(is_null($replacement_src)){$replacement_src = '@import url("https://fonts.googleapis.com/css?family=Open+Sans");';}
    $css = str_replace($tag_src, $replacement_src, $css);

    return $css;
}

function frontpage($theme) {
    global $DB,$PAGE,$CFG;

    $templatecontext['bannerimage'] = $CFG->wwwroot . '/theme/pafco/pix/navid-laptop-3d.png';
    $templatecontext['bannerlogo'] = $CFG->wwwroot . '/theme/pafco/pix/vums.png';
    if (!empty(get_config('theme_pafco','bannerimage'))) {
        $url = $PAGE->theme->setting_file_url('bannerimage', 'banner');
        if ($url) {
            // Get a URL suitable for moodle_url.
            $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
            $url = str_replace($relativebaseurl, '', $url);
            $templatecontext['bannerimage'] = new moodle_url($url);
        }
    }
    if (!empty(get_config('theme_pafco','bannerlogo'))) {
        $url = $PAGE->theme->setting_file_url('bannerlogo', 'banner');
        if ($url) {
            // Get a URL suitable for moodle_url.
            $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
            $url = str_replace($relativebaseurl, '', $url);
            $templatecontext['bannerlogo'] = new moodle_url($url);
        }
    }
    if (!empty($theme->settings->videofile)) {
        $url = $theme->setting_file_url('videofile', 'videofile');
        //print_object($fileurl);
        //die();
        if ($url) {
            // Get a URL suitable for moodle_url.
            $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
            $url = str_replace($relativebaseurl, '', $url);
            $templatecontext['videofile'] = new moodle_url($url);
        }
    }
    if (!empty(get_config('theme_pafco','videoposter'))) {
        $url = $PAGE->theme->setting_file_url('videoposter', 'videoposter');
        if ($url) {
            // Get a URL suitable for moodle_url.
            $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
            $url = str_replace($relativebaseurl, '', $url);
            $templatecontext['videoposter'] = new moodle_url($url);
        }
    }
    $templatecontext['whatsistitle'] = get_config('theme_pafco','whatsistitle');
    $templatecontext['whatsiscontent'] = format_string(get_config('theme_pafco','whatsiscontent'),false);

    $templatecontext['showmoodlefirstpage'] = get_config('theme_pafco','showmoodlefirstpage');
    $numbersfrontpage = get_config('theme_pafco','numbersfrontpage');
    if ($numbersfrontpage) {
        $templatecontext['numbersfrontpage'] = $numbersfrontpage;
        $templatecontext['numbersusers'] = $DB->count_records_sql(
            'SELECT COUNT(DISTINCT userid)
                    FROM {role_assignments}
                    WHERE roleid = 5'
        );
        $templatecontext['numberscourses'] = $DB->count_records('course', ['visible' => 1]) - 1;
        $templatecontext['numbersteachers'] = $DB->count_records_sql(
            'SELECT COUNT(DISTINCT userid)
                    FROM {role_assignments}
                    WHERE roleid = 3'
        );
        $templatecontext['numbersactivties'] = $DB->count_records('course_modules', ['visible' => 1]) - 1;
    }
    $templatecontext = array_merge($templatecontext,blog_entries());
    if ($faq = faq()) {
        return array_merge($templatecontext,$faq);
    }

    return $templatecontext;
}
 function faq() {
    $templatecontext['faqenabled'] = false;
    $theme = theme_config::load('pafco');

    if ($theme->settings->faqcount) {
        for ($i = 1; $i <= $theme->settings->faqcount; $i++) {
            $faqquestion = 'faqquestion' . $i;
            $faqanswer = 'faqanswer' . $i;

            if (!$theme->settings->$faqquestion || !$theme->settings->$faqanswer) {
                continue;
            }

            $templatecontext['faq'][] = [
                'id' => $i,
                'question' => $theme->settings->$faqquestion,
                'answer' => $theme->settings->$faqanswer
            ];
        }

        if ($templatecontext['faq'] && count($templatecontext['faq'])) {
            $templatecontext['faqenabled'] = true;
        }
    }

    return $templatecontext;
}
function blog_entries() {
    global $DB;

    if (current_language() !== 'fa'){
        $templatecontext['no_news']=true;
        return $templatecontext;
    }
    $publishstate = isloggedin() ? "('site','public') " : "('public') ";
    $templatecontext['news'] = array_values(
        $DB->get_records_sql(
            'SELECT id,subject,summary,created FROM {post}
                                  WHERE publishstate IN ' . $publishstate .
                                  ' ORDER BY created DESC'
            ,null,0,6));

    return $templatecontext;
}
