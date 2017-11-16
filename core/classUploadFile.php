<?php

class UploadFile
{
    public  $file;
    public  $fileSize;
    public  $name;
    public  $source;
    public  $destin;
    public  $filetype;
    public  $extension;
    public  $fileCategory;
    public  $srcWidth;
    public  $srcHeight;
    // Settings
    public  $directory = 'upload'; // upload directory
    public  $maxFileSize = 120; // MB, max size 120MB
    public  $maxWidth  = 840;   // max image width
    public  $maxHeight = 680;   // max image height
    public  $imageAllow = ['jpg','png','gif','bmp']; // possible to resize images
    public  $fileAllow = ['pdf','txt','zip','7z','tar.gz','tgz','mp4','swf',
                          'avi','mov','wmv','flv','mpg','mp3','flac','wav'];
    // Settings end
    public  $imageMime = array(
            'jpg' => ['image/jpeg','image/jpg'],
            'png' => ['image/png','application/png','application/x-png'],
            'gif' => ['image/gif','image/x-xbitmap'],
            'bmp' => ['image/bmp','image/x-bmp'] );
    public  $fileMime = array(
            'pdf' => ['application/pdf','application/x-pdf'],
            'txt' => ['text/plain','application/txt'],
            'zip' => ['application/zip','application/x-zip','application/x-zip-compressed','application/x-compressed'],
            '7z'  => ['application/x-7z-compressed'],
            'tar.gz' => ['application/gzip','application/x-gzip','application/x-tar'],
            'tgz' =>    ['application/gzip','application/x-gzip','application/x-tar'],
            'mp4' => ['video/mp4','video/mp4v-es'],
            'swf' => ['application/x-shockwave-flash'],
            'avi' => ['video/avi','video/msvideo','video/x-msvideo'],
            'mov' => ['video/quicktime','video/x-quicktime'],
            'wmv' => ['video/x-ms-wmv'],
            'flv' => ['video/x-flv'],
            'mpg' => ['video/mpeg','video/mpg','video/x-mpg'],
            'mp3' => ['audio/mp3','audio/x-mp3'],
            'flac' => ['audio/flac'],
            'wav' => ['audio/wav','audio/x-wav','audio/wave'] );

    public function __construct()
    {
    }

    public function registerFile($file)
    {
        if ($file['error']) {
            exit('File upload error');
        }
        $this->file = $file;
        $this->fileSize = $file['size'];
        $this->checkSize();
        $this->name = $file['name'];
        $this->source = $file['tmp_name'];
        $this->destin = $this->directory.'/'.$this->name;
        $this->filetype = $file['type'];
        $this->extension = $this->getExtension();
        $this->validateFile();
        $this->checkDir();
    }

    public function upload()
    {
        if (!move_uploaded_file($this->source, $this->destin)){
            echo "Move uploaded file error";
            exit();
        }
        $this->setSource($this->destin);
    }

