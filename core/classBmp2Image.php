<?php

class Bmp2Image
{
    /**
     * Original copyright: itgalaxy inc.
     * https://github.com/itgalaxy/bmp2image
    
     * Creates an jpg imageressource from a Bitmap
     *
     * @param string $filename Path to the Bitmap
     *
     * @throws \Exception
     *
     * @return mixed false or a imageressource
     */
    public static function make($filename, $maxWidth = 65535, $maxHeight = 65535)
    {
        if (!($fh = @fopen($filename, 'rb'))) {
            throw new \Exception('Can not open ' . $filename);
        }

        // read file header
        $meta = unpack('vtype/Vfilesize/Vreserved/Voffset', fread($fh, 14));

        // check for bitmap
        if ($meta['type'] != 19778) {
            throw new \Exception($filename . ' is not a valid bitmap!');
        }

        // read image header
        $meta += unpack(
            'Vheadersize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vcolors/Vimportant',
            fread($fh, 40)
        );

        if ($meta['width'] > $maxWidth) {
            $meta['width'] = 65535;
        }

        if ($meta['height'] > $maxHeight) {
            $meta['height'] = 65535;
        }

        // read additional 16bit header
        if ($meta['bits'] == 16) {
            $meta += unpack('VrMask/VgMask/VbMask', fread($fh, 12));
        }

        // set bytes and padding
        $meta['bytes'] = $meta['bits'] / 8;
        $meta['decal'] = 4 - (4 * (($meta['width'] * $meta['bytes'] / 4) - floor($meta['width'] * $meta['bytes'] / 4)));

        if ($meta['decal'] == 4) {
            $meta['decal'] = 0;
        }

        // obtain imagesize
        if ($meta['imagesize'] < 1) {
            $meta['imagesize'] = $meta['filesize'] - $meta['offset'];

            // in rare cases filesize is equal to offset so we need to read physical size
            if ($meta['imagesize'] < 1) {
                $meta['imagesize'] = filesize($filename) - $meta['offset'];

                if ($meta['imagesize'] < 1) {
                    throw new \Exception('Can not obtain filesize of ' . $filename . '!');
                }
            }
        }

        // calculate colors
        $meta['colors'] = !$meta['colors'] ? pow(2, $meta['bits']) : $meta['colors'];
        // read color palette
        $palette = [];

        if ($meta['bits'] < 16) {
            switch ($meta['headersize']) {
                // BITMAPCOREHEADER
                // OS21XBITMAPHEADER
                case 12:
                    fseek($fh, 0x1a);
                    break;
                // BITMAPINFOHEADER
                case 40:
                    fseek($fh, 0x36);
                    break;
                // OS22XBITMAPHEADER
                case 64:
                    fseek($fh, 0x4e);
                    break;
                // BITMAPV4HEADER
                case 108:
                    fseek($fh, 0x7a);
                    break;
                // BITMAPV5HEADER
                case 124:
                    fseek($fh, 0x8a);
                    break;
                default:
                    // No default
                    break;
            }

            $palette = unpack('l' . $meta['colors'], fread($fh, $meta['colors'] * 4));

            // in rare cases the color value is signed
            if ($palette[1] < 0) {
                foreach ($palette as $i => $color) {
                    $palette[$i] = $color + 16777216;
                }
            }
        }

        // create gd image
        $im = imagecreatetruecolor($meta['width'], $meta['height']);

        fseek($fh, $meta['offset']);

        $data = fread($fh, $meta['imagesize']);
        $p = 0;
        $vide = chr(0);
        $y = $meta['height'] - 1;
        $error = $filename . ' has not enough data!';

        // loop through the image data beginning with the lower left corner
        while ($y >= 0) {
            $x = 0;

            while ($x < $meta['width']) {
                switch ($meta['bits']) {
                    case 32:
                        if (!($part = substr($data, $p, 4))) {
                            throw new \Exception($error);
                        }

                        $color = unpack('C4', $part . $vide);
                        $color[1] = ($color[4] << 16) | ($color[3] << 8) | $color[2];
                        break;
                    case 24:
                        if (!($part = substr($data, $p, 3))) {
                            throw new \Exception($error);
                        }

                        $color = unpack('V', $part . $vide);
                        break;
                    case 16:
                        if (!($part = substr($data, $p, 2))) {
                            throw new \Exception($error);
                        }

                        $color = unpack('v', $part);
                        $color[1] = (($color[1] & 0xf800) >> 8)
                            * 65536 + (($color[1] & 0x07e0) >> 3)
                            * 256 + (($color[1] & 0x001f) << 3);
                        break;
                    case 8:
                        $color = unpack('n', $vide . substr($data, $p, 1));
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    case 4:
                        $color = unpack('n', $vide . substr($data, floor($p), 1));
                        $color[1] = ($p * 2) % 2 == 0 ? $color[1] >> 4 : $color[1] & 0x0F;
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    case 1:
                        $color = unpack('n', $vide . substr($data, floor($p), 1));

                        switch (($p * 8) % 8) {
                            case 0:
                                $color[1] = $color[1] >> 7;
                                break;
                            case 1:
                                $color[1] = ($color[1] & 0x40) >> 6;
                                break;
                            case 2:
                                $color[1] = ($color[1] & 0x20) >> 5;
                                break;
                            case 3:
                                $color[1] = ($color[1] & 0x10) >> 4;
                                break;
                            case 4:
                                $color[1] = ($color[1] & 0x8) >> 3;
                                break;
                            case 5:
                                $color[1] = ($color[1] & 0x4) >> 2;
                                break;
                            case 6:
                                $color[1] = ($color[1] & 0x2) >> 1;
                                break;
                            case 7:
                                $color[1] = ($color[1] & 0x1);
                                break;
                            default:
                                // No default
                                break;
                        }

                        $color[1] = $palette[$color[1] + 1];
                        break;
                    default:
                        throw new \Exception($filename . ' has ' . $meta['bits'] . ' bits and this is not supported!');
                }

                imagesetpixel($im, $x, $y, $color[1]);
                $x++;
                $p += $meta['bytes'];
            }

            $y--;
            $p += $meta['decal'];
        }

        fclose($fh);

        return $im;
    }
}
