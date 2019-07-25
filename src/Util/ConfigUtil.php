<?php

namespace WeqClient\Util;

use WeqClient\Joint\ConfigJoint;

/**
 * 設定ユーティリティ.
 */
class ConfigUtil
{
    /**
     * リクエストするAPIのベースとなるURLを作成して返す.
     * @param ConfigJoint $config 設定
     * @return string
     */
    public static function makeBaseUrl(ConfigJoint $config)
    {
        $scheme = $config->isSecure() ? 'https' : 'http';

        return sprintf('%s://%s', $scheme, $config->getHost());
    }
}
