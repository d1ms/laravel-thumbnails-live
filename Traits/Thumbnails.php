<?php
// d1ms 2024 - Thumbnails live generation without php output file, only pure web server output
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

trait Thumbnails {
	
	private $allowMethods = ['fit' , 'resize' , 'crop'];
	private $folder = 'thumbnails';
	private $storage = null;
    /**
     * @param string $field
     * @param string $size
     * @param fit/crop/resize string $method
     * @return string
     */
  public function urlThumbnails($field , $size , $method = 'fit') {
		
		$this->storage = Storage::disk('public');
		$file = is_array($this->{$field}) ? collect($this->{$field})->first() : $this->{$field};
		
		if( $file && in_array($method , $this->allowMethods ) ){
			$sourcePath = $this->storage->path($file);
			$thumbnail = $this->generateThumbnailName($sourcePath , $size , $method);
			
			if( $this->storage->exists($thumbnail) )
				return $this->generateUrl($thumbnail);
			
			if( $newThumbnail = $this->makeThumbnail($sourcePath , $size , $method) && $newThumbnail )
				return $this->generateUrl($newThumbnail);
		}
		
		unset($this->storage);
        return "https://via.placeholder.com/$size";
		
  }
	/**
     * @param string $sourcePath - relative path of source img
     * @param string $size
     * @param fit/crop/resize string $method
     * @return string - relative path of generated thumbnail
     */
	private function makeThumbnail($sourcePath, $size, $method){
		
		$image = Image::make( $this->storage->path($sourcePath) );
		$size = Str::of($size);
		if ($size->contains('x')) {
			$image->{$method}(
				$size->before('x')->toString(),
				$size->after('x')->toString()
			);
		} else {
			$image->{$method}($size->toString());
		}
		$fullPath = $this->generateThumbnailName($sourcePath , $size , $method);
		
		$saving = $image->save( $this->storage->path($fullPath) );
		unset($image);
		
		return $saving ? $fullPath : '';
		
	}
	/**
     * @param string $sourcePath - relative path of source img
     * @param string $size
     * @param fit/crop/resize string $method
     * @return string - relative path of generated thumbnail
    */
	private function generateThumbnailName($sourcePath , $size , $method){
		
		return $this->folder . '/' . $size . '-' . $method . '-' . basename($sourcePath);
		
	}
	/**
     * @param string $path - file path
     * @return string - thumbnail url with web allows
     */
	private function generateUrl($path){
		
		$relativePath = str_replace( $this->storage->path('/') , '/' , $path );
		return $this->storage->url($relativePath);
		
	}
}
