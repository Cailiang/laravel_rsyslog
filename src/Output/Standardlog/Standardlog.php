<?php

namespace Output\Standardlog;

use Illuminate\Support\Facades\Log;

class Standardlog
{

    protected $tag;

    protected $alarm_level = ['error' => 1, 'critical' => 2, 'alert' => 3];

    protected $sys_log_tag = [
        'debug' => 'LOG_DEBUG',
        'info' => 'LOG_INFO',
        'notice' => 'LOG_NOTICE',
        'warning' => 'LOG_WARNING',
        'error' => 'LOG_ERR',
        'critical' => 'LOG_CRIT',
        'alert' => 'LOG_ALERT',

    ];

    protected $now_sys_log_tag;

    /**
     * Packagetest constructor
     */
    public function __construct()
    {

    }

    /**
     * @param $message
     * @param $content
     * @param string $tag
     * @param int $label
     */
    public function debug($message, $content, $tag = '', $label = 1)
    {
        $this->tag = $tag ? $tag : env('APP_NAME');
        $this->getMessage('debug', $label, $message, $content);
    }

    /**
     * @param $message
     * @param $content
     * @param string $tag
     * @param int $label
     */
    public function info($message, $content, $tag = '', $label = 1)
    {
        $this->tag = $tag ? $tag : env('APP_NAME');
        $this->getMessage('info', $label, $message, $content);
    }

    /**
     * @param $message
     * @param $content
     * @param string $tag
     * @param int $label
     */
    public function notice($message, $content, $tag = '', $label = 1)
    {
        $this->tag = $tag ? $tag : env('APP_NAME');
        $this->getMessage('notice', $label, $message, $content);
    }

    /**
     * @param $message
     * @param $content
     * @param string $tag
     * @param int $label
     */
    public function warning($message, $content, $tag = '', $label = 1)
    {
        $this->tag = $tag ? $tag : env('APP_NAME');
        $this->getMessage('warning', $label, $message, $content);
    }

    /**
     * @param $message
     * @param $content
     * @param string $tag
     * @param int $label
     */
    public function error($message, $content, $tag = '', $label = 1)
    {
        $this->tag = $tag ? $tag : env('APP_NAME');
        $this->getMessage('error', $label, $message, $content);
    }

    /**
     * @param $message
     * @param $content
     * @param string $tag
     * @param int $label
     */
    public function critical($message, $content, $tag = '', $label = 1)
    {
        $this->tag = $tag ? $tag : env('APP_NAME');
        $this->getMessage('critical', $label, $message, $content);
    }

    /**
     * @param $message
     * @param $content
     * @param string $tag
     * @param int $label
     */
    public function alert($message, $content, $tag = '', $label = 1)
    {
        $this->tag = $tag ? $tag : env('APP_NAME');
        $this->getMessage('alert', $label, $message, $content);
    }

    /**
     * 组合信息
     * @param $level
     * @param $label
     * @param $message
     * @param $content
     */
    protected function getMessage($level, $label, $message, $content)
    {
        $this->now_sys_log_tag = $this->sys_log_tag[$level];

        if (in_array($level, array_keys($this->alarm_level))) {
            $this->sendAlarm($this->alarm_level[$level], $message, $content);
        }

        switch ($label) {
            case 1:
                $label_name = '业务';
                break;
            case 2:
                $label_name = '代理';
                break;
            case 3:
                $label_name = '网络';
                break;
            default:
                $label_name = '业务';
                break;
        }

        $data = ['level' => $level,
            'label' => $label_name,
            'message' => $message,
            'content' => $content];


        $data = time() . ' ' . json_encode($data, JSON_UNESCAPED_UNICODE);

        $this->writeLog($data, $level);

    }

    /**
     * @param $data
     * @param $func
     */
    protected function writeLog($data, $func)
    {
        if (env('APP_ENV') && env('APP_ENV') == 'local') {
            $this->writeLocalLog($data, $func);
        } else {
            $this->writeServerLog($data);

        }
    }

    /**
     * 测试时通过 udp 写日志
     * @param $data
     * @param $func
     */
    protected function writeLocalLog($data, $func)
    {
        config(['logging.channels.papertrail.handler_with.ident' => $this->tag]);
        Log::channel('papertrail')->{$func}($data);
    }

    /**
     * 在服务器上通过配置的 rsyslog 客户端写日志
     * @param $data
     */
    protected function writeServerLog($data)
    {
        openlog($this->tag, LOG_PID, LOG_USER);
        syslog($this->now_sys_log_tag, $data);
        closelog();
    }

    /**
     * @param $level
     * @param $message
     * @param $content
     * @return mixed
     */
    protected function sendAlarm($level, $message, $content)
    {
        $postUrl = env('ALARM_URL');
        $curlPost = ['business' => env('APP_NAME'),
            'function' => $content,
            'message' => $message,
            'env' => env('APP_ENV'),
            'alert_level' => $level];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);
        curl_close($ch);
    }


}






















