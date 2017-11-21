<?php

/*
    Read Windows BMP v.1.1
    Licence: MIT
    Author: Nicholas Marus (nmarus@gmail.com) 

    This will convert windows BMP files to img object to use with the PHP GD 
    library. This works only with Windows BMP files of color depth 4, 8, and 24.
    This is based on spec: http://en.wikipedia.org/wiki/BMP_file_format

    'halojoy' has rewritten Nicholas Marus script to this Class 2017-11-21.
    https://github.com/halojoy

    Original script: https://github.com/nmarus/phpbmpread
*/


Class ConvertBMP
{
    //Original BMP image
    private static $bmp;

    //Data to be processed
    private static $bmpdata;

    //The header data of the BMP
    private static $header;

    //The final image created from BMP
    private static $img;
 
    public static function bmp2jpg($bmp, $save_as) {
        self::$bmp = $bmp;
        self::imagecreatefrombmp();
        imagejpeg(self::$img, $save_as);
        imagedestroy(self::$img);
    }

    public static function bmp2png($bmp, $save_as) {
        self::$bmp = $bmp;
        self::imagecreatefrombmp();
        imagepng(self::$img, $save_as);
        imagedestroy(self::$img);
    }

    public static function bmp2gif($bmp, $save_as) {
        self::$bmp = $bmp;
        self::imagecreatefrombmp();
        imagegif(self::$img, $save_as);
        imagedestroy(self::$img);
    }

    //Get info regarding bmp
    public static function bmpinfo($bmp) {
        self::$bmp = $bmp;
        self::loadbmpdata();
        self::loadbmpheader();

        echo('<table border=1>');
        foreach (self::$header as $key => $value) {
            echo('<tr><td>'.$key.':</td><td>'.$value.'</td></tr>');
        }
        echo('</table>');
        return true;
    }

    //Core function
    private static function imagecreatefrombmp() {
        self::loadbmpdata();
        self::makeimg();
    }

    //Checks and returns bmp data
    private static function loadbmpdata() {
        if(is_string(self::$bmp)) {
            if($f = fopen(self::$bmp, "rb")) {
                self::$bmpdata = fread($f, filesize(self::$bmp));
                fclose($f);
                return;
            } else {
                $error = 'Error opening file '.self::$bmp;
                throw new Exception($error);
                return false;
            }
        } else if(is_object(self::$bmp)) {
            self::$bmpdata = self::$bmp;
            return;
        } else {
            $error = 'Invalid variable passed to function';
            throw new Exception($error);
            return false;
        }
    }

    //Decodes bmpdata and make image handle
    private static function makeimg() {

        self::loadbmpheader();
        //Set info from header about BMP
        $w  = self::$header['width'];     //image width
        $h  = self::$header['height'];    //image height
        $s  = self::$header['bitmap_start'];  //offset where BMP data
        $b  = self::$header['bits_pixel'];    //bits per pixel
        $ds = self::$header['dib_size'];  //size of dib header
        $hs = 14;           //size of BMP file header data
        $ps = pow(2,$b) * 4;        //size of palette
        $bp = array('4','8','24');  //array of supported bp

        //Validate $b matches what this script can read
        if(!in_array($b, $bp)) {
            $error = 'BMP bits per pixel not supported';
            throw new Exception($error);
            return false;
        }

        //Checks for and grab color palette on 4,8 bit images
        if(($b <= 8) && $s >= $hs+$ds+$ps) { 
            $cp = substr(self::$bmpdata, $hs + $ds, $ps); //grab color palette after header
            $cp = bin2hex($cp); //convert to hex
            $cp = str_split($cp, 8); //split into color codes
        }   

        //Create image object
        self::$img = imagecreatetruecolor($w, $h);

        //Trim header from BMP
        self::$bmpdata = substr(self::$bmpdata, $s);

        //Convert to string of HEX
        self::$bmpdata = bin2hex(self::$bmpdata);

        //Get row size with padding (must be multiple of 4 bytes)
        $row_size = ceil(($b * $w / 8) / 4) * 8;

        //Split data to array of rows
        self::$bmpdata=str_split(self::$bmpdata,$row_size);

        //Process data
        for($y=0; $y<$h; $y++) {
            
            //Get 1 row (flip row vertical order)
            $row = self::$bmpdata[abs(($h-1)-$y)];

            //Get row pixel data (remove trailing buffer)
            $row = substr($row, 0, $w * $b / 4);

            //Split row to pixel
            $pixels = str_split($row, $b / 4);

            //Process 24bit bitmap
            if($b == 24) {
                //write pixel data for row to img
                for($x=0; $x<$w; $x++) {
                    imagesetpixel(self::$img, $x, $y, self::loadcolor24($pixels[$x]));
                }
                //Process palette based bitmap
            } else if(in_array($b, $bp)) { 
                //Write pixel data for row to img
                for($x=0; $x<$w; $x++) {
                    imagesetpixel(self::$img, $x, $y, self::loadcolorpalette($pixels[$x],$b,$cp));
                }
            } else {
                return false;
            }
        }
        self::$bmpdata = '';
    }

    //Returns header of BMP raw
    private static function loadbmpheader() {
        //Grab first 54 to determine file info
        if($header = substr(self::$bmpdata, 0, 54)) {
        $header = unpack(   'a2type/'.      //00-2b - header to identify file type
                        'Vfile_size/'.      //02-4b - size of bmp in bytes
                        'vreserved1/'.      //06-2b - reserved
                        'vreserved2/'.      //08-2b - reserved
                        'Vbitmap_start/'.   //10-4b - offset of where bmp pixel array can be found
                        'Vdib_size/' .      //14-4b - size of dib header
                    'Vwidth/'.      //18-4b - width on pixels
                    'Vheight/'.     //22-4b - height in pixels
                    'vcolor_planes/'.   //26-2b - number of color planes
                    'vbits_pixel/'.     //28-2b - bits per pixel
                    'Vcompression/'.    //30-4b - compression method
                    'Vimage_size/'.     //34-4b - image size in bytes
                    'Vh_resolution/'.   //38-4b - horizontal resolution
                    'Vv_resolution/'.   //42-4b - vertical resolution
                    'Vcolor_palette/'.  //46-4b - number of colors in palette
                    'Vimp_colors/'      //50-4b - important colors
                    , $header);
                    self::$header = $header;
                    return;
        } else {
            $error = 'BMP header data not found';
            throw new Exception($error);
            return false;
        }

        //Validate bitmap
        if($header['type'] != 'BM') {
            $error = 'BMP not valid';
            throw new Exception($error);
            return false;
        }
    }

    //24 bit to image color function
    private static function loadcolor24($hc) {
        $hc = str_split($hc,2);
        $r = hexdec($hc[2]);
        $g = hexdec($hc[1]);
        $b = hexdec($hc[0]);
        return ($r * 65536) + ($g * 256) + $b;
    }

    //4,8 bit to image color function
    private static function loadcolorpalette($hc,$b,$cp) {
        $r = 0; //red
        $g = 0; //green
        $b = 0; //blue
        if($cp != 0) { //If defined, set rgb value based on palette
            $r = hexdec(substr($cp[hexdec($hc)],4,2));
            $g = hexdec(substr($cp[hexdec($hc)],2,2));
            $b = hexdec(substr($cp[hexdec($hc)],0,2));
            return ($r * 65536) + ($g * 256) + $b;
        } else if($b == '4') { //Else if no palette and 4 bit, use standard 16 color palette as defined below
            switch ($hc) {
                case '0': //black
                $r = 0; $g = 0; $b = 0;
                break;
                
                case '1': //dark red
                $r = 128; $g = 0; $b = 0;
                break;

                case '2': //red
                $r = 255; $g = 0; $b = 0;
                break;

                case '3': //pink
                $r = 255; $g = 0; $b = 255;
                break;

                case '4': //teal
                $r = 0; $g = 128; $b = 128;
                break;

                case '5': //green
                $r = 0; $g = 128; $b = 0;
                break;

                case '6': //bright green
                $r = 0; $g = 255; $b = 0;
                break;

                case '7': //turquoise
                $r = 0; $g = 255; $b = 255;
                break;

                case '8': //dark blue
                $r = 0; $g = 0; $b = 128;
                break;

                case '9': //violet
                $r = 128; $g = 0; $b = 128;
                break;

                case 'a': //blue
                case 'A':
                $r = 0; $g = 0; $b = 255;
                break;

                case 'b': //gray 25%
                case 'B':
                $r = 192; $g = 192; $b = 192;
                break;

                case 'c': //gray 50%
                case 'C':
                $r = 128; $g = 128; $b = 128;
                break;

                case 'd': //dark yellow
                case 'D':
                $r = 128; $g = 128; $b = 0;
                break;

                case 'e': //yellow
                case 'E':
                $r = 255; $g = 255; $b = 0;
                break;

                case 'f': //white
                case 'F':
                $r = 255; $g = 255; $b = 255;
                break;

                default:
                $r = 0; $g = 0; $b = 0;
                break;
            }

            return ($r * 65536) + ($g * 256) + $b;

        } else {
            $error = 'BMP palette not found and image is not 4 bpp';
            throw new Exception($error);
            return false;
        }
    }

}