    public function validateFile()
    {
        $allImageMimes = array();
        foreach($this->imageAllow as $img) {
            $allImageMimes = array_merge($allImageMimes, $this->imageMime[$img]);
        }
        $allFileMimes = array();
        foreach($this->fileAllow as $file) {
            $allFileMimes = array_merge($allFileMimes, $this->fileMime[$file]);
        }
        if (in_array($this->filetype, $allImageMimes)) {
            $this->fileCategory = 'image';
        } elseif (in_array($this->filetype, $allFileMimes)) {
            $this->fileCategory = 'file';
        } else {
            $this->fileCategory = 'nosupport';
            exit('<b>'.$this->name.'</b><br>
                File with extension <b>.'.$this->extension.'</b> not supported'.'<br>
                Mime filetype: '.$this->filetype.'<br>
                File category: '.$this->fileCategory );
        }
        if ($this->filetype == 'image/bmp' || $this->filetype == 'image/x-bmp') {
            $this->bmp2jpg();
        }      
    }

    public function checkSize()
    {
        if ($this->fileSize > $this->maxFileSize * 1048576) {
            exit('File size too big!<br>
            Max size is: <b>'.$this->maxFileSize.' MB</b>');
        }
    }
    
    public function getExtension()
    {
        if (preg_match("@\.tar.gz$@i", $this->name))
            return 'tar.gz';
        else
            return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }

    public function setDirectory($dir)
    {
        $this->directory = $dir;
        $this->checkDir();
        $this->destin = $this->directory.'/'.$this->name;
    }

    public function checkDir()
    {
        if (!file_exists($this->directory))
            if (!mkdir($this->directory))
                exit('Upload Directory does not exist and could not be created');
    }
    
    public function getSupported()
    {
        $all = array_merge($this->img1Allow, $this->img2Allow, $this->fileAllow);
        $supported = '';
        foreach($all as $ext)
            $supported .= $ext.', ';
        $supported = rtrim($supported, ', ');
        return $supported;
    }

    public function setFileSize($size) //MB, megabytes
    {
        $this->maxFileSize = $size;
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->destin = $this->directory.'/'.$this->name;
    }

    public function setSource($file)
    {
        $this->source = $file;
    }

    public function setDestin($file)
    {
        $this->destin = $file;
    }

    public function setMaxSize($width, $height)
    {
        $this->maxWidth  = $width;
        $this->maxHeight = $height;
    }

    public function getDisplayWidth($image)
    {
        list($width, $height) = getimagesize($image);
        $k = min($this->maxWidth/$width, $this->maxHeight/$height);
        if ($k >= 1)
            return $width;
        else
            return round($k * $width);
    }

    public function bmp2jpg()
    {
        $this->filetype = 'image/jpeg';
        $this->extension = 'jpg';
        $this->destin = str_replace('bmp', 'jpg', $this->source);
        $this->name = str_replace('bmp', 'jpg', $this->name);
        require 'core/classBmp2Image.php';
        $jpg = Bmp2Image::make($this->source);
        imagejpeg($jpg, $this->destin);
        imagedestroy($jpg);
        $this->source = $this->destin;
        $this->destin = $this->directory.'/'.$this->name;
    }

    public function resize()
    {
        list($this->srcWidth, $this->srcHeight) = getimagesize($this->source);
        if ($this->srcWidth <= $this->maxWidth && $this->srcHeight <= $this->maxHeight) {
            $this->upload();
            return;
        }
        $imgSource = $this->source;
        $imgResize = $this->destin;

        $k = min($this->maxWidth/$this->srcWidth, $this->maxHeight/$this->srcHeight);               
        $newWidth  = round($k * $this->srcWidth);
        $newHeight = round($k * $this->srcHeight);

        $thumbImage = imagecreatetruecolor( $newWidth, $newHeight );

        if ($this->filetype=='image/jpeg') {
            $srcImage = imagecreatefromjpeg($imgSource);
        } elseif ($this->filetype=='image/png') {
            $srcImage = imagecreatefrompng($imgSource);
        } elseif ($this->filetype=='image/gif') {
            $srcImage = imagecreatefromgif($imgSource);
        } elseif ($this->filetype=='image/bmp') {
            $srcImage = imagecreatefrombmp($imgSource);
        }        

        imagecopyresized($thumbImage, $srcImage, 0, 0, 0, 0, 
            $newWidth, $newHeight, $this->srcWidth, $this->srcHeight);

        if ($this->filetype=='image/jpeg') {
            imagejpeg($thumbImage, $imgResize, 95);
        } elseif ($this->filetype=='image/png') {
            imagepng($thumbImage, $imgResize, 9);
        } elseif ($this->filetype=='image/gif') {
            imagegif($thumbImage, $imgResize);
        } elseif ($this->filetype=='image/bmp') {
            imagebmp($thumbImage, $imgResize);
        }

        imagedestroy($srcImage);
        imagedestroy($thumbImage);
        
        return $this->destin;
    }

}