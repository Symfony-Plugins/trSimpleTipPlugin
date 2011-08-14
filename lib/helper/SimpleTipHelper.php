<?php

require_once(sfConfig::get('sf_symfony_lib_dir') . '/helper/JavascriptBaseHelper.php');

/**
 * Binds a simpletip jQuery object to a html tag
 * 
 * Eg: 
 * 
 * // binds all anchors with a title to a tooltip
 * // element and display anchor title
 * simpletip('a[title]');  
 *                        
 * // binds all span elements
 * // and displays the specified content
 * simpletip('span', array('content' => 'Tooltip content'));
 * 
 * 
 * @param string $tag html tag of the DOM element to bind simpletip to
 * $param array $settings array of additional js settings
 * @param string $profile profile defined in app.yml
 */
function simpletip($tag, $settings = array(), $profile = 'default') {
  _simpletip_include_js();
  _simpletip_include_css();
  
  $config = sfConfig::get('app_tr_simpletip_' . $profile, NULL);
  
  if (!isset($config)) {
    _simpletip_log('Simpletip profile ' . $profile . ' not loaded or empty.');
  }
  
  $config = (array)$settings + $config;
  
  echo _simpletip($tag, $config);
}

/**
 * Tooltip js code
 * 
 * @param string $tag html tag to bind tooltip to
 * @param array $config tooltip configuration array
 * @return string tooltip js code
 */
function _simpletip($tag, $config) {
  $options = json_encode($config);
  
  if (isset($config['content']) && !empty($config['content'])) {
    $code = "$('$tag').simpletip($options);";
  }
  else {
    // Tooltip content will be taken from element's title
    $code = "$('$tag').each(function() {
      var options = $options;
      options['content'] = $(this).attr('title');
      $(this).simpletip(options);
      $(this).attr('title', '');
      $(this).attr('alt', '');
    });";
  }
  
  return javascript_tag($code);
}

/**
 * Log messages
 * 
 * @param string $text
 */
function _simpletip_log($text) {
  $logger = sfContext::getInstance()->getLogger();
  
  $logger->notice($text);
}


/**
 * Include simpletip js into current response object.
 */
function _simpletip_include_js() {
  $context = sfContext::getInstance();
  $response = $context->getResponse();
  
  if (sfConfig::get('sf_web_debug', FALSE)) {
    // Include unminified version of simple tip plugin
    $response->addJavascript('/trSimpleTipPlugin/js/jquery.simpletip-1.3.1.js');
  }
  else {
    // Include minified version of simple tip plugin
    $response->addJavascript('/trSimpleTipPlugin/js/jquery.simpletip-1.3.1.min.js');
  }
}

/**
 * Include simpletip css into current response object.
 */
function _simpletip_include_css() {
  $context = sfContext::getInstance();
  $response = $context->getResponse();
  
  $response->addStylesheet('/trSimpleTipPlugin/css/simpletip.css');
}

