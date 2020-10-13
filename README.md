# idcard

## desc

身份证OCR识别、印刷文字识别-身份证识别 ，PHP版本的SDK

## 安装

```
composer require xinmoumomo/idcards
```

#### 发布配置

如果您希望覆盖存储库和条件所在的路径,请发布配置文件:

```shell script
php artisan vendor:publish
```

然后只需打开 `config/idcard.php` 并编辑即可！

#### 配置文件

```
return [
    // 印刷文字识别-身份证识别 调用地址
    "url" => "",
    // 身份证OCR识别接口 调用地址
    "host" => "",
    // Path 格式
    "path" => "",
    // 请求方式
    "method" => "POST",
    //AppCode
    "appcode" => "appcode",
];
```
