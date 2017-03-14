<?php

namespace GeneralSettingsMcs;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Psr7\Request;

/**
 * Class Processor
 *
 * @package GeneralSettingsMcs
 */
class Processor
{
    protected $endpoint;

    /**
     * Processor constructor.
     *
     * @param $endpoint
     *
     * @throws \Exception
     */
    public function __construct($endpoint)
    {
        if (null === $endpoint) {
            throw new \Exception(
                "General Settings service: endpoint is null"
            );
        }
        $this->endpoint = $endpoint;
    }

    /**
     * @return array
     */
    public function ping()
    {
        $request = new Request(
            'get',
            $this->getPath('/ping'),
            ['content-type' => 'application/json']
        );
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function create($data)
    {
        $request = new Request(
            'post',
            $this->getPath('/locationGroups'),
            ['content-type' => 'application/json'],
            json_encode($data)
        );
        $response = $this->send($request);
        return $response;
    }

    /**
     * @return array
     */
    public function read()
    {
        $request = new Request(
            'get',
            $this->getPath('/locationGroups'),
            ['content-type' => 'application/json']
        );
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param $param
     *
     * @return array
     */
    public function readOne($param)
    {
        $request = new Request(
            'get',
            $this->getPath(sprintf('/locationGroups/%s', $param)),
            ['content-type' => 'application/json']
        );
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param $data
     * @param $param
     *
     * @return array
     */
    public function update($data, $param)
    {
        $request = new Request(
            'put',
            $this->getPath(sprintf('/locationGroups/%s', $param)),
            ['content-type' => 'application/json'],
            json_encode($data)
        );
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param $param
     *
     * @return array
     */
    public function delete($param)
    {
        $request = new Request(
            'delete',
            $this->getPath(sprintf('/locationGroups/%s', $param)),
            ['content-type' => 'application/json']
        );
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getPath($path)
    {
        return $this->endpoint . $path;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function send(Request $request)
    {
        $client = new GuzzleClient();
        try {
            $response = $client->send($request);
            $data = [
                'body'       => json_decode($response->getBody(), true),
                'headers'    => [],
                'statusCode' => $response->getStatusCode()
            ];
            return $data;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            return [
                'body'       => [],
                'headers'    => [],
                'statusCode' => 204
            ];
        } catch (GuzzleClientException $e) {
            if ($e->getCode() >= 400 && $e->getCode() <= 499) {
                $message = $e->getResponse()->getBody()->getContents();
                $message = json_decode($message, true);
                $message = isset($message['message']) ? $message['message']
                    : "Something bad happened with General Settings service";
                throw new \Exception($message, $e->getCode());
            } else {
                $message = $this->formatErrorMessage($e);
                throw new \Exception(json_encode($message), 0, $e);
            }
        }
    }

    /**
     * @param $httpException
     *
     * @return array
     */
    public function formatErrorMessage($httpException)
    {
        $message = [
            'message'  => "Something bad happened with Assets service",
            'request'  => [
                'headers' => $httpException->getRequest()->getHeaders(),
                'body'    => $httpException->getRequest()->getBody()
            ],
            'response' => [
                'headers' => $httpException->getResponse()->getHeaders(),
                'body'    => $httpException->getResponse()->getBody()
                    ->getContents(),
                'status'  => $httpException->getResponse()->getStatusCode()
            ]
        ];
        return $message;
    }
}
