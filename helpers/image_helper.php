<?php
namespace macsch15\capsule\helpers;

class image_helper
{
    /**
     * @var \phpbb\user
     */
    protected $user;

    const THUMB_WIDTH = 200;
    const THUMB_HEIGHT = 120;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(\phpbb\user $user)
    {
        $this->user = $user;
    }

    /**
     * Get mime type for image
     *
     * @param string $file 
     * @return string
     */
    public function getMime($file)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = @finfo_file($finfo, $file);
        finfo_close($finfo);

        return $mime;
    }

    /**
     * Build thumbnail from image
     *
     * @param string $upload_directory
     * @param string $img
     * @param string $id
     * @throws \Exception
     * @return void
     */
    public function makeThumbnail($upload_directory, $img, $id)
    {
        $image_details = getimagesize($upload_directory . $img);
        $original_width = $image_details[0];
        $original_height = $image_details[1];

        if ($original_width > $original_height) {
            $new_width = self::THUMB_WIDTH;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = self::THUMB_HEIGHT;
            $new_width = intval($original_width * $new_height / $original_height);
        }

        $dest_x = intval((self::THUMB_WIDTH - $new_width) / 2);
        $dest_y = intval((self::THUMB_HEIGHT - $new_height) / 2);

        if ($image_details[2] == 1) {
            $imgt = 'ImageGIF';
            $imgcreatefrom = 'ImageCreateFromGIF';
        }

        if ($image_details[2] == 2) {
            $imgt = 'ImageJPEG';
            $imgcreatefrom = 'ImageCreateFromJPEG';
        }

        if ($image_details[2] == 3) {
            $imgt = 'ImagePNG';
            $imgcreatefrom = 'ImageCreateFromPNG';
        }

        if ($imgt) {
            if (!$old_image = @$imgcreatefrom($upload_directory . $img)) {
                throw new \Exception($this->user->lang('CAPSULE_ERROR_GENERAL'));
            }

            $new_image = imagecreatetruecolor(self::THUMB_WIDTH, self::THUMB_HEIGHT);
            imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            $imgt($new_image, $upload_directory . $id . $img);
        }
    }
}
