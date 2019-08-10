<?php 
//获取文章的第一张图片
function get_post_content_first_image($post_content){
    if(!$post_content){
        $the_post       = get_post();
        $post_content   = $the_post->post_content;
    } 

    preg_match_all( '/class=[\'"].*?wp-image-([\d]*)[\'"]/i', $post_content, $matches );
    if( $matches && isset($matches[1]) && isset($matches[1][0]) ){  
        $image_id = $matches[1][0];
        if($image_url = get_post_image_url($image_id)){
            return $image_url;
        }
    }

    preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
    if( $matches && isset($matches[1]) && isset($matches[1][0]) ){     
        return $matches[1][0];
    }
}

//获取文章图片的地址
function get_post_image_url($image_id, $size='full'){
    if($thumb = wp_get_attachment_image_src($image_id, $size)){
        return $thumb[0];
    }
    return false;   
}

function getPostImages($content,$postId){
    $content_first_image= get_post_content_first_image($content);
    $post_frist_image=$content_first_image;

    if(empty($content_first_image))
    {
        $content_first_image='';
    }

    if(empty($post_frist_image))
    {
        $post_frist_image='';
    }

    $post_thumbnail_image_150='';
    $post_medium_image_300='';
    $post_thumbnail_image_624=''; 

    $post_thumbnail_image='';

    $post_medium_image="";
    $post_large_image="";
    $post_full_image="";   

    $_data =array();

    if (has_post_thumbnail($postId)) {
        //获取缩略的ID
        $thumbnailId = get_post_thumbnail_id($postId);

        //特色图缩略图
        $image=wp_get_attachment_image_src($thumbnailId, 'thumbnail');
        $post_thumbnail_image=$image[0];
        $post_thumbnail_image_150=$image[0];
        //特色中等图
        $image=wp_get_attachment_image_src($thumbnailId, 'medium');
        $post_medium_image=$image[0];
        $post_medium_image_300=$image[0];
        //特色大图
        $image=wp_get_attachment_image_src($thumbnailId, 'large');
        $post_large_image=$image[0];
        $post_thumbnail_image_624=$image[0];
        //特色原图
        $image=wp_get_attachment_image_src($thumbnailId, 'full');
        $post_full_image=$image[0];

    }

    if(!empty($content_first_image) && empty($post_thumbnail_image))
     {
        $post_thumbnail_image=$content_first_image;
        $post_thumbnail_image_150=$content_first_image;
     }

     if(!empty($content_first_image) && empty($post_medium_image))
     {
        $post_medium_image=$content_first_image;
        $post_medium_image_300=$content_first_image;
        
     }

     if(!empty($content_first_image) && empty($post_large_image))
     {
        $post_large_image=$content_first_image;
        $post_thumbnail_image_624=$content_first_image;
     }

     if(!empty($content_first_image) && empty($post_full_image))
     {
        $post_full_image=$content_first_image;
     }

     //$post_all_images = get_attached_media( 'image', $postId);
     $post_all_images= get_post_content_images($content);

     $_data['post_frist_image']=$post_frist_image;
     $_data['post_thumbnail_image']=$post_thumbnail_image;
     $_data['post_medium_image']=$post_medium_image;
     $_data['post_large_image']=$post_large_image;
     $_data['post_full_image']=$post_full_image;
     $_data['post_all_images']=$post_all_images;

     $_data['post_thumbnail_image_150']=$post_thumbnail_image_150;
     $_data['post_medium_image_300']=$post_medium_image_300;
     $_data['post_thumbnail_image_624']=$post_thumbnail_image_624;
    
    
    $_data['content_first_image']=$content_first_image; 


    return  $_data; 
           

}

function get_post_content_images($post_content){
    if(!$post_content){
        $the_post       = get_post();
        $post_content   = $the_post->post_content;
    } 

    

    preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
    $images=array();
    if($matches && isset($matches[1]))
    {
        $_images=$matches[1]; 
       
        for($i=0; $i<count($matches[1]);$i++) {
            $imageurl['imagesurl'] =$matches[1][$i];
            $imageurl['id'] ='image'.$i;
            $images[]=$imageurl;
            
        }
        
        return $images;

    }

    return null;
        
}

function get_content_post($url,$post_data=array(),$header=array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_AUTOREFERER,true);
    $content = curl_exec($ch);
    $info = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
    $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($code == "200"){
        return $content;
    }else{
        return "error";
    }
}

//发起https请求
function https_request($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl,  CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);  
    $data = curl_exec($curl);
    if (curl_errno($curl)){
        return 'ERROR';
    }
    curl_close($curl);
    return $data;
}


