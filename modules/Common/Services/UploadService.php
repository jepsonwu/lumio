<?php

namespace Modules\Common\Services;

use Qiniu\Auth;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Exception;
use Qiniu\Storage\UploadManager;

class UploadService extends BaseService
{
    public function getUploadInfo($encode, $extension)
    {

        try {
            $config = config("common.qiniu");
            $key = $filename = $this->getFileName($extension);
            $encode && $key = \Qiniu\base64_urlSafeEncode($key);

            $result = [
                'token' => $this->getToken(),
                'filename' => "{$filename}",
                'key' => $key,
                'expires' => time() + (int)$config['expires'],
                'host' => $this->getUploadDomain()
            ];
        } catch (Exception $e) {
            $result = [];
        }

        return $result;
    }

    public function uploadFile($fileName, $isBinary = false, $extension = 'jpg')
    {
        $qiniuFileName = "";

        try {
            if ($isBinary) {
                $tmpFileName = tempnam(sys_get_temp_dir(), microtime(true));
                file_put_contents($tmpFileName, $fileName);
                $fileName = $tmpFileName;
            } else {
                $extension = substr($fileName, strrpos($fileName, ".") + 1);
            }

            $upToken = $this->getToken();
            $uploadMgr = new UploadManager();
            $uploadKey = $this->getFileName($extension);

            list($ret, $err) = $uploadMgr->putFile($upToken, $uploadKey, $fileName);
            $err === null && $qiniuFileName = $ret["key"];
            $isBinary && unlink($fileName);
        } catch (Exception $e) {
            $qiniuFileName = "";
        }

        return $qiniuFileName;
    }

    public function getUploadDomain()
    {
        return config("common.upload_domain");
    }

    protected function getToken()
    {
        $config = config("common.qiniu");
        $auth = new Auth($config["access_key"], $config["secret_key"]);
        return $auth->uploadToken($config["bucket"]);
    }

    protected function getFileName($extension)
    {
        return 'upload/' . date("Y") . '/' . date("m") . '/' . date("d")
            . '/' . md5(microtime(true)) . '.' . $extension;
    }
}