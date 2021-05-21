<?php

/**
 * Name: 身份证SDK
 * User: Silent
 * Date: 2021-05-21
 * Time: 15:27:08
 */

namespace Xinmoumomo\Idcard;

class IdCardSDK
{
    private static $config = [];

    public function __construct($config)
    {
        self::$config = $config;
    }

    /**
     * 身份证OCR识别-身份证图片识别-身份证识别-二代居民身份证OCR识别-身份证信息识别.
     *
     * @param $image
     * @param  string  $side
     * @return false|string
     */
    public function card($image, $side = 'front')
    {
        // 图片 base64 加密
//        $img_data = base64_encode(file_get_contents($image));
        $img_data = urlencode(base64_encode(file_get_contents($image)));

        $headers = [];
        array_push($headers, 'Authorization:APPCODE ' . self::$config['appcode']);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, 'Content-Type' . ':' . 'application/x-www-form-urlencoded; charset=UTF-8');
        $querys = '';
        $bodys  = 'image=' . $img_data . '&side=' . $side;
        $url    = self::$config ['host'] . self::$config ['path'];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::$config ['method']);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        if (1 == strpos('$' . self::$config ['host'], 'https://')) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $result      = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rheader     = substr($result, 0, $header_size);
        $rbody       = substr($result, $header_size);
        $httpCode    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode == 403) {
            return [
                'code' => $httpCode,
                'msg'  => '当前的API配额已耗尽，请重新购买',
                'data' => '',
            ];
        }

        return $rbody;
    }

    /**
     * 印刷文字识别-身份证识别.
     * @param $img_path
     * @return array
     */
    public function image($img_path)
    {
        $headers = [];
        array_push($headers, 'Authorization:APPCODE ' . self::$config ['appcode']);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, 'Content-Type' . ':' . 'application/json; charset=UTF-8');

        //如果没有configure字段，config设为空
        $config = [
            'side' => 'face',
        ];

        $img_data = $this->img_base64($img_path);
        $request  = [
            'image' => "$img_data",
        ];
        if (count($config) > 0) {
            $request['configure'] = json_encode($config);
        }
        $body = json_encode($request);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::$config ['method']);
        curl_setopt($curl, CURLOPT_URL, self::$config ['url']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos('$' . self::$config ['url'], 'https://')) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result      = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rheader     = substr($result, 0, $header_size);
        $rbody       = substr($result, $header_size);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            $result_str = $rbody;
            $return     = json_decode($result_str, true);
            $data       = [
                'name'        => $return['name'],
                'nationality' => $return['nationality'],
                'idcard'      => $return['num'],
                'sex'         => $return['sex'],
                'address'     => $return['address'],
                'image'       => $img_path,

            ];
            $res = [
                'code'    => $httpCode,
                'header'  => '',
                'message' => '',
                'data'    => $data,
            ];
        } else {
            $res = [
                'code'    => $httpCode,
                'message' => $rbody,
                'header'  => $rheader,
                'data'    => '',
            ];
        }

        return $res;
    }

    /**
     * 图片 base64 加密.
     *
     * @param $path
     * @return array|string
     */
    public function img_base64($path)
    {
        //对path进行判断，如果是本地文件就二进制读取并base64编码，如果是url,则返回
        $img_data = '';
        if (substr($path, 0, strlen('http')) === 'http') {
            $img_data = $path;
        } else {
            if ($fp = fopen($path, 'rb', 0)) {
                $binary = fread($fp, filesize($path)); // 文件读取
                fclose($fp);
                $img_data = base64_encode($binary); // 转码
            } else {
                return ['code' => 9999, 'message' => '图片不存在'];
            }
        }

        return $img_data;
    }
}
