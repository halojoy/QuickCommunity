<?php

class UploadFile
{
    public  $file;
    public  $name;
    public  $source;
    public  $destin;
    public  $srcwidth;
    public  $srcheight;
    public  $maxwidth = 200;
    public  $maxheight = 200;
    private $directory = 'upload';
    private $allowedExtentions = ['image/jpeg','image/png','image/gif'];
    //$this->allowed ['image/jpeg','image/png','image/gif','image/bmp'];
    public  $errors;

    public function __construct($file)
    {
        $this->file = $file;
        $this->name = $file['name'];
        $this->source = $file['tmp_name'];
        $this->destin = $this->directory.'/'.$this->name;
        list($this->srcwidth, $this->srcheight) = getimagesize($this->source);
        $this->errors = [];
        $this->validateFile();
    }

    public function upload()
    {
        if (!move_uploaded_file($this->source, $this->destin)){
            echo "Move uploaded error";
            exit();
        }
        $this->setSource($this->destin);
    }

    public function validateFile()
    {
        if (empty($this->file) || !file_exists($this->source)){
            array_push($this->errors,"Image upload error");
        }
        if (!in_array($this->file['type'], $this->allowedExtentions)){
            array_push($this->errors,"Image type not supported");
        }
        if (!empty($this->errors)){
            echo var_dump($this->errors);
            exit();
        }
    }

    public function setDirectory($dir)
    {
        $this->directory = $dir;
        $this->destin = $this->directory.'/'.$this->name;
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
        $this->source = $file;
    }

    public function setMaxSize($width, $height)
    {
        $this->maxwidth = $width;
        $this->maxheight = $height;
    }

    public function resize()
    {
        if ($this->srcwidth <= $this->maxwidth && $this->srcheight <= $this->maxheight) {
            return;
        }

        $imgsource = $this->source;
        $imgresize = $this->destin;

        $k = min($this->maxwidth/$this->srcwidth, $this->maxheight/$this->srcheight);               
        $new_width  = round($k * $this->srcwidth);
        $new_height = round($k * $this->srcheight);

        $thumb_image = imagecreatetruecolor( $new_width, $new_height );

        if ($this->file['type']=='image/jpeg') {
            $src_image = imagecreatefromjpeg($imgsource);
        } elseif ($this->file['type']=='image/png') {
            $src_image = imagecreatefrompng($imgsource);
        } elseif ($this->file['type']=='image/gif') {
            $src_image = imagecreatefromgif($imgsource);
        } elseif ($this->file['type']=='image/bmp') {
            $src_image = imagecreatefrombmp($imgsource);
        }        

        imagecopyresized($thumb_image, $src_image, 0, 0, 0, 0, 
            $new_width, $new_height, $this->srcwidth, $this->srcheight);

        if ($this->file['type']=='image/jpeg') {
            imagejpeg( $thumb_image, $imgresize, 95 );
        } elseif ($this->file['type']=='image/png') {
            imagepng($thumb_image, $imgresize, 9);
        } elseif ($this->file['type']=='image/gif') {
            imagegif($thumb_image, $imgresize);
        } elseif ($this->file['type']=='image/bmp') {
            imagebmp($thumb_image, $imgresize);
        }

        imagedestroy($src_image);
        imagedestroy($thumb_image);
        
        return $this->destin;
    }

}
