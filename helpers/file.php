<?php
defined('C5_EXECUTE') or die("Access Denied.");

class FileHelper extends Concrete5_Helper_File
{

    function checkOrCreateDirPath($uploadDirPath)
    {
        if (!is_dir($uploadDirPath)) {
            @mkdir($uploadDirPath, DIRECTORY_PERMISSIONS_MODE, true);
        }
    }

    public function uploadFile($inputName, $filePath, $append = '')
    {
        if (!isset($_FILES[$inputName])) {
            return false;
        }

        $file = $_FILES[$inputName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $extension = '.' . $this->getExtension($file['name']);
        $filePath  = rtrim($filePath, '/') . '/';

        do {
            $destination = $filePath . $append .'_'. time() . $extension;
        } while (file_exists($destination));

        return @move_uploaded_file($file["tmp_name"], $destination) ? $destination : false;
    }

    public function uploadFiles($inputName, $filePath, $append = '')
    {
        if (!isset($_FILES[$inputName])) {
            return [];
        }

        $files = $_FILES[$inputName];

        if (!$files) {
            return [];
        }

        foreach ($files['error'] as $errorCode) {
            if ($errorCode !== UPLOAD_ERR_OK && $errorCode !== UPLOAD_ERR_NO_FILE) {
                return [];
            }
        }
        $uploadedFiles = array();
        foreach ($files['tmp_name'] as $i => $pointer) {
            $extension = '.' . $this->getExtension($files['name'][$i]);
            $filePath  = rtrim($filePath, '/') . '/';

            do {
                $destination = $filePath . $append .'_'. time() . $extension;
            } while (file_exists($destination));

            $uploadedFiles[] = @move_uploaded_file($pointer, $destination) ? $destination : false;
        }
        return array_filter($uploadedFiles);
    }
}
