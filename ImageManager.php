<?php
/**
 * Created by Ostashev Dmitriy <ostashevdv@gmail.com>
 * Date: 17.10.14 Time: 11:45
 * -------------------------------------------------------------
 */

namespace ostashevdv\image;


use yii\base\Component;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

class ImageManager extends Component
{
    public $driver = 'gd';

    public $cachePath;

    /**
     * Initiates an Image instance from different input types
     *
     * @param  mixed $data
     *
     * @return \Intervention\Image\Image
     */
    public function make($data)
    {
        return $this->createDriver()->init($data);
    }

    /**
     * Creates an empty image canvas
     *
     * @param  integer $width
     * @param  integer $height
     * @param  mixed $background
     *
     * @return \Intervention\Image\Image
     */
    public function canvas($width, $height, $background = null)
    {
        return $this->createDriver()->newImage($width, $height, $background);
    }

    /**
     * Create thumbnail or return src if thumb already created
     *
     * @param mixed $data
     * @param integer $width
     * @param integer $height
     * @param null|string $cacheDir
     * @return null|string
     */
    public function thumb($data, $width, $height, $cacheDir=null)
    {
        if ($cacheDir===null) {
            $cacheDir = $this->cachePath;
        }

        $src['md5'] = md5($data);
        $src['name'] = StringHelper::basename($data);
        $src['ext'] = pathinfo($src['name'], PATHINFO_EXTENSION);

        $dest['name'] = $src['md5']."[{$width}x{$height}].".$src['ext'];
        $dest['dir'] = \Yii::getAlias($cacheDir).DIRECTORY_SEPARATOR.
            substr($src['md5'], 0, 3).DIRECTORY_SEPARATOR.
            substr($src['md5'], 2, 3).DIRECTORY_SEPARATOR.
            substr($src['md5'], 5, 3).DIRECTORY_SEPARATOR;
        $dest['dir'] = FileHelper::normalizePath($dest['dir']);
        $dest['path'] = $dest['dir'].DIRECTORY_SEPARATOR.$dest['name'];
        $dest['url'] = FileHelper::normalizePath($dest['path'], '/');

        if (!file_exists($dest['path'])) {
            try{
                FileHelper::createDirectory($dest['dir']);
                $this->make($data)->fit($width, $height)->save($dest['path']);
            } catch(\Exception $e) {
                \Yii::getLogger()->log('THUMB: '.$e->getMessage(), 0);
                return null;
            }
        }
        return $dest['url'];
    }

    /**
     * Creates a driver instance according to config settings
     *
     * @return \Intervention\Image\AbstractDriver
     */
    private function createDriver()
    {
        $drivername = ucfirst($this->driver);
        $driverclass = sprintf('Intervention\\Image\\%s\\Driver', $drivername);

        if (class_exists($driverclass)) {
            return new $driverclass;
        }

        throw new \Intervention\Image\Exception\NotSupportedException(
            "Driver ({$drivername}) could not be instantiated."
        );
    }
} 