<?php

namespace WeqClient\Joint;

/**
 * 設定インターフェース.
 */
interface ConfigJoint
{
    /**
     * セキュア通信をする場合は `true` を返す.
     * @return bool
     */
    public function isSecure();

    /**
     * 通信するAPIサーバのホスト文字列を返す.
     * @return string
     */
    public function getHost();

    /**
     * タイムアウト値(単位:秒)を返す.
     * @return int
     */
    public function getTimeout();

    /**
     * API失敗時のリトライ回数を返す.
     * @return int
     */
    public function getRetryCount();

    /**
     * リトライ待機時間(単位:ミリ秒)を返す.
     * @return int
     */
    public function getWaitTime();
}
