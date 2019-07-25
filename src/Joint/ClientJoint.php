<?php

namespace WeqClient\Joint;

/**
 * Clientインターフェース
 */
interface ClientJoint
{
    /**
     * 疎通結果を返す.
     * @return bool
     */
    public function ping();

    /**
     * 指定したリソースにクエリを発行し結果を返す.
     * @param string $resource リソース名
     * @param string $query    クエリ文字列
     * @param array  $binds    バインド変数
     * @return \Iterator 結果セット
     */
    public function fetch(string $resource, string $query, array $binds = []);

    /**
     * 指定リソースにクエリを発行し、指定範囲(offset ~ limit)の結果を返す.
     * @param string $resource リソース名
     * @param string $query    クエリ文字列
     * @param int    $offset   オフセット値
     * @param int    $limit    リミット値
     * @param array  $binds    バインド変数
     * @return array 結果セット配列
     */
    public function fetchRange(string $resource, string $query, int $offset, int $limit, array $binds = []);

    /**
     * 指定したリソースにクエリを発行した場合のデータ取得件数を返す.
     * @param string $resource リソース名
     * @param string $query    クエリ文字列
     * @param array  $binds    バインド変数
     * @return int データ取得件数
     */
    public function count(string $resource, string $query, array $binds = []);
}
