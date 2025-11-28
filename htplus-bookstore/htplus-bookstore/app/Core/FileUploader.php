<?php
declare(strict_types = 1);
namespace App\Core;

class FileUploader { 
    public static function uploadBookImage(array $file): ?string {

        if(empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE){
            return null;
        }

        if($file["error"] !== UPLOAD_ERR_OK){
            throw new \RuntimeException('Upload error code:' . $file["error"]);
        }

        if($file["size"]>2*1024*1024){
            throw new \RuntimeException("File too large (max 2MB)");
        }
        
        //Checking mime type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);
        $allowed = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
        ];
        
        $ext = array_search($mime, $allowed, true);
        if($ext === false) { 
            throw new \RuntimeException("Only allowed jpg/png/gif");
        }

        $baseName = bin2hex(random_bytes(8));
        $fileName = $baseName . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/books';
        if(!is_dir($uploadDir)){
            mkdir($uploadDir,0777,true);
        }
        //return path in database 
        $targetPath = $uploadDir . '/' . $fileName;
               if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Không thể lưu file');
        }

        return '/uploads/books/' . $fileName;
    }
} 
?>