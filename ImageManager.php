<?php
/**
 * Created by Ostashev Dmitriy <ostashevdv@gmail.com>
 * Date: 17.10.14 Time: 11:45
 * -------------------------------------------------------------
 */

namespace ostashevdv\image;


use yii\base\Component;

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

    public function thumb($data, $width, $height, $cachePath=null)
    {
        if ($cachePath===null) {
            $cachePath = $this->cachePath;
        }
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