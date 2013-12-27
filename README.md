company
=======

多用户企业管理系统

nginx配置 rewrite
server {
         listen       80;
         server_name  qxd.com;

        #access_log   logs/access_log;
        #error_log    logs/error_log;

         root    /home/zhanglei/html/qxd;

         log_not_found       off;

        if (!-e $request_filename) {
            rewrite "^/(?!qx-admin|id-code)(.*)$" /index.php/$1 last;
    		rewrite "^/(qx-admin|id-code)(/.*)$" /$1.php/$2 last;
        }

         location / {
             index  index.php index.html index.htm;
         }

          location ~ .*\.(gif|jpg|jpeg|png|bmp|swf|js|css|ico|taobao)$ {
             access_log off;
         }

      location ~* ^(.+\.php)(.*)$ {
        fastcgi_pass        127.0.0.1:9000;
        fastcgi_index       index.php;
        fastcgi_hide_header X-Powered-By;
        fastcgi_intercept_errors on;
        fastcgi_buffers     16 8k;
        fastcgi_buffer_size 8k;
        fastcgi_busy_buffers_size 16k;
        fastcgi_param       PATH_INFO   $2;
        include             fastcgi_params;
        fastcgi_param       SCRIPT_URI http://$server_name$uri;
        fastcgi_param       HTTP_REFERER $http_referer;
        fastcgi_param       HTTP_HOST $http_host;
        fastcgi_param       HTTP_ACCEPT $http_accept;
      }
}
