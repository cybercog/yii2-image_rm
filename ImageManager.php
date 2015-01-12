<?php
/**
 * Created by Ostashev Dmitriy <ostashevdv@gmail.com>
 * Date: 17.10.14 Time: 11:45
 * -------------------------------------------------------------
 */

namespace ostashevdv\image;

use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;

class ImageManager extends Component
{
    public $driver = 'imagick';

    public $cachePath = '@web/assets/thumbs/';


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
    public function thumb($url, $width, $height, $cacheDir=null)
    {
        if ($cacheDir===null) {
            $cacheDir = $this->cachePath;
        }

        if (parse_url($url, PHP_URL_HOST) == null) {
            $url = Yii::$app->homeUrl.'/'.ltrim($url,'/');
        }

        $urlNorm = new UrlNormalizer($url);
        $url = $urlNorm->normalize();

        $dest['name'] = md5($url)."[{$width}x{$height}].".pathinfo($url, PATHINFO_EXTENSION);
        $dest['dir'] = Yii::getAlias($cacheDir).
            substr(md5($url), 0, 3).'/'.
            substr(md5($url), 2, 3).'/'.
            substr(md5($url), 5, 3).'/';

        $dest['path'] = FileHelper::normalizePath(Yii::getAlias('@webroot').DIRECTORY_SEPARATOR.$dest['dir']);

        if (!file_exists($dest['path'].$dest['name'])) {
            try {
                FileHelper::createDirectory($dest['path']);
                $this->make($url)->fit($width, $height)->save($dest['path'].DIRECTORY_SEPARATOR.$dest['name']);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $dest['dir'].$dest['name'];

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