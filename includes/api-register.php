<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Squirrel_API_Register extends WP_REST_Controller{

    /**
     * Setup class.
     * @since 2.0
     */
    public function __construct() {     
        $this->rest_api_init();
    }
    
    /**
     * Init WP REST API.
     * @since 2.6.0
     */
    private function rest_api_init() {
        global $wp_version;

        // REST API was included starting WordPress 4.4.
        // if ( version_compare( $wp_version, 4.4, '<' ) ) {
        //  return;
        // }

        $this->rest_api_includes();

        // Init REST API routes.
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * Include REST API classes.
     * @since 2.6.0
     */
    private function rest_api_includes() {
        include_once( 'api/squirrel-rest-posts-controller.php' );
        include_once( 'api/squirrel-rest-comments-controller.php' );
        include_once( 'api/squirrel-rest-auth-controller.php' );
        include_once( 'api/squirrel-rest-internal-controller.php' );
    }

    /**
     * Register REST API routes.
     * @since 2.6.0
     */
    public function register_rest_routes() {
        $controllers = array(
            'Squirrel_REST_Posts_Controller',
            'Squirrel_REST_Comments_Controller',
            'Squirrel_REST_Auth_Controller',
            'Squirrel_REST_Internal_Controller'
        );

        foreach ( $controllers as $controller ) {
            $this->$controller = new $controller();
            $this->$controller->register_routes();
        }
    }
}
