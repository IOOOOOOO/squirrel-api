<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


function weixinapp_create_menu() {
    // 创建新的顶级菜单
    add_menu_page('松鼠API设置', '松鼠API设置', 'administrator', 'squirrel_api_config', 'weixinapp_settings_page', plugins_url('squirrel-api/includes/images/logo.png'),null);
    // 调用注册设置函数
    add_action( 'admin_init', 'register_weixinappsettings' );
}

function get_jquery_source() {
        $url = plugins_url('',__FILE__); 
        wp_enqueue_style("squirrelcss", plugins_url()."/squirrel-api/includes/js/style.css", false, "1.0", "all");
        wp_enqueue_script("squirrel_superslide", plugins_url()."/squirrel-api/includes/js/jquery.SuperSlide.2.1.1.js", false, "1.0", "all");
        wp_enqueue_script("squirrel_message", plugins_url()."/squirrel-api/includes/js/message.js", false, "1.0", "all");
}

function register_weixinappsettings() {
    // 注册设置
    register_setting( 'weixinapp-group', 'squirrelzoo_appid' );
    register_setting( 'weixinapp-group', 'squirrelzoo_secret' );
    register_setting( 'weixinapp-group', 'squirrelzoo_banners' );

    register_setting( 'weixinapp-group', 'squirrelzoo_redis_host' );
    register_setting( 'weixinapp-group', 'squirrelzoo_redis_port' );
    register_setting( 'weixinapp-group', 'squirrelzoo_redis_db' );
    register_setting( 'weixinapp-group', 'squirrelzoo_redis_password' ); 

    register_setting( 'weixinapp-group', 'squirrelzoo_mailcode_interval' ); 
    register_setting( 'weixinapp-group', 'squirrelzoo_mailcode_freq' ); 
    register_setting( 'weixinapp-group', 'squirrelzoo_mailcode_expire' ); 

    register_setting( 'weixinapp-group', 'squirrelzoo_id_min' ); 
    register_setting( 'weixinapp-group', 'squirrelzoo_id_max' ); 
    register_setting( 'weixinapp-group', 'squirrelzoo_id_available' ); 

    
}

