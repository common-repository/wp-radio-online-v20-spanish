<?php
  /**
   *  Plugin Name: WP Radio Online
   *  Version: 2.3
   *  Plugin URI: http://www.laliamos.com/servicios-seo-barato-posicionamiento-web-diseno-web/diseno-web-valencia/wp-radio-online-v2-0-plugin-wordpress/
   *  Description: Coloca un widget con emisoras de radio online, como en http://www.laliamos.com
   *  Author: acidc00l (Original y API de GeorgeJipa http://www.radiourionline.ro/plugin-cu-widget-radiouri-online-s195.html)
   *  Author URI: http://www.laliamos.com
   **/
  include_once(ABSPATH . WPINC . '/feed.php');   
  
   $dir_site = 'http://www.radiourionline.ro/';
   $api_url = 'http://www.radiourionline.ro/api/index.php'; 
   $dirr = get_settings('siteurl');
   $plugin_path = $dirr.'/wp-content/plugins/wp-radio-online';
   $blog_URL = get_settings('siteurl');
         
   function getCateg(){
    global $api_url;
    
    $data = array('method' => 'getCateg', 'time' => time());
    $query = http_build_query($data);
    
    $rss = fetch_feed($api_url.'?'.$query); 
    $items = $rss->get_items();
    
    foreach($items as $item){
      $categ = $item->data['data'];
      
      if(get_option('rw_categ_bifate') == TRUE){
        $categBifate = unserialize(get_option('rw_categ_bifate'));
        $checked = (in_array($categ, $categBifate)) ? 'checked' : '';
      } else {
        $checked = '';
      }
      echo '<input type="checkbox" name="categoria[]" value="'.$categ.'" '.$checked.'/> '.$categ.' ';
    }
   }

   function getRCateg(){
    global $api_url;
    
    $categBifate = unserialize(get_option('rw_categ_bifate')); 
    $data = array('method' => 'getRCateg', 'cats' => $categBifate, 'time' => time());
    $query = http_build_query($data);
    
    $rss = fetch_feed($api_url.'?'.$query);
    $items = $rss->get_items();
    
    foreach($items as $item){
      $post = $item->data['data'];
      $idpost = $item->data['attribs']['']['idpost'];
      $stream = $item->data['attribs']['']['stream'];

      if(get_option('rw_post_bifate') == TRUE) {
        $postBifate = unserialize(get_option('rw_post_bifate'));
        $checked = (in_array($idpost, $postBifate)) ? 'checked' : '';
      } else  {
        $checked = '';
      }
      echo '<input type="checkbox" name="cadena[]" value="'.$idpost.'" '.$checked.'/> <a href="'.$stream.'" target="_blank">'.$post.'</a> ';
    }
   }

   function getRadios(){ 
    global $api_url, $plugin_path, $dir_site;
    
    if(get_option('rw_post_bifate') == TRUE){
      $max = get_option('rw_widget_max');
            
      $postBifate = unserialize(get_option('rw_post_bifate'));
      $data = array('method' => 'getRadios', 'ids' => $postBifate);
      $query = http_build_query($data);
      $rss = fetch_feed($api_url.'?'.$query);
      $items = $rss->get_items();

      echo '<ul>';
      foreach($items as $item){
        $urlcat = $item->data['attribs']['']['urlcat'];
        $numecat = $item->data['attribs']['']['numecat'];
      
        echo '<li><strong style="font-size:14px;">'.$numecat.'</strong>';
        $i=1;
		echo '<ul>';
        foreach($item->data['child']['']['post'] as $post){
          $stream = $post['attribs']['']['stream'];
		  $postid = $post['attribs']['']['idpost'];
          $post = $post['data']; 
          
          if($i<=$max) echo '<li><a target="_blank" rel="nofollow" href="'.$dir_site . 'listen.php?id=' . $postid.'"  title="Reproducir '.$post.'">'.$post.' <img src="'$plugin_path.'/reproducir.png ?>" border="0" /></a></li>';
          $i++;
        }
        echo '</ul></li>';
      }
      echo '<br /><img src="'$plugin_path.'/radio-online.gif" /><br />Radio Online by <a href="http://www.laliamos.com" title="Posicionamiento Web SEO" target="_blank">Posicionamiento Web SEO</a>';
      echo '</ul>'; 
    } else {
      echo '<ul><li>Estación seleccionada!</li></ul>';
    }
   }
   
  function register_rw_widget($args) {
    extract($args);
    
    echo $before_widget;
    $title = get_option('rw_widget_title');
    echo $args['before_title'].' '.$title.' '.$args['after_title'];
    getRadios(); 
    echo $after_widget;
  }
  	  
  function register_rw_control(){
    $max = get_option('rw_widget_max');
    $title = get_option('rw_widget_title');
    echo '<img src="'$plugin_path.'/radio-online.gif" /><p>Radio Online by <a href="http://www.laliamos.com" title="Posicionamiento Web SEO" target="_blank">Posicionamiento Web SEO</a></p>';
    echo '<p><label>Titulo Radio Widget: <input name="title" type="text" value="'.$title.'" /></label></p>';
    echo '<p><label>Emisoras/Categorías: <input name="max" type="text" value="'.$max.'" /></label></p>';
      
    if(isset($_POST['max'])){
      update_option('rw_widget_max', attribute_escape($_POST['max']));
      update_option('rw_widget_title', attribute_escape($_POST['title']));
    }
  }    
  
  function rw_widget() {
  	 register_widget_control('RadioWidget', 'register_rw_control'); 
  	 register_sidebar_widget('RadioWidget', 'register_rw_widget');
  }          
        
   function rw_admin(){
    echo '<div class="wrap">';
    echo '<br /><img src="'$plugin_path.'/radio-online.gif" /><br />Radio Online by <a href="http://www.laliamos.com" title="Posicionamiento Web SEO" target="_blank">Posicionamiento Web SEO</a>';
    echo '<h2>Configurar WP-Radio Online</h2>';
    if(isset($_POST['scategoria']) && isset($_POST['categoria'])){ 
        $categoria = serialize($_POST['categoria']);
        if(get_option('rw_categ_bifate') === FALSE){
          add_option('rw_categ_bifate', $categoria);
        } else {
          delete_option('rw_categ_bifate');
          add_option('rw_categ_bifate', $categoria);
        }
    }
    echo '<div class="widefat" style="padding: 5px">1) Seleccione una o más categorías:<br /><br />';
    echo '<form method="post" name="categoria" target="_self">';
    getCateg();
    echo '<input name="scategoria" type="hidden" value="yes" />';
    echo '<br /><br /><input type="submit" name="Submit" value="Lista de categorías &raquo;" />';    
    echo '</form>';
    echo '</div>';
    echo '<br />';
    if(isset($_POST['scategoria']) && isset($_POST['categoria'])){
      echo '<div class="widefat fade" style="padding: 5px">2) Elija las estaciones de radio que desea guardar <br /><br />';
      echo '<form method="post" name="cadena" target="_self">';      
      getRCateg();
      echo '<input name="sposturi" type="hidden" value="yes" />';
      echo '<br /><br /><input type="submit" name="Submitt" value="Guardar emisora &raquo;" />';    
      echo '</form>';
      echo 'Recuerda esta es la página del plugin: http://www.laliamos.com/servicios-seo-barato-posicionamiento-web-diseno-web/diseno-web-valencia/wp-radio-online-v2-0-plugin-wordpress/<br /></div>';   
    }
    if(isset($_POST['sposturi']) && isset($_POST['cadena'])){
        $cadena = serialize($_POST['cadena']);
        if(get_option('rw_post_bifate') === FALSE){
          add_option('rw_post_bifate', $cadena);
        } else {
          delete_option('rw_post_bifate');
          add_option('rw_post_bifate', $cadena);
        }
        echo '<div id="message" class="updated fade"><p><strong>Emisoras de radio guardadas!</strong></p></div>';        
      }    
    echo '</div>';
   }   

  
  function rw_addpage() {
    add_submenu_page('options-general.php', 'WP Radio Widget', 'WP Radio Widget', 10, __FILE__, 'rw_admin');
  }
     
  add_action('admin_menu', 'rw_addpage');
  add_action("plugins_loaded", "rw_widget");
  add_option('rw_widget_max', '5');
  add_option('rw_widget_title', 'WP Radio Online');  
?>
