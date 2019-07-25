<?php

namespace WeqClient;

use Curl\Curl;
use STS\Backoff\Backoff;
use STS\Backoff\Strategies\ConstantStrategy;
use WeqClient\Joint\ConfigJoint;
use WeqClient\Joint\ClientJoint;
use WeqClient\Util\ConfigUtil;
use WeqClient\Util\QueryUtil;

/**
 * クライアント.
 */
class Client implements ClientJoint
{
    /** @var int リミット値. */
    const MAX_LIMIT = 1000;

    /** @var string ベースURL. */
    private $baseUrl;

    /** @var Backoff リトライ機能. */
    private $backoff;

    /** @var int タイムアウト値. */
    private $timeout;

    /**
     * コンストラクタ.
     * @param ConfigJoint $config 設定
     */
    public function __construct(ConfigJoint $config)
    {
        $this->baseUrl = ConfigUtil::makeBaseUrl($config);
        
        $backoff = new Backoff($config->getRetryCount());
        $backoff->setStrategy(new ConstantStrategy($config->getWaitTime()));
        $this->backoff = $backoff;

        $this->timeout = $config->getTimeout();
    }

    /**
     * クライアントインスタンスを生成して返す.
     * @param ConfigJoint $config 設定
     */
    public static function create(ConfigJoint $config)
    {
        return new static($config);
    }

    /**
     * 疎通結果を返す.
     * @return bool
     */
    public function ping()
    {
        try {
            return $this->backoff->run(function() {
                $endpointUrl = $this->baseUrl.'/api/ping';
                $curl = $this->newCurl($this->timeout);

                $curl->get($endpointUrl);
                $this->ifErrorThenThrow($curl);

                return true;
            });
        }catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 指定したリソースにクエリを発行し結果を返す.
     * @param string $resource リソース名
     * @param string $query    クエリ文字列
     * @param array  $binds    バインド変数
     * @return \Iterator 結果セット
     */
    public function fetch(string $resource, string $query, array $binds = [])
    {
        $endpointUrl = $this->baseUrl.'/api/fetch';
        $fmttedQuery = QueryUtil::format($query);
        $offset      = -1 * static::MAX_LIMIT;  // do-while の初回処理で `0` に変更される
        $limit       = static::MAX_LIMIT;
        $rows        = [];

        do {
            $offset += $limit;
            $params = [
                'resource' => $resource,
                'query'    => $fmttedQuery,
                'binds'    => $binds,
                'offset'   => $offset,
                'limit'    => $limit,
            ];

            $rows   = [];
            $result = $this->post($endpointUrl, $params);

            if (isset($result['rows'])) {
                $rows = $result['rows'];
                foreach ($rows as $row) {
                    yield $row;
                }
            }
        } while (!empty($rows));
    }

    /**
     * 指定リソースにクエリを発行し、指定範囲(offset ~ limit)の結果を返す.
     * @param string $resource リソース名
     * @param string $query    クエリ文字列
     * @param int    $offset   オフセット値
     * @param int    $limit    リミット値
     * @param array  $binds    バインド変数
     * @return array 結果セット配列
     */
    public function fetchRange(string $resource, string $query, int $offset, int $limit, array $binds = [])
    {
        $endpointUrl = $this->baseUrl.'/api/fetch';
        if ($limit > static::MAX_LIMIT) {
            throw new \Exception('limitの値が最大値を越えています');
        }

        $params = [
            'resource' => $resource,
            'query'    => QueryUtil::format($query),
            'binds'    => $binds,
            'offset'   => $offset,
            'limit'    => $limit,
        ];

        $result = $this->post($endpointUrl, $params);

        if (isset($result['rows'])) {
            return $result['rows'];
        } else {
            throw new \Exception('not found "rows"');
        }
    }

    /**
     * 指定したリソースにクエリを発行した場合のデータ取得件数を返す.
     * @param string $resource リソース名
     * @param string $query    クエリ文字列
     * @param array  $binds    バインド変数
     * @return int データ取得件数
     */
    public function count(string $resource, string $query, array $binds = [])
    {
        $endpointUrl = $this->baseUrl.'/api/count';
        $params = [
            'resource' => $resource,
            'query'    => QueryUtil::format($query),
            'binds'    => $binds,
        ];

        $result = $this->post($endpointUrl, $params);

        if (isset($result['count'])) {
            return (int) $result['count'];
        } else {
            throw new \Exception('not found "count"');
        }
    }

    /**
     * エンドポイントURLにパラメータをPOSTして結果を返す.
     * @param string $endpointUrl エンドポイントURL
     * @param array  $params      パラメータ
     * @return array
     */
    private function post(string $endpointUrl, array $params)
    {
        return $this->backoff->run(function() use ($endpointUrl, $params) {
            $curl = $this->newCurl($this->timeout, $json = true);
            $curl->post($endpointUrl, $params);
            $this->ifErrorThenThrow($curl);

            return $curl->response;
        });
    }

    /**
     * `Curl` インスタンスを生成して返す.
     * @param int  $timeout タイムアウト値
     * @param bool $isJson  JSONアプリケーションの場合は `true` を設定する
     * @return Curl
     */
    private function newCurl(int $timeout, $isJson = false)
    {
        $curl = new Curl();
        $curl->setTimeout($timeout);
        if ($isJson) {
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setDefaultJsonDecoder($assoc = true);
        }

        return $curl;
    }

    /**
     * `Curl` の実行結果がエラーだった場合は例外を投げる.
     * @param Curl $curl リクエスト実行後の `Curl`
     * @return bool
     */
    private function ifErrorThenThrow(Curl $curl)
    {
        if ($curl->error) {
            $response = $curl->response;
            if (is_array($response)) {
                $response = json_encode($response);
                $response = $response === false ? $curl->response : $response;
            }
            throw new \Exception($curl->errorCode.':'.$curl->errorMessage.':'.$response);
        }

        return true;
    }
}
