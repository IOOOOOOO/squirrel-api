<?php

/**
获取openid
获取用户信息
修改用户信息
发送邮件验证码
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Squirrel_REST_Auth_Controller  extends WP_REST_Controller{

    public function __construct() {
        $this->namespace     = 'squirrel/v1';
        $this->resource_name = 'auth';
    }

    public function register_routes() {

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/openid', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'get_openid' ),
                'permission_callback' => array( $this, 'get_openid_permissions_check' ),
                'args'               => array(              
                    'js_code' => array(
                        'required' => true
                    ),                    
                    'encrypted_data' => array(
                        'required' => true
                    ),
                    'iv' => array(
                        'required' => true
                    ),
                    'avatar_url' => array(
                        'required' => true
                    ),
                    'nickname' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
        
        register_rest_route( $this->namespace, '/' . $this->resource_name.'/mail_code', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'request_bind_mail' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(              
                    'email' => array(
                        'required' => true
                    ),                    
                    'openid' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/user_info', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'get_user' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(                               
                    'openid' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/user_info_by_id', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'get_user_by_id' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(                               
                    'id' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );


        register_rest_route( $this->namespace, '/' . $this->resource_name.'/user_info', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'update_user' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(                               
                    'openid' => array(
                        'required' => true
                    ),
                    'display_name' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/user_avatar', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'update_avatar' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(                               
                    'openid' => array(
                        'required' => true
                    ),

                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/bind_mail', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'bind_mail' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(                               
                    'email' => array(
                        'required' => true
                    ),
                    'openid' => array(
                        'required' => true
                    ),
                    'mail_code' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/password', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'update_password' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(                               
                    'openid' => array(
                        'required' => true
                    ),
                    'new_password' => array(
                        'required' => true
                    ),
                    'mail_code' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name.'/password_mail_code', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'request_update_password' ),
                'permission_callback' => array( $this, 'open_api_check' ),
                'args'               => array(                               
                    'openid' => array(
                        'required' => true
                    )
                )
                 
            ),            
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );


    }


    function get_openid($request){
        global $wpdb;
     
        $appid = get_option('squirrelzoo_appid');
        $appsecret = get_option('squirrelzoo_secret');
        $js_code= $request['js_code'];
        $encrypted_data=$request['encrypted_data'];
        $iv=$request['iv'];
        $avatar_url=$request['avatar_url'];
        $nickname=empty($request['nickname'])?'':$request['nickname'];
        
        $access_url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=".$js_code."&grant_type=authorization_code";
        $access_result = https_request($access_url);
        if($access_result=='ERROR') {
            return new WP_Error( 'error', 'API错误：' . json_encode($access_result), array( 'status' => 500 ) );
        } 
        $api_result = json_decode($access_result,true);            
        if( empty( $api_result['openid'] ) || empty( $api_result['session_key'] )) {
            return new WP_Error('error', 'API错误：' . json_encode( $api_result ), array( 'status' => 500 ) );
        }            
        $openid = $api_result['openid']; 
        $sessionKey = $api_result['session_key']; 
        //已经存在就直接返回
        if (user_exists_by_openid($openid)){
            $res_data['openid'] = $openid;
            $result = respone_data('0','success',$res_data);
            $response = rest_ensure_response($result);
            return $response; 
        }

        //第一次获取，创建用户
        $redis = get_redis();
        //从id池获取用户id
        $new_uid = $redis->spop('UserIdPool');
        $redis->close();
        if(!$new_uid){
            return new WP_Error( 'error', '已经达到用户上限', array( 'status' => 500 ) );   
        }

              
        $nickname=filterEmoji($nickname);  

        $new_user_data = apply_filters( 'new_user_data', array(
            'user_login'    => $new_uid,
            'first_name'    => $nickname ,
            'nickname'      => $nickname,                    
            'user_nicename' => $new_uid,
            'display_name'  => $nickname,
            'user_pass'     => $openid,
        ));                
        $user_id = wp_insert_user( $new_user_data );         
        if ( is_wp_error( $user_id ) || empty($user_id) ||  $user_id==0 ) {
            return new WP_Error( 'error', '插入wordpress用户失败：', array( 'status' => 500 ) );               
        }
        //把用户信息插入meta 表
        update_user_meta($user_id,'squirrelzoo_openid',$openid);
        update_user_meta($user_id,'squirrelzoo_avatar',$avatar_url);

        $res_data['openid'] = $openid;
        $result = respone_data('0','success',$res_data);
        $response = rest_ensure_response($result);
        return $response;  
    }

    //需要做发送频率控制,过期时间控制
    //只能保持一个邮件验证码有效
    function request_bind_mail($request){
        //wp_mail('535553297@qq.com','邮箱验证','12345');
        $send_freq = 60;
        $expire = 10 * 60;
        $email= $request['email'];
        $openid= $request['openid'];

        $u = get_user_by_openid($openid);
        if ($u == null){
           return new WP_Error( 'error', 'openid无效', array( 'status' => 400 ) ); 
        }
        $user = get_user_by('ID',$u->user_id);

        if (!empty($user->user_email)){
            return new WP_Error( 'error', '邮箱地址只能绑定一次', array( 'status' => 400 ) ); 
        }
        
        if(filter_var($email, FILTER_VALIDATE_EMAIL)==false){
            return new WP_Error( 'error', '邮件地址无效', array( 'status' => 400 ) );
        }
        $user = get_user_by('email',$email);
        if($user){
            return new WP_Error( 'error', '邮件地址已经被占用', array( 'status' => 400 ) );
        }
        
        
        //code 格式
        //mail_code/openid/email = {code,time}
        $redis = get_redis();
        //判断是否达到窗口大小
        $code_list = $redis->keys('mail_code/'.$openid.'/*');
        if(count($code_list) >= $squirrelzoo_mailcode_freq){
            return new WP_Error( 'error', '发送次数过多，请休息下再发送', array( 'status' => 403 ) ); 
        }
       
        //读取全部code
        $latest_time = 0;
        foreach ($code_list as $key => $value) {
             
            $c = $redis->get($value);
    
            if($c){
                $c= json_decode($c,true);
                if($c['time'] > $latest_time) {
                    $latest_time = $c['time'];
                }
            }
        }


      
        //判断上次发送时间
        if(time()-$latest_time < $squirrelzoo_mailcode_interval){
            return new WP_Error( 'error', '发送太频繁，请休息下再发送', array( 'status' => 403 ) ); 
        }

        //生成验证码
        $code = rand('100000','999999');
        $code_obj['code'] = $code;
        $code_obj['time'] = time();
        // $code_obj['code'] = 
        //保存到redis
        $redis->setex('mail_code/'.$openid.'/'.$email.'/'.$code_obj['time'], $expire,json_encode($code_obj));
        
        //发送
        $sent = wp_mail($email,'邮箱验证-邮箱绑定','您的邮箱验证码为'.$code.'，有效期10分钟。多次发送，自动作废先前的验证码。');
        if ($sent){
            $result = respone_data('0','success',$sent);
        }else{
            $result = respone_data('-1','failed',$sent);
        }

        $response = rest_ensure_response($result);
        return $response; 

    }

    //获取用户信息：用户名，昵称，邮箱，头像
    function get_user($request){
        $openid= $request['openid'];
        $u = get_user_by_openid($openid);
        if ($u == null){
           return new WP_Error( 'error', 'openid无效', array( 'status' => 400 ) ); 
        }
        $user = get_user_by('ID',$u->user_id);
        $result['user_login'] = $user->data->user_login;
        $result['display_name'] = $user->data->display_name;
        $result['user_email'] = $user->data->user_email;
        $result['avatar'] = get_usermeta($u->user_id,'squirrelzoo_avatar',true);
        $response = rest_ensure_response(respone_data('0','success',$result));
        return $response;     
    }




    //更新个人信息，目前只有昵称   
    function update_user($request){
        $openid= $request['openid'];
        $display_name= $request['display_name'];

        if(strlen($display_name)>10){
            return new WP_Error( 'error', '昵称不可超过10个字符', array( 'status' => 400 ) ); 
        }
        $display_name = esc_html($display_name);
        $u = get_user_by_openid($openid);
        if ($u == null){
           return new WP_Error( 'error', 'openid无效', array( 'status' => 400 ) ); 
        }
        $user_id = wp_update_user( array( 'ID' => $u->user_id, 'display_name' => $display_name ) );
        if ( is_wp_error( $user_id ) ) {
            // There was an error, probably that user doesn't exist.
            return new WP_Error( 'error', '更新个人信息失败', array( 'status' => 500 ) );
        } 
        $response = rest_ensure_response(respone_data('0','success',true));
        return $response;  
    }

    //修改头像
    function update_avatar($request){
        $openid = $request['openid'];
        $u = get_user_by_openid($openid);
        if ($u == null){
           return new WP_Error( 'error', 'openid无效', array( 'status' => 400 ) ); 
        }

        if(!isset($_FILES['file'])){
            return new WP_Error( 'error', '图片不能为空', array( 'status' => 400 ) ); 
        }

        //62 035 kb
        if($_FILES['file']['size']>2*1024*1024){
            return new WP_Error( 'error', '图片不能大于1M', array( 'status' => 400 ) ); 
        }

        $new_name = time().'-'.md5($openid).'-'.wp_generate_uuid4();
        $new_url = WP_CONTENT_URL.'/uploads/avatar/'.$new_name;
        $path = WP_CONTENT_DIR.'/uploads/avatar/';
        if(!file_exists($path)){
            mkdir($path,0777);
        }
        // print($request);
        $r = move_uploaded_file($_FILES["file"]["tmp_name"],$path.$new_name);
        if($r==false){
            return new WP_Error( 'error', '上传失败', array( 'status' => 500 ) );
        }

        $update_res = update_user_meta($u->user_id,'squirrelzoo_avatar',$new_url);
        if(!$update_res){
            $response = rest_ensure_response(respone_data('0','上传失败'));
            return $response; 
        }

        $response = rest_ensure_response(respone_data('0','上传成功',$new_url));
        return $response;  
    }

    //绑定邮箱
    function bind_mail($request){
        $email= $request['email'];
        $openid= $request['openid'];
        $mail_code= $request['mail_code'];
        $u = get_user_by_openid($openid);
        if ($u == null){
            return new WP_Error( 'error', 'openid无效', array( 'status' => 400 ) ); 
        }
       //获取窗口
        $redis = get_redis();
        $code_list = $redis->keys('mail_code/'.$openid.'/*');
        $passed = false;
        foreach ($code_list as $key => $value) {
            $c = $redis->get($value);
            if($c){
                $c= json_decode($c,true);
                if($c['code'] == $mail_code) {
                    $passed = true;
                    $redis->del($value);
                    break;
                }
            }
        }
        if(!$passed){
            return new WP_Error( 'error', '验证码不正确', array( 'status' => 403 ) );
        }


        $user_id = wp_update_user( array( 'ID' => $u->user_id, 'user_email' => $email) );
        if ( is_wp_error( $user_id ) ) {
            // There was an error, probably that user doesn't exist.
            return new WP_Error( 'error', '绑定邮箱失败', array( 'status' => 500 ) );
        } 
        $redis->del('mail_code/'.$email);
        $response = rest_ensure_response(respone_data('0','success','绑定邮箱成功'));
        return $response;  

    }

    //修改密码，需要发送邮件验证码
    function update_password($request){
        $openid= $request['openid'];
        $mail_code= $request['mail_code'];
        $new_password= $request['new_password'];
        $u = get_user_by_openid($openid);
        if ($u == null){
            return new WP_Error( 'error', 'openid无效', array( 'status' => 400 ) ); 
        }

        $user = get_user_by('ID',$u->user_id);
        $email = $user->data->user_email;
        
        //获取窗口
        $redis = get_redis();
        $code_list = $redis->keys('mail_code/'.$openid.'/*');
        $passed = false;
        foreach ($code_list as $key => $value) {
            $c = $redis->get($value);
            if($c){
                $c= json_decode($c,true);
                if($c['code'] == $mail_code) {
                    $passed = true;
                    $redis->del($value);
                    break;
                }
            }
        }
        if(!$passed){
            return new WP_Error( 'error', '验证码不正确', array( 'status' => 403 ) );
        }

        $user_id = wp_update_user( array( 'ID' => $u->user_id, 'user_pass' => $new_password  ) );
        if ( is_wp_error( $user_id ) ) {
            // There was an error, probably that user doesn't exist.
            return new WP_Error( 'error', '更新密码失败', array( 'status' => 500 ) );
        } 
        $redis->del('mail_code/'.$email);
        $response = rest_ensure_response(respone_data('0','success','更新密码成功'));
        return $response;  
    }

    //请求修改密码，发送验证码
    function request_update_password($request){
        $squirrelzoo_mailcode_freq = get_option('squirrelzoo_mailcode_freq');
        $squirrelzoo_mailcode_interval = get_option('squirrelzoo_mailcode_interval');
        $expire = get_option('squirrelzoo_mailcode_expire');
        $openid= $request['openid'];
        $u = get_user_by_openid($openid);
        if ($u == null){
            return new WP_Error( 'error', 'openid无效', array( 'status' => 400 ) ); 
        }
        $user = get_user_by('ID',$u->user_id);
        $email = $user->data->user_email;
        if (empty($email)){
            return new WP_Error( 'error', '请先绑定邮箱', array( 'status' => 403 ) ); 
        }
        //code 格式
        //mail_code/openid/email = {code,time}
        $redis = get_redis();
        //判断是否达到窗口大小
        $code_list = $redis->keys('mail_code/'.$openid.'/*');
        if(count($code_list) >= $squirrelzoo_mailcode_freq){
            return new WP_Error( 'error', '发送次数过多，请休息下再发送', array( 'status' => 403 ) ); 
        }
       
        //读取全部code
        $latest_time = 0;
        foreach ($code_list as $key => $value) {
             
            $c = $redis->get($value);
    
            if($c){
                $c= json_decode($c,true);
                if($c['time'] > $latest_time) {
                    $latest_time = $c['time'];
                }
            }
        }


      
        //判断上次发送时间
        if(time()-$latest_time < $squirrelzoo_mailcode_interval){
            return new WP_Error( 'error', '发送太频繁，请休息下再发送', array( 'status' => 403 ) ); 
        }

        //生成验证码
        $code = rand('100000','999999');
        $code_obj['code'] = $code;
        $code_obj['time'] = time();
        // $code_obj['code'] = 
        //保存到redis
        $redis->setex('mail_code/'.$openid.'/'.$email.'/'.$code_obj['time'], $expire,json_encode($code_obj));
        //发送
        $sent = wp_mail($email,'邮箱验证-密码修改','您的邮箱验证码为'.$code.'，有效期10分钟。多次发送，自动作废先前的验证码。');
        if ($sent){
            $result = respone_data('0','success',$sent);
        }else{
            $result = respone_data('-1','failed',$sent);
        }

        $response = rest_ensure_response($result);
        return $response; 
    }

    function get_user_by_id($request){
        $id= $request['id'];
        $user = get_user_by('ID',$id);
        $result['display_name'] = $user->data->display_name;
        $result['avatar'] = 'https://www.squirrelzoo.com/wp-content/uploads/2019/06/2019061105290828-150x150.png';
        $xxx  = get_user_meta($id,'squirrelzoo_avatar',true);
        if (!empty($xxx)){
            $result['avatar'] = $xxx;
        }
        if(!$result['avatar']){
            $result['avatar'] = '';
        }
        $response = rest_ensure_response(respone_data('0','success',$result));
        return $response;     
    }

    //---------------------------------------------

    function open_api_check($request){
        return true;
    }

    function get_openid_permissions_check($request){
        $appid = get_option('squirrelzoo_appid');
        $appsecret = get_option('squirrelzoo_secret');

        if(empty($appid) || empty($appsecret) ){
            return new WP_Error( 'error', 'appid或appsecret为空', array( 'status' => 500 ) );
        }
        return true;
    }


}