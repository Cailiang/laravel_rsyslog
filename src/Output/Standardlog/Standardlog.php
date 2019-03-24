<?php

namespace Output\Standardlog;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;

class Standardlog
{

    protected $channel;

    /**
     * Packagetest constructor
     */
    public function __construct()
    {

    }

    /**
     * @param $content
     * @param $message
     * @param int $label
     * @param string $channel
     */
    public function debug($content, $message, $label = 1, $channel = 'papertrail')
    {
        $this->channel = $channel;
        $this->getMessage('debug', $label, $message, $content);
    }

    /**
     * @param $content
     * @param $message
     * @param int $label
     * @param string $channel
     */
    public function info($content, $message, $label = 1, $channel = 'papertrail')
    {
        $this->channel = $channel;
        $this->getMessage('info', $label, $message, $content);
    }

    /**
     * @param $content
     * @param $message
     * @param int $label
     * @param string $channel
     */
    public function notice($content, $message, $label = 1, $channel = 'papertrail')
    {
        $this->channel = $channel;
        $this->getMessage('notice', $label, $message, $content);
    }

    /**
     * @param $content
     * @param $message
     * @param int $label
     * @param string $channel
     */
    public function warning($content, $message, $label = 1, $channel = 'papertrail')
    {
        $this->channel = $channel;
        $this->getMessage('warning', $label, $message, $content);
    }

    /**
     * @param $content
     * @param $message
     * @param int $label
     * @param string $channel
     */
    public function error($content, $message, $label = 1, $channel = 'papertrail')
    {
        $this->channel = $channel;
        $this->getMessage('error', $label, $message, $content);
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

        $host = $_SERVER['HTTP_HOST'];

        $addr = $_SERVER['SERVER_ADDR'];

        $app_name = env('APP_NAME');

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


        $data = $host . ' ' . $addr . ' ' . $app_name . ' ' . json_encode($data, JSON_UNESCAPED_UNICODE);

        $this->writeLog($data);

    }

    /**
     * @param $data
     */
    protected function writeLog($data)
    {
        Log::channel($this->channel)->info($data);
    }
}






















