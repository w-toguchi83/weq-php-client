<?php

namespace WeqClient;

use WeqClient\Joint\ConfigJoint;

/**
 * 設定.
 */
class Config implements ConfigJoint
{
    /** @var bool セキュア通信の場合は `true` .*/
    private $secure;
    /** @var string 通信するホスト. */
    private $host;
    /** @var int タイムアウト値(秒). */
    private $timeout;
    /** @var int リトライ回数. */
    private $retryCount;
    /** @var int リトライ待機値(ミリ秒). */
    private $waitTime;

    /**
     * コンストラクタ.
     * @param string $host ホスト
     */
    public function __construct(string $host)
    {
        $this->host = $host;

        $this->secure     = true;
        $this->timeout    = 60;
        $this->retryCount = 1;
        $this->waitTime   = 300;
    }

    /**
     * 設定インスタンスを生成して返す.
     * @param string $host ホスト
     * @return Config
     */
    public static function create(string $host)
    {
        return new static($host);
    }

    /**
     * セキュア通信をする場合は `true` を返す.
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * セキュア通信するかしないかのフラグを設定する.
     * @param bool $bool フラグ値
     * @return $this
     */
    public function setSecure(bool $bool)
    {
        $this->secure = $bool;

        return $this;
    }

    /**
     * 通信するAPIサーバのホスト文字列を返す.
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * ホストを設定する.
     * @param string $host ホスト
     * @return $this
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * タイムアウト値(単位:秒)を返す.
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * タイムアウト値(秒)を設定する.
     * @param int $timeout タイムアウト値
     * @return $this
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * API失敗時のリトライ回数を返す.
     * @return int
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    /**
     * リトライ回数を設定する.
     * @param int $retryCount リトライ回数
     * @return $this
     */
    public function setRetryCount(int $retryCount)
    {
        $this->retryCount = $retryCount;

        return $this;
    }

    /**
     * リトライ待機時間(単位:ミリ秒)を返す.
     * @return int
     */
    public function getWaitTime()
    {
        return $this->waitTime;
    }

    /**
     * リトライ待機値(ミリ秒)を設定する.
     * @param int $waitTime リトライ待機値
     * @return $this
     */
    public function setWaitTime(int $waitTime)
    {
        $this->waitTime = $waitTime;

        return $this;
    }
}
