<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Squirrel_REST_Internal_Controller  extends WP_REST_Controller{

	public function __construct() {
        
        $this->namespace     = 'squirrel/v1';
        $this->resource_name = 'internal';

    }

    public function register_routes() {
    	register_rest_route( $this->namespace, '/' . $this->resource_name.'/redis_conf', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'save_redis_conf' ),
                'permission_callback' => array( $this, 'permissions_check' ),
                'args'               => array(              
                    'squirrelzoo_redis_host' => array(
                        'required' => true
                    ),                    
                    'squirrelzoo_redis_port' => array(
                        'required' => true
                    ),
                    'squirrelzoo_redis_db' => array(
                        'required' => true
                    ),
                    'squirrelzoo_redis_password' => array(
                        'required' => true
                    )
                   
                )
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/wx_conf', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'save_wx_conf' ),
                'permission_callback' => array( $this, 'permissions_check' ),
                'args'               => array(              
                    'squirrelzoo_appid' => array(
                        'required' => true
                    ),                    
                    'squirrelzoo_secret' => array(
                        'required' => true
                    ),
                    'squirrelzoo_banners' => array(
                        'required' => true
                    )
                   
                )
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/code_conf', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'save_code_conf' ),
                'permission_callback' => array( $this, 'permissions_check' ),
                'args'               => array(              
                    'squirrelzoo_mailcode_interval' => array(
                        'required' => true
                    ),                    
                    'squirrelzoo_mailcode_freq' => array(
                        'required' => true
                    ),
                    'squirrelzoo_mailcode_expire' => array(
                        'required' => true
                    )
                   
                )
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/re_init_id_pool', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 're_init_id_pool' ),
                'permission_callback' => array( $this, 'permissions_check' ),
                'args'               => array(              
                    'squirrelzoo_id_min' => array(
                        'required' => true
                    ),                    
                    'squirrelzoo_id_max' => array(
                        'required' => true
                    ),
                   
                   
                )
                 
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );


        register_rest_route( $this->namespace, '/' . $this->resource_name.'/test_redis', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'test_redis' ),
                'permission_callback' => array( $this, 'permissions_check' ),
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );


        register_rest_route( $this->namespace, '/' . $this->resource_name.'/id_pool_size', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'get_id_pool_size' ),
                'permission_callback' => array( $this, 'permissions_check' ),
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );


        register_rest_route( $this->namespace, '/' . $this->resource_name.'/clean_id_pool', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'clean_id_pool' ),
                'permission_callback' => array( $this, 'permissions_check' ),
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

    }



    function save_redis_conf($request){
    	$squirrelzoo_redis_host = $request['squirrelzoo_redis_host'];
    	$squirrelzoo_redis_port = $request['squirrelzoo_redis_port'];
    	$squirrelzoo_redis_db = $request['squirrelzoo_redis_db'];
    	$squirrelzoo_redis_password = $request['squirrelzoo_redis_password'];
    	update_option('squirrelzoo_redis_host', $squirrelzoo_redis_host);
    	update_option('squirrelzoo_redis_port', $squirrelzoo_redis_port);
    	update_option('squirrelzoo_redis_db', $squirrelzoo_redis_db);
    	update_option('squirrelzoo_redis_password', $squirrelzoo_redis_password);
    
    	$response = rest_ensure_response(respone_data('0','保存成功'));
        return $response; 
    }


    function save_wx_conf($request){
    	$squirrelzoo_appid = $request['squirrelzoo_appid'];
    	$squirrelzoo_secret = $request['squirrelzoo_secret'];
    	$squirrelzoo_banners = $request['squirrelzoo_banners'];
    	update_option('squirrelzoo_appid', $squirrelzoo_appid);
    	update_option('squirrelzoo_secret', $squirrelzoo_secret);
    	update_option('squirrelzoo_banners', $squirrelzoo_banners);

    	$response = rest_ensure_response(respone_data('0','保存成功'));
        return $response; 
    }

    function save_code_conf($request){

    	$squirrelzoo_mailcode_interval = $request['squirrelzoo_mailcode_interval'];
    	$squirrelzoo_mailcode_freq = $request['squirrelzoo_mailcode_freq'];
    	$squirrelzoo_mailcode_expire = $request['squirrelzoo_mailcode_expire'];
    	update_option('squirrelzoo_mailcode_interval', $squirrelzoo_mailcode_interval);
		update_option('squirrelzoo_mailcode_freq', $squirrelzoo_mailcode_freq);
		update_option('squirrelzoo_mailcode_expire', $squirrelzoo_mailcode_expire);

    	$response = rest_ensure_response(respone_data('0','保存成功'));
        return $response; 
    }

    function re_init_id_pool($request){
    	$squirrelzoo_id_min = $request['squirrelzoo_id_min'];
    	$squirrelzoo_id_max = $request['squirrelzoo_id_max'];
    	
    	update_option('squirrelzoo_id_min', $squirrelzoo_id_min);
    	update_option('squirrelzoo_id_max', $squirrelzoo_id_max);
    	

    	$min = intval($squirrelzoo_id_min);
		$max = intval($squirrelzoo_id_max);		
		$redis = get_redis();
		if(!$redis){
			$response = rest_ensure_response(respone_data('-1','redis连接失败'));
			return $response; 
		}
		$count = 0;
		$except = $max-$min;
		if($max-$min > 0 && $max-$min < 100000){
			//往redis中插入
			 set_time_limit(120);
			 for($i=$min;$i<$max;$i++){
			 	$u = username_exists($i);
			 	if($u){
			 		continue;
			 	}
			 	$redis->sadd('UserIdPool',$i);
			 	$count++;
			 }

			$response = rest_ensure_response(respone_data('0','初始化成功，期望'.$except.' 成功'.$count,$redis->scard('UserIdPool')));
		}else{
			$response = rest_ensure_response(respone_data('-1','扩容失败,每次扩容的ID范围差要小于100000'));
		}

    	
        return $response; 
    }

    function test_redis($request){
     	$redis = get_redis();
        $test = $redis->setex('squirrel_test',100,'1');
        $redis->close();
        
        if($test==true){
        	$res = respone_data('0','连接成功');
        }else{
        	$res = respone_data('-1','连接失败');
        }

    	$response = rest_ensure_response($res);
        return $response; 
    }

    function get_id_pool_size($request){
     	$redis = get_redis();
       	$res = respone_data('0','获取成功',$redis->scard('UserIdPool'));
       	$redis->close();
    	$response = rest_ensure_response($res);
        return $response; 
    }


    function clean_id_pool($request){
     	$redis = get_redis();
     	$redis->del('UserIdPool');
       	$res = respone_data('0','清空成功');
        $redis->close();

    	$response = rest_ensure_response($res);
        return $response; 
    }


    public function permissions_check($request) {      
        $token = get_option('squirrel_setting_token');
        return $token == $request['token'];
    }

}