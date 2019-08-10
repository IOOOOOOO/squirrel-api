<?php 
//禁止直接访问
if ( ! defined( 'ABSPATH' ) ) exit;
function users_columns( $columns ){
    // $columns[ 'avatar' ] = __( '头像' );    
    return $columns;
}
function  output_users_columns( $var, $columnName, $userId ){
    switch( $columnName ) {
		case "avatar" :
			return getAvatar($userId);
	}

}

function  getAvatar($userId)
{
	$avatar= get_user_meta( $userId, 'squirrelzoo_avatar', true);
	if(empty($avatar))
	{		
		$avatarImg ='<img  src="'.plugins_url().'/'.SQUIRREL_API_PLUGIN_NAME.'/includes/images/timg.jpeg"  width="30px" heigth="30px" style="border-radius:50%"/>';
	}
	else{
		$avatarImg ='<img  src="'.$avatar.'"  width="30px" heigth="30px" style="border-radius:50%"/>';
		
	}

	return $avatarImg;

}

//头像处理
add_filter( 'get_avatar' , 'squirrel_custom_avatar' , 1 , 5 );
function squirrel_custom_avatar( $avatar, $id_or_email, $size, $default, $alt) {
	if (filter_var($id_or_email, FILTER_VALIDATE_EMAIL)) {//判断是否为邮箱
		$email = $id_or_email;//用户邮箱
		$user = get_user_by( 'email', $email );//通过邮箱查询用户信息
	}else{
		$uid = (int) $id_or_email;//获取用户 ID
		$user = get_user_by( 'id', $uid );//通过 ID 查询用户信息
	}

	$avatar = get_user_meta($user->ID,'squirrelzoo_avatar',true);
	if(empty($avatar)){		
		$avatar =plugins_url().'/'.SQUIRREL_API_PLUGIN_NAME.'/includes/images/timg.jpeg';
	}

    $avatar = "<img src={$avatar} class='avatar avatar-{$size} photo' style='height:{$size}px;width:{$size}px;border-radius:50%'/>";
    return $avatar;
}


add_action('show_user_profile','squirrel_edit_user_avatar_profile');
add_action('edit_user_profile','squirrel_edit_user_avatar_profile');
function squirrel_edit_user_avatar_profile($profileuser){
	
	
	$user_avatar = get_user_meta($profileuser->ID,'squirrelzoo_avatar',true);
	if(empty($user_avatar)){		
		$user_avatar =plugins_url().'/'.SQUIRREL_API_PLUGIN_NAME.'/includes/images/timg.jpeg';
	}
	echo '<table class="form-table">
			<tbody>

			<tr class="user-profile-picture">
				<th style="color:blue">头像</th>
				<td>
					<img src="'.$user_avatar.'" class="avatar avatar-96 photo squirrelzoo_upload" style="height:96px;width:96px;border-radius:50%;cursor: pointer">
				</td>
			</tr>
			<input style="display:none" id="squirrel_avatar_input" name="squirrelzoo_avatar" value="'.$user_avatar.'"/>
			</tbody>
			</table>';

}

add_action('personal_options_update','squirrel_edit_user_avatar_profile_update');
add_action('edit_user_profile_update','squirrel_edit_user_avatar_profile_update');
function squirrel_edit_user_avatar_profile_update($user_id){
	if(!empty($_POST['squirrelzoo_avatar'])){
		update_user_meta( $user_id, 'squirrelzoo_avatar', $_POST['squirrelzoo_avatar'] );
	}else{
		if(get_user_meta( $user_id, 'squirrelzoo_avatar', true )){
			delete_user_meta( $user_id, 'squirrelzoo_avatar' );
		}
	}
}

// 后台表单 JS
add_action('admin_enqueue_scripts', 'squirrel_upload_image_enqueue_scripts');
function squirrel_upload_image_enqueue_scripts() {
	wp_enqueue_media();
	wp_enqueue_script('squirrel-api-setting',  plugins_url().'/squirrel-api/includes/js/upload.js', array('jquery'));
}

//移除多余的





