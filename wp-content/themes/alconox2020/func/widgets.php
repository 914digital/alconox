<?php

function bst_widgets_init() {

  	/*
    Sidebar (one widget area)
     */
    register_sidebar( array(
        'name' => __( 'Login', 'bst' ),
        'id' => 'login-widget-area',
        'description' => __( 'The login widget area', 'bst' ),
        'before_widget' => '<section class="%1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ) );

  	/*
    Footer (three widget areas)
     */
    register_sidebar( array(
        'name' => __( 'Footer', 'bst' ),
        'id' => 'footer-widget-area',
        'description' => __( 'The footer widget area', 'bst' ),
        'before_widget' => '<div class="%1$s %2$s col-sm-4">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ) );

    /*
    RSS (One Widget Area)
    */
    register_sidebar( array(
      'name' => __( 'RSS', 'bst' ),
      'id' => 'rss-widget-area',
      'description' => __( 'The RSS widget area', 'bst' ),
      'before_widget' => '<div>',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>',
  ) );

}
add_action( 'widgets_init', 'bst_widgets_init' );

?>