function weixinapp_settings_page() {

if (!empty($_REQUEST['settings-updated'])){
    echo '<div id="message" class="updated fade"><p><strong>设置已保存</strong></p></div>';

} 
?>
    <div class="suCai17-content">
            <div class="suCai17-background clearfix">
                <div class="suCai17-nav hd">
                    <h2 class="suCai17-logo">
                        <a href="https://www.squirrelzoo.com" target="_blank">
                            <img src="<?= plugins_url('squirrel-api/includes/images/logo2.png') ?>" alt="">
                        </a>
                    </h2>
                    <ul class="suCai17-nav-list ">
                        <li class="suCai17-item">
                            <span class="suCai17-title">Redis配置</span>
                        </li>
                        <li class="suCai17-item ">
                            <span class="suCai17-title">小程序配置</span>
                        </li>
                        <li class="suCai17-item ">
                            <span class="suCai17-title">验证码流控</span>
                        </li>
                        <li class="suCai17-item on">
                            <span class="suCai17-title">用户池控制</span>
                        </li>
                        <li class="suCai17-item ">
                            <span class="suCai17-title">关于</span>
                        </li>
                    </ul>
                </div>
                
                <div class="suCai17-info bd">
                    <ul style="display: block;">
                        <li>
                            <div class="m-label">Redis地址</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_redis_host"  value="<?php echo esc_attr( get_option('squirrelzoo_redis_host') ); ?>" />
                            </div>

                            <div class="m-label">Redis端口</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_redis_port" value="<?php echo esc_attr( get_option('squirrelzoo_redis_port') ); ?>"/>
                            </div>

                            <div class="m-label">Redis数据库</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_redis_db" value="<?php echo esc_attr( get_option('squirrelzoo_redis_db') ); ?>" />
                            </div>
                            <div class="m-label">Redis密码</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_redis_password" value="<?php echo esc_attr( get_option('squirrelzoo_redis_password') ); ?>"/>
                            </div>
                            <a class="m-btn" onclick="test_redis()">测试连接</a>
                            <a class="m-btn" onclick="saveRedisConf()">保存设置</a>
                        </li>
                    </ul>
                    <ul style="display: none;">
                        <li>
                            <div class="m-label">AppID</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_appid" value="<?php echo esc_attr( get_option('squirrelzoo_appid') ); ?>"/>
                            </div>

                            <div class="m-label">AppSecret</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_secret" value="<?php echo esc_attr( get_option('squirrelzoo_secret') ); ?>"/>
                            </div>

                            <div class="m-label">Banner文章ID列表</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_banners" value="<?php echo esc_attr( get_option('squirrelzoo_banners') ); ?>"/>
                            </div>
                            <a class="m-btn" onclick="saveWXConf()">保存设置</a>
                        </li>
                    </ul>
                    <ul style="display: none;">
                        <li>
                            <div class="m-label">最小时间间隔（秒）</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_mailcode_interval" value="<?php echo esc_attr( get_option('squirrelzoo_mailcode_interval') ); ?>"/>
                            </div>

                            <div class="m-label">每个用户发送窗口大小</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_mailcode_freq" value="<?php echo esc_attr( get_option('squirrelzoo_mailcode_freq') ); ?>"/>
                            </div>

                            <div class="m-label">过期时长（秒）</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_mailcode_expire" value="<?php echo esc_attr( get_option('squirrelzoo_mailcode_expire') ); ?>"/>
                            </div>
                            <a class="m-btn" onclick="saveCodeConf()">保存设置</a>
                        </li>
                    </ul>
                    <ul style="display: none;">
                        <li>
                        
                            <div class="m-label">最小ID</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_id_min" value="<?php echo esc_attr( get_option('squirrelzoo_id_min') ); ?>"/>
                            </div>

                            <div class="m-label">最大ID</div>
                            <div class="m-form-item">
                                <input class="m-form-input" type="text" id="squirrelzoo_id_max" value="<?php echo esc_attr( get_option('squirrelzoo_id_max') ); ?>"/>
                            </div>

                            <div class="m-label">可用容量（点击刷新获取）</div>
                            <div class="m-form-item">
                                <input class="m-form-input" disabled="true" readonly="readonly" type="text" id="squirrelzoo_id_available" />
                                 <a class="m-btn" onclick="get_id_pool_size()" >刷新</a>
                            </div>
                            <a class="m-btn" onclick="cleanIdPool()">清空</a>
                            <a class="m-btn" onclick="reInitIdPool()" >扩容</a>

                    
                        </li>
                    </ul>
                    <ul style="display: none;">
                        <li>
                            <div style="float: left;padding: 20px">
                                <div style="font-size: 15px;width: 150px;text-align: center;padding: 10px">微信扫一扫体验下</div>
                                <img style="width: 150px" src="<?= plugins_url('squirrel-api/includes/images/gh_735272938124_344.jpg') ?>"/>
                            </div>

                            <div style="float: left;padding: 20px">
                                <div style="font-size: 15px;width: 150px;text-align: center;padding: 10px">微信捐赠支持</div>
                                <img style="width: 150px" src="<?= plugins_url('squirrel-api/includes/images/2019072508503543.jpg') ?>"/>
                            </div>
                        </li>
                    
                    </ul>
                </div>
            </div>
        </div>
        <?php get_jquery_source(); ?>
        <script type="text/javascript">
            var token = '<?php
                function token(){
                    $tk = wp_generate_uuid4();
                    update_option('squirrel_setting_token',$tk);
                    return $tk;
                }
                echo token();
             ?>'
         </script>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                jQuery(".suCai17-background").slide({
                    autoPlay: false,
                    trigger: "click",
                    easing: "easeOutCirc",
                    delayTime: 1000
                });

                jQuery(".suCai17-side").slide({
                    titCell:"h3",
                    targetCell:"ul",
                    defaultIndex:1,
                    effect:"slideDown",
                    trigger:"click"
                });
            
            });

            function reInitIdPool(){
               $.ajax({
                type : "POST",
                url : "<?=site_url().'/wp-json/squirrel/v1/internal/re_init_id_pool' ?>",
                data : { 
                    "squirrelzoo_id_min" : $('#squirrelzoo_id_min').val(),
                    "squirrelzoo_id_max" : $('#squirrelzoo_id_max').val(),
                    "squirrelzoo_mailcode_expire" : $('#squirrelzoo_mailcode_expire').val(),
                    "token":token
                },
                success : function(result) {
                    if(result.code === '0'){
                        $('#squirrelzoo_id_available').val(result.data)
                        $.message({
                            message:result.message,
                            type:'success'
                        });
                    }else{
                        $.message({
                            message:result.message,
                            type:'error'
                        });
                    }
                }
                })
            }

            function saveRedisConf(){
                $.ajax({
                type : "POST",
                url : "<?=site_url().'/wp-json/squirrel/v1/internal/redis_conf' ?>",
                data : { 
                    "squirrelzoo_redis_host" : $('#squirrelzoo_redis_host').val(),
                    "squirrelzoo_redis_port" : $('#squirrelzoo_redis_port').val(),
                    "squirrelzoo_redis_db" : $('#squirrelzoo_redis_db').val(),
                    "squirrelzoo_redis_password" : $('#squirrelzoo_redis_password').val(),
                    "token":token
                },
                success : function(result) {
                    if(result.code === '0'){
                        $.message({
                            message:'保存成功',
                            type:'success'
                        });
                    }else{
                        $.message({
                            message:'保存失败',
                            type:'error'
                        });
                    }
                }
                })

            }

            function saveWXConf(){
                $.ajax({
                type : "POST",
                url : "<?=site_url().'/wp-json/squirrel/v1/internal/wx_conf' ?>",
                data : { 
                    "squirrelzoo_appid" : $('#squirrelzoo_appid').val(),
                    "squirrelzoo_secret" : $('#squirrelzoo_secret').val(),
                    "squirrelzoo_banners" : $('#squirrelzoo_banners').val(),
                    "token":token
                },
                success : function(result) {
                    if(result.code === '0'){
                        $.message({
                            message:'保存成功',
                            type:'success'
                        });
                    }else{
                        $.message({
                            message:'保存失败',
                            type:'error'
                        });
                    }
                }
                })
            }

            function saveCodeConf(){

                $.ajax({
                type : "POST",
                url : "<?=site_url().'/wp-json/squirrel/v1/internal/code_conf' ?>",
                data : { 
                    "squirrelzoo_mailcode_interval" : $('#squirrelzoo_mailcode_interval').val(),
                    "squirrelzoo_mailcode_freq" : $('#squirrelzoo_mailcode_freq').val(),
                    "squirrelzoo_mailcode_expire" : $('#squirrelzoo_mailcode_expire').val(),
                    "token":token
                },
                success : function(result) {
                    if(result.code === '0'){
                        $.message({
                            message:'保存成功',
                            type:'success'
                        });
                    }else{
                        $.message({
                            message:'保存失败',
                            type:'error'
                        });
                    }
                }
                })

            }

            function test_redis(){
                $.ajax({
                type : "POST",
                url : "<?=site_url().'/wp-json/squirrel/v1/internal/test_redis' ?>",
                data : { 
                    "token":token
                },
                success : function(result) {
                    if(result.code === '0'){
                        $.message({
                            message:'连接成功',
                            type:'success'
                        });
                    }else{
                        $.message({
                            message:'连接失败',
                            type:'error'
                        });
                    }
                },
                error(e){
                    $.message({
                        message:'连接失败',
                        type:'error'
                    });
                }
                })
            }
            
            function get_id_pool_size(){
                $.ajax({
                type : "POST",
                url : "<?=site_url().'/wp-json/squirrel/v1/internal/id_pool_size' ?>",
                data : { 
                    "token":token
                },
                success : function(result) {
                    if(result.code === '0'){
                        $('#squirrelzoo_id_available').val(result.data)
                       
                    }else{
                        $.message({
                            message:'获取可用容量失败',
                            type:'error'
                        });
                    }
                },
                error(e){
                    $.message({
                        message:'获取可用容量失败',
                        type:'error'
                    });
                }
                })
            }

            function cleanIdPool(){
                $.ajax({
                type : "POST",
                url : "<?=site_url().'/wp-json/squirrel/v1/internal/clean_id_pool' ?>",
                data : { 
                    "token":token
                },
                success : function(result) {
                    if(result.code === '0'){
                        $('#squirrelzoo_id_available').val(0)
                        $.message({
                            message:result.message,
                            type:'success'
                        });
                    }else{
                        $.message({
                            message:'获取可用容量失败',
                            type:'error'
                        });
                    }
                },
                error(e){
                    $.message({
                        message:'获取可用容量失败',
                        type:'error'
                    });
                }
                })
            }

        </script>
<?php }  