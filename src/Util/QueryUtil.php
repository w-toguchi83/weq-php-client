<?php

namespace WeqClient\Util;

/**
 * クエリユーティリティー.
 */
class QueryUtil
{
    /**
     * クエリ文字列を `Weq` にリクエストできる形に整形して返す.
     * @param string $query クエリ文字列
     * @return string
     */
    public static function format(string $query)
    {
        // JSONに改行があることで悩みたくないので除去
        return trim(str_replace(["\r\n", "\n", "\r"], ' ', $query));
    }
}
