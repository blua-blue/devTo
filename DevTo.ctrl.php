<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Curl;
use Neoan3\Apps\Db;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Frame\Neoan;
use League\HTMLToMarkdown\HtmlConverter;

/**
 * Class DevTo
 * @package Neoan3\Components
 */
class DevTo extends Neoan
{

    /**
     * @var bool
     */
    private $markdown = false;
    private $apiKey;

    function getDevTo()
    {
        $test = json_decode(file_get_contents(__DIR__ . '/test.json'), true);
        $this->postDevTo($test);
    }

    /**
     * @param array $body
     *
     * @return array
     * @throws RouteException
     */
    function postDevTo(array $body)
    {

        $jwt = Stateless::restrict();
        // get dev.to api key
        try {
            $credentials = getCredentials();
            // check token
            if(!isset($_SERVER['HTTP_TOKEN']) || $_SERVER['HTTP_TOKEN'] !== $credentials['blua_devto']['token']){
                return ['webhook' => 'denied'];
            }


            $this->apiKey = $this->getApiKey($credentials);

            switch ($body['event']) {
                case 'created':
                case 'updated':
                    // find existing
                    $update = $this->investigateStoreObject($body['payload']['store']);
                    $devBody = $this->transformPayload($body['payload']);
                    $this->sendToDevTo($devBody, $update);
                    break;
                case 'deleted':
                    break;
            }
        } catch (\Exception $e) {
            throw new RouteException('Unable to execute dev.to plugin', 500);
        }
        return ['webhook' => 'received'];
    }

    /**
     * @param $payload
     * @param $existingId
     *
     * @throws \Neoan3\Apps\DbException
     */
    private function sendToDevTo($payload, $existingId)
    {
        $header = [
            'User-Agent: neoan3',
            'Content-Type: application/json',
            'api-key: ' . $this->apiKey
        ];
        $url = 'https://dev.to/api/articles' . ($existingId ? '/' . $existingId : '');
        $call = Curl::curling($url, json_encode(['article' => $payload]), $header, $existingId ? 'PUT' : 'POST');
        if (isset($call['id']) && !$existingId) {
            Db::ask('article_store', [
                'article_id' => $payload['id'],
                'store_key' => 'dev-to-id',
                'value' => $call['id']
            ]);
        }
    }

    /**
     * @param $credentials
     *
     * @return mixed
     * @throws \Exception
     */
    private function getApiKey($credentials)
    {
        if (!isset($credentials['blua_devto']['api_key'])) {
            throw new \Exception('Key not set');
        }
        return $credentials['blua_devto']['api_key'];
    }

    /**
     * @param $payload
     *
     * @return array
     */
    private function transformPayload($payload)
    {
        $isLocal = strpos(base,'localhost') !== false;
        $article = [
            'title' => $payload['name'],
            'tags' => explode(',', $payload['keywords']),
            'description' => $payload['teaser'],
            'body_markdown' => $this->prepareContent($payload['content'])
        ];
        if(!$isLocal){
            $article['canonical_url'] = base . 'article/' . $payload['slug'] . '/';
        }
        if (!empty($payload['publish_date'])) {
            $article['published'] = true;
        }
        if ($payload['image_id'] && !$isLocal) {
            $article['cover_image'] = base . $payload['image']['path'];
        }

        return $article;
    }

    /**
     * @param $contentArray
     *
     * @return string
     */
    private function prepareContent($contentArray)
    {
        $content = '';
        foreach ($contentArray as $contentPart) {
            $content .= $this->convertContent($contentPart['content']);
        }
        return $content;
    }

    /**
     * @param $content
     *
     * @return string
     */
    private function convertContent($content)
    {
        if (!$this->markdown) {
            $this->markdown = new HtmlConverter(['strip_tags' => true]);
        }
        return $this->markdown->convert($content);
    }

    /**
     * @param $store
     *
     * @return bool
     */
    private function investigateStoreObject($store)
    {
        foreach ($store as $possible) {
            if ($possible['store_key'] === 'dev-to-id') {
                return $possible['value'];
            }
        }
        return false;
    }

}
