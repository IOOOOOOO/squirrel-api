<?php
/*
Plugin Name: 松鼠API
Plugin URI: https://www.squirrelzoo.com
Description: 为小程序定制输出API.
Version: 1.0.0
Author: squirrelzoo
Author URI: https://www.squirrelzoo.com
WordPress requires at least: 4.7.1
*/


define('SQUIRREL_API_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SQUIRREL_API_PLUGIN_FILE',__FILE__);
const SQUIRREL_API_PLUGIN_NAME='squirrel-api';

include(SQUIRREL_API_PLUGIN_DIR . 'includes/utils.php' );
include(SQUIRREL_API_PLUGIN_DIR . 'includes/api-register.php' );
include(SQUIRREL_API_PLUGIN_DIR . 'includes/settings/wp-wechat-config.php');
include(SQUIRREL_API_PLUGIN_DIR . 'includes/filter/squirrel-custom-comment-fields.php');
include(SQUIRREL_API_PLUGIN_DIR . 'includes/filter/squirrel-custom-post-fields.php');
include(SQUIRREL_API_PLUGIN_DIR . 'includes/filter/squirrel-custom-users-columns.php');

if ( ! class_exists( 'SquirrelAPI' ) ) {

    class SquirrelAPIPlugin {
       
        public function __construct() {
            //定制化内容输出，对pc端和api都生效

            //对文章的自定义输出
            add_filter( 'rest_prepare_post', 'custom_post_fields', 10, 3 );            
            //对评论的自定义输出
            add_filter( 'rest_prepare_comment', 'custom_comment_fields', 10, 3 );
            //对用户列添加自定义输出
            add_filter( 'manage_users_columns', 'users_columns' );
			add_action( 'manage_users_custom_column', 'output_users_columns', 10, 3 );
			

            //更新浏览次数（pc）
            add_action('wp_head', 'addPostPageviews');

            
            // 管理配置 
            if ( is_admin() ) {             
                
                //new WP_Category_Config();
               add_action('admin_menu', 'weixinapp_create_menu');
               add_filter( 'plugin_action_links', 'squirrel_api_plugin_action_links', 10, 2 );
                 
            }
            //注册APi
            new Squirrel_API_Register();//api

        }

    }


    // 实例化并加入全局变量
    $GLOBALS['SquirrelAPIPlugin'] = new SquirrelAPIPlugin();

    function squirrel_api_plugin_action_links( $links, $file ) {
        if ( plugin_basename( __FILE__ ) !== $file ) {
            return $links;
        }


        $settings_link = '<a href="https://www.squirrelzoo.com/" target="_blank"> <span style="color:green; font-weight:bold;">' . esc_html__( '技术支持', 'squirrel api' ) . '</span></a>';

        array_unshift( $links, $settings_link );

        $settings_link = '<a href="admin.php?page=squirrel_api_config">' . esc_html__( '设置', 'squirrel api' ) . '</a>';

        array_unshift( $links, $settings_link );

        return $links;
    }

}
