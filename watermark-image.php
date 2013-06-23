<?php
/*
 * PHP function to image-watermark an image
 * http://salman-w.blogspot.com/2008/11/watermark-your-images-with-another.html
 *
 * Writes the given watermark image on the specified image
 * and saves the result as another image
 */

define('WATERMARK_OVERLAY_IMAGE', 'brands/expedia.png');
define('WATERMARK_OVERLAY_OPACITY', 50);
define('WATERMARK_OUTPUT_QUALITY', 90);

function create_watermark($source_file_path, $output_file_path)
{
    list($source_width, $source_height, $source_type) = getimagesize($source_file_path);
    if ($source_type === NULL) {
        return false;
    }
    switch ($source_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_file_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_file_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_file_path);
            break;
        default:
            return false;
    }
    $overlay_gd_image = imagecreatefrompng(WATERMARK_OVERLAY_IMAGE);
    $overlay_width = imagesx($overlay_gd_image);
    $overlay_height = imagesy($overlay_gd_image);
    imagecopymerge(
        $source_gd_image,
        $overlay_gd_image,
        $source_width - $overlay_width - 105,
        $source_height - $overlay_height - 85,
        0,
        0,
        $overlay_width,
        $overlay_height,
        WATERMARK_OVERLAY_OPACITY
    );
    imagejpeg($source_gd_image, $output_file_path, WATERMARK_OUTPUT_QUALITY);
    imagedestroy($source_gd_image);
    imagedestroy($overlay_gd_image);
}

/*
 * Uploaded file processing function
 */

define('UPLOADED_IMAGE_DESTINATION', 'originals/');
define('PROCESSED_IMAGE_DESTINATION', 'images/');

function process_image_upload($Field)
{
    $temp_file_path = $_FILES[$Field]['tmp_name'];
    $temp_file_name = $_FILES[$Field]['name'];
    list(, , $temp_type) = getimagesize($temp_file_path);
    if ($temp_type === NULL) {
        return false;
    }
    switch ($temp_type) {
        case IMAGETYPE_GIF:
            break;
        case IMAGETYPE_JPEG:
            break;
        case IMAGETYPE_PNG:
            break;
        default:
            return false;
    }
    $uploaded_file_path = UPLOADED_IMAGE_DESTINATION . $temp_file_name;
    $processed_file_path = PROCESSED_IMAGE_DESTINATION . preg_replace('/\\.[^\\.]+$/', '.jpg', $temp_file_name);
    move_uploaded_file($temp_file_path, $uploaded_file_path);
    $result = create_watermark($uploaded_file_path, $processed_file_path);
    if ($result === false) {
        return false;
    } else {
        return array($uploaded_file_path, $processed_file_path);
    }
}

/*
 * Here is how to call the function(s)
 */

$result = process_image_upload('File1');
if ($result === false) {
    echo '<br>An error occurred during file processing.';
} else {
    echo '<br>Original image saved as <a href="' . $result[0] . '" target="_blank">' . $result[0] . '</a>';
    echo '<br>Watermarked image saved as <a href="' . $result[1] . '" target="_blank">' . $result[1] . '</a>';
}
?>