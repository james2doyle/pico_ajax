<?php

/**
 * AJAX fetch a template
 *
 * @author James Doyle
 * @link http://ohdoylerules.com
 * @license http://opensource.org/licenses/MIT
 */
class Pico_Ajax {

  private $fetching; // bool for right URL and POST request
  private $plugin_path; // where dis plugin at
  private $development; // sleep delay
  private $data; // the loaded config array
  private $data_key; // the key for the data in the config you want
  private $template; // which template to render
  private $request_string; // which url to use

  public function __construct() {
    $this->template = 'template.html';
    $this->request_string = 'brands';
    $this->data_key = $this->request_string;
    $this->plugin_path = dirname(__FILE__);
    // adds a delay to the request for emulation of real life
    $this->development = false;
    // by default we are not at the correct URL
    $this->fetching = false;
  }

  public function manipulate_post_data() {
    // I may need to edit my data before it gets to the template
    $id = explode('-', $_POST['id']);
    return $id[1];
  }

  public function request_url(&$url) {
    // check to see if we are fetching from the quotes URL
    // also check if this is a POST request
    if (strlen($url) > 0) {
      // the url im looking for
      if(strpos($this->request_string, $url) !== false && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->fetching = true;
      }
    }
  }

  public function config_loaded(&$settings) {
    // grab some custom settings
    $this->data = $settings[$this->data_key];
    return;
  }

  public function before_render(&$twig_vars, &$twig) {
    if($this->fetching){
      // get the manipulated post data
      $post = $this->manipulate_post_data();
      if ($this->development) {
        sleep(2);
      }
      header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
      $loader = new Twig_Loader_Filesystem($this->plugin_path);
      $twig_editor = new Twig_Environment($loader, $twig_vars);
      echo $twig_editor->render($this->template, $this->data[$post]);
      // Don't continue to render template
      exit;
    }
  }

}

?>