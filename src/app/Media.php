<?php

namespace Kaleidoscope\Factotum;

use Illuminate\Database\Eloquent\Model;
use Image;

use Kaleidoscope\Factotum\Library\Utility;

class Media extends Model
{
	protected $fillable = [
		'caption', 'alt_text', 'description'
	];

	public static function filenameAvailable($filename, $origFilename, $counter = '')
	{
		$filename = str_replace('jpeg', 'jpg', $filename);
		$mediaExist = Media::where('filename', $filename)->get();
		if ( $mediaExist->count() > 0 ) {
			if (!$counter) {
				$counter = 1;
			} else {
				$counter++;
			}
			$ext      = substr( $origFilename, strlen($origFilename) -3 , 3);
			$filename = substr( $origFilename, 0, -4 ) . '-' . $counter . '.' . $ext;
			return self::filenameAvailable($filename, $origFilename, $counter);
		}
		return $filename;
	}

	public static function saveImage( $field, $filename )
	{
		if ( $field->image_bw ) {
			$origImage = Image::make( $filename )->greyscale();
		} else {
			$origImage = Image::make( $filename );
		}

		$filename = str_replace( 'jpeg', 'jpg', $filename );

		$ext          = substr( $filename, strlen($filename) - 3, 3 );
		$origFilename = substr( $filename, 0, -4 );

		$operation = $field->image_operation;

		if ( $field->resizes ) {
			$resizes = Utility::convertOptionsTextToAssocArray( $field->resizes );
			foreach ($resizes as $width => $height) {
				$newFilename = $origFilename . '-' . $width .'x' . $height . '.' . $ext;
				$image = Image::make( $filename );

				if ($operation == 'resize') {
					$image->resize( $width, null, function ($constraint) {
						$constraint->aspectRatio();
					});
				} else if ($operation == 'crop') {
					$image->crop( $width, $height );
				} else if ($operation == 'fit') {
					$image->fit( $width, $height, function ($constraint) {
						$constraint->upsize();
					});
				}
				$image->save( $newFilename, 90 );
			}
		}

		$thumbSize = config('factotum.factotum.thumb_size');
		$thumbFilename = $origFilename . '-thumb.' . $ext;
		$origImage->fit( $thumbSize['width'], $thumbSize['height'], function ($constraint) {
			$constraint->upsize();
		});

		$origImage->save( $thumbFilename );

		return $origImage;
	}

	public static function retrieve( $mediaId, $fieldModel )
	{
		if ( !is_array($mediaId) ) {
			$media = self::find($mediaId)->toArray();

			if ( isset($fieldModel->sizes) && $fieldModel->sizes != '' ) {
				$mediaUrl = substr( $media['url'], 0, -4);
				$ext = substr( $media['filename'], strlen($media['filename'])-3, 3 );

				if ( count($fieldModel->sizes) > 0 ) {
					$tmp = array();
					foreach ( $fieldModel->sizes as $size ) {
						$tmp[] = $mediaUrl . $size . '.' . $ext ;
					}
					$media['sizes'] = $tmp;
				}
			}
			return $media;
		}
		return $mediaId;
	}
}
