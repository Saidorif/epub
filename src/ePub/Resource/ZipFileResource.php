<?php

/*
 * This file is part of the ePub Reader package
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ePub\Resource;

use ZipArchive;

class ZipFileResource
{
    private $zipFile;

    private $cwd;

    public function __construct($file)
    {
        $this->zipFile = new \ZipArchive();

        $this->zipFile->open($file);
    }

    public function setDirectory($dir)
    {
        $this->cwd = $dir;
    }

    public function get($name)
    {
        if (null !== $this->cwd) {
            $name = $this->cwd . '/' . $name;
        }

        return $this->zipFile->getFromName($name);
    }

    public function getXML($name)
    {
        return simplexml_load_string($this->get($name));
    }

    public function all()
    {
        $result = array();
        $cover_image = '';


        for ($i = 0; $i < $this->zipFile->numFiles; $i++){
            $item = $this->zipFile->statIndex($i);
            $test_item = $item;
            $extension = substr($test_item['name'], -4);
            
            if($extension == '.jpg' || $extension == '.png' || $extension == '.gif' || $extension == '.svg'){
                $this->zipFile->extractTo(public_path().'/covers/', array($this->zipFile->getNameIndex($i)));
                $cover_image = 'covers/'.$test_item['name'];
            }
            if($extension == 'html'){
                $this->zipFile->deleteIndex($i);
            }
            $result[] = $item['name'];
        }
        $result['cover'] = $cover_image;

        return $result;
    }
}