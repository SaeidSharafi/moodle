<?php
/*
@ccnRef: @block_pafco/block.php
*/

defined('MOODLE_INTERNAL') || die();

// if (!($this->config)) {
//   if(!($this->content)){
//     $this->content = new \stdClass();
//   }
//     $this->content->text = '<h5 class="mb30">'.$this->title.'</h5>';
//     return $this->content->text;
// }

// print_object($this);
$ccnBlockType = $this->instance->blockname;

$ccnCollectionFullwidthTop =  array(
  "pafco_about_1",
  "pafco_about_2",
  "pafco_accordion",
  "pafco_action_panels",
  "pafco_blog_recent_slider",
  "pafco_boxes",
  "pafco_event_list",
  "pafco_event_list_2",
  "pafco_faqs",
  "pafco_features",
  "pafco_form",
  "pafco_gallery_video",
  "pafco_parallax",
  "pafco_parallax_apps",
  "pafco_parallax_counters",
  "pafco_parallax_features",
  "pafco_parallax_testimonials",
  "pafco_parallax_subscribe",
  "pafco_parallax_subscribe_2",
  "pafco_partners",
  "pafco_parallax_white",
  "pafco_pills",
  "pafco_price_tables",
  "pafco_price_tables_dark",
  "pafco_services",
  "pafco_services_dark",
  "pafco_simple_counters",
  "pafco_hero_1",
  "pafco_hero_2",
  "pafco_hero_3",
  "pafco_hero_4",
  "pafco_hero_5",
  "pafco_hero_6",
  "pafco_hero_7",
  "pafco_slider_1",
  "pafco_slider_1_v",
  "pafco_slider_2",
  "pafco_slider_3",
  "pafco_slider_4",
  "pafco_slider_5",
  "pafco_slider_6",
  "pafco_slider_7",
  "pafco_slider_8",
  "pafco_steps",
  "pafco_steps_dark",
  "pafco_subscribe",
  "pafco_tablets",
  "pafco_course_categories",
  "pafco_course_categories_2",
  "pafco_course_categories_3",
  "pafco_course_categories_4",
  "pafco_course_categories_5",
  "pafco_course_grid",
  "pafco_course_grid_2",
  "pafco_course_grid_3",
  "pafco_course_grid_4",
  "pafco_course_grid_5",
  "pafco_course_grid_6",
  "pafco_course_grid_7",
  "pafco_course_grid_8",
  "pafco_featuredcourses",
  "pafco_featured_posts",
  "pafco_featured_video",
  "pafco_featured_teacher",
  "pafco_courses_slider",
  "pafco_users_slider",
  "pafco_users_slider_2",
  "pafco_users_slider_2_dark",
  "pafco_users_slider_round",
  "pafco_tstmnls",
  "pafco_tstmnls_2",
  "pafco_tstmnls_3",
  "pafco_tstmnls_4",
  "pafco_tstmnls_5",
  "pafco_tstmnls_6",
);

$ccnCollectionAboveContent =  array(
  "pafco_contact_form",
  "pafco_course_overview",
  "pafco_tabs",
);

$ccnCollectionBelowContent =  array(
  "pafco_course_rating",
  "pafco_more_courses",
  "pafco_course_instructor",
);

$ccnCollection = array_merge($ccnCollectionFullwidthTop, $ccnCollectionAboveContent, $ccnCollectionBelowContent);

//if (empty($this->config)) {
//  if(in_array($ccnBlockType, $ccnCollectionFullwidthTop)) {
//    $this->instance->defaultregion = 'fullwidth-top';
//    $this->instance->region = 'fullwidth-top';
//    $DB->update_record('block_instances', $this->instance);
//  }
//  if(in_array($ccnBlockType, $ccnCollectionAboveContent)) {
//    $this->instance->defaultregion = 'above-content';
//    $this->instance->region = 'above-content';
//    $DB->update_record('block_instances', $this->instance);
//  }
//  if(in_array($ccnBlockType, $ccnCollectionBelowContent)) {
//    $this->instance->defaultregion = 'below-content';
//    $this->instance->region = 'below-content';
//    $DB->update_record('block_instances', $this->instance);
//  }
//  /* Begin Legacy */
//  if(!in_array($ccnBlockType, $ccnCollection)) {
//    if(!($this->content)){
//       $this->content = new \stdClass();
//    }
//    $this->content->text = '<h5 class="mb30">'.$this->title.'</h5>';
//    return $this->content->text;
//  }
//  /* End Legacy */
//}