function https_curl_post($url,$data,$type){
        if($type=='json'){
            //$headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
            $data=json_encode($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
        $data = curl_exec($curl);
        if (curl_errno($curl)){
            return 'ERROR';
        }
        curl_close($curl);
        return $data;
    }


function time_tran($the_time){
    $now_time = date("Y-m-d H:i:s",time()+8*60*60); 
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if($dur < 0){
        return $the_time; 
    }else{
        if($dur < 60){
            return $dur.'秒前'; 
        }else{
            if($dur < 3600){
             return floor($dur/60).'分钟前'; 
         }
         else{
                 if($dur < 86400){
                     return floor($dur/3600).'小时前'; 
                 }
                 else{
                   if($dur < 259200){//3天内
                     return floor($dur/86400).'天前';
                    }
                     else{
                         return date("Y-m-d",$show_time); 
                     }
                }
            }
        }
    }
}

/**
 * 检验数据的真实性，并且获取解密后的明文.
 * @param $sessionKey string 用户在小程序登录后获取的会话密钥
 * @param $appid string 小程序的appid
 * @param $encryptedData string 加密的用户数据
 * @param $iv string 与用户数据一同返回的初始向量
 * @param $data string 解密后的原文
 *
 * @return int 成功0，失败返回对应的错误码
 */
function decrypt_data( $appid, $sessionKey, $encryptedData, $iv, &$data ) {
    
    $errors = array(
        'OK'                => 0,
        'IllegalAesKey'     => -41001,
        'IllegalIv'         => -41002,
        'IllegalBuffer'     => -41003,
        'DecodeBase64Error' => -41004
    );
    
    if (strlen($sessionKey) != 24)
    {
        return $errors['IllegalAesKey'];
    }
    $aesKey=base64_decode($sessionKey);

    
    if (strlen($iv) != 24)
    {
        return $errors['IllegalIv'];
    }
    $aesIV=base64_decode($iv);

    $aesCipher=base64_decode($encryptedData);

    $result=openssl_decrypt( $aesCipher, 'AES-128-CBC', $aesKey, 1, $aesIV);

    $dataObj=json_decode( $result );
    if( $dataObj  == NULL )
    {
        return $errors['IllegalBuffer'];
    }
    if( $dataObj->watermark->appid != $appid )
    {
        return $errors['IllegalBuffer'];
    }
    $data = $result;
    return $errors['OK'];
}

function get_client_ip()
{
    foreach (array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                //会过滤掉保留地址和私有地址段的IP，例如 127.0.0.1会被过滤
                //也可以修改成正则验证IP
                if ((bool) filter_var($ip, FILTER_VALIDATE_IP,
                                FILTER_FLAG_IPV4 |
                                FILTER_FLAG_NO_PRIV_RANGE |
                                FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }
    return null;
}

function filterEmoji($str)
{
  $str = preg_replace_callback(
    '/./u',
    function (array $match) {
      return strlen($match[0]) >= 4 ? '' : $match[0];
    },
    $str);

  return $str;
}

function  getUserLevel($userId)
{
    global $wpdb;
    $sql =$wpdb->prepare("SELECT  t.meta_value
            FROM
                ".$wpdb->usermeta." t
            WHERE
                t.meta_key = '". $wpdb->prefix."user_level' 
            AND t.user_id =%d",$userId);

    $level =$wpdb->get_var($sql); 
    $levelName ="订阅者";
    switch($level)
    {
        case "10":
        $levelName="管理者";
        break;

        case "7":
        $levelName="编辑";
        break;

        case "2":
        $levelName="作者";
        break;

        case "1":
        $levelName="贡献者";
        break;

        case "0":
        $levelName="订阅者";
        break;

    }
    $userLevel["level"]=$level;
    $userLevel["levelName"]=$levelName;
    return $userLevel;

}



function user_exists_by_openid($openid){
    global $wpdb;
    $sql = "select * from ".$wpdb->usermeta." where meta_key='squirrelzoo_openid' and meta_value='".$openid."'";
    $c = count($wpdb->get_results($sql));
    return $c > 0;
}

function get_user_by_openid($openid){
    global $wpdb;
    $sql = "select * from ".$wpdb->usermeta." where meta_key='squirrelzoo_openid' and meta_value='".$openid."'";
    $user_list = $wpdb->get_results($sql);
    $count = count($user_list);
    return $count > 0?$user_list[0] : null;
}






function respone_data($code,$message,$data=null){
    $result["code"]=$code;
    $result["message"]=$message;
    $result["data"]=$data;
    return $result;
}

function get_redis(){
    $squirrelzoo_redis_host = get_option('squirrelzoo_redis_host');
    $squirrelzoo_redis_port = get_option('squirrelzoo_redis_port');
    $squirrelzoo_redis_db = get_option('squirrelzoo_redis_db');
    $squirrelzoo_redis_password = get_option('squirrelzoo_redis_password');
    if(empty($squirrelzoo_redis_host) || empty($squirrelzoo_redis_port) ){
        return false;
    }
    $redis = new Redis();
    $redis->connect($squirrelzoo_redis_host, intval($squirrelzoo_redis_port)); //连接Redis
    if(!empty($squirrelzoo_redis_password)){
        $redis->auth($squirrelzoo_redis_password); //密码验证
    }
    if(!empty($squirrelzoo_redis_db)){
        $redis->select(intval($squirrelzoo_redis_db));//选择数据库2
    }else{
        $redis->select(0);//选择数据库
    }
    return $redis;
}
