<?php
session_start();

list($mod, $type, $cb) = $paths;
$cb = preg_replace("[^a-z]", "", $cb);

if ($type == 's') {
    // 处理新浪
    if ('cb' != $cb) {
        $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
        $code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );
        $code_url = str_replace('&amp;', '&', $code_url);
        $_SESSION['back'] = $cb;
        header("Location: $code_url");
    } else {
        $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = WB_CALLBACK_URL;
            try {
                $token = $o->getAccessToken( 'code', $keys ) ;
            } catch (OAuthException $e) {
                header('Content-type: text/html; charset=utf-8');
                echo "<h1>取token异常</h1>";
                var_dump($e);
            }
        }
        if ($token) {
            // 授权成功
            $_SESSION['token'] = $token;
            setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
            header("Location: /app/$cb/success/s");
        } else {
            // 授权失败
            header('Content-type: text/html; charset=utf-8');
            echo '授权失败';
        }
    }
} else if ($type == 't') {
    // 处理腾讯    
    OpenSDK_Tencent_Weibo::init($appkey, $appsecret);
    if (!$cb) {
        $mini=true;
        $request_token = OpenSDK_Tencent_Weibo::getRequestToken($callback);
        $url = OpenSDK_Tencent_Weibo::getAuthorizeURL($request_token);
        $_SESSION['back'] = $cb;
        header('Location: ' . $url);
    } else if ('cb' == $cb) {
        if (OpenSDK_Tencent_Weibo::getAccessToken($_GET['oauth_verifier'])) {
            header("Location: /app/$cb/success/t");
        } else {
            header('Content-type: text/html; charset=utf-8');
            echo '授权失败';
        }
    } else {
        var_dump($paths);
        print_r($_SERVER);
    }
}

