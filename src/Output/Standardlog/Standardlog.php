<?php

namespace Output\Standardlog;

use Illuminate\Support\Facades\Log;

class Standardlog
{

    protected $tag;

    protected $alarm_level = ['error' => 1, 'critical' => 2, 'alert' => 3];

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
     */
    protected function writeLog($data, $func)
    {
        config(['logging.channels.papertrail.handler_with.ident' => $this->tag]);
        Log::channel('papertrail')->{$func}($data);
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






















