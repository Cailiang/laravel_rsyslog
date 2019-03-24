

### Installation

```shell
$ composer require "format_rsyslog/laravel_rsyslog" -vvv
```
### 要求
laravel 版本 >= 5.6

### 注册

config -> app.php -> providers 添加 Output\Standardlog\StandardlogServiceProvider::class
config -> app.php -> aliases 添加 "Standardlog" => Output\Standardlog\Facades\Standardlog::class
 
### env 配置
PAPERTRAIL_URL = （rsyslog 服务器 ip）
PAPERTRAIL_PORT = 514
### 使用

项目中添加 use Standardlog


        Standardlog::info($content,  $message, $label, $channel);
        Standardlog::debug($content,  $message, $label, $channel);
        Standardlog::error($content,  $message, $label, $channel');
        Standardlog::warning$content,  $message, $label, $channel);
        Standardlog::notice($content,  $message, $label, $channel);
		
参数含义：
content： 错误内容
message:  错误提示
label：错误类型 1：业务   2： 代理   3：网络    默认为 1
channel： 默认 papertrail 以请求 ip 方式 与 rsyslog 服务器交互
