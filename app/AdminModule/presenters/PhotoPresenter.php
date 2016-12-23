<?php

namespace App\AdminModule\Presenters;
use Nette,
	App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use Nette\Utils\Finder;
use Nette\Utils\Image;
use Nette\Utils\Strings;
use Nette\Database\SqlLiteral;

final class PhotoPresenter extends BasePresenter {
    
    /** @var object */
    private $record;

     /** @var object */
    private $model;   	    
    
    private $photos_dir = "./images/photos";
    
    protected function startup()  {
        parent::startup();
		$this->model = $this->photo;
    
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }
    
    public function beforeRender() {
    	parent::beforeRender();
		$this->template->photos_dir = $this->photos_dir;
    }

	function renderDetail($vehicle_id) {
		$vehicle = $this->vehicle
		   		        ->get($vehicle_id);
		
		$this->template->images_dir_count = Finder::findFiles('*.jpg', '*.jpeg', '*.png', '*.gif')->in("./images/photos/{$vehicle->id}_{$vehicle->rz}/photos")->count();
		$this->template->vehicle_photos_folder = $this->photos_dir."/".$vehicle->id."_".$vehicle->rz;
		
		$this->template->photos = $this->photo
									   ->findAll()
									   ->where(["vehicle_id" => $vehicle_id])
									   ->order("position");
								
		$this->template->vehicle = $vehicle;
	}

	function handleUploadFile($vehicle_id) {
		$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions
   		
		$verifyToken = md5('unique_salt' . $_POST['timestamp']);
		
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			
			if (in_array(strtolower($fileParts['extension']), $fileTypes)) {
				$vehicle = $this->vehicle->get($vehicle_id);
				$tempFile   = $_FILES['Filedata']['tmp_name'];
				$uploadDir  = $this->photos_dir."/photos";
				$targetFile = Strings::webalize($fileParts['filename']).".".$fileParts['extension'];
				$vehicle_photos_folder = $this->photos_dir."/".$vehicle->id."_".$vehicle->rz;
				
				$image = Image::fromFile($tempFile);
				$image->resize(NULL, 1200, Image::SHRINK_ONLY);
				$image->save($vehicle_photos_folder."/photos/{$targetFile}");
				chmod($vehicle_photos_folder."/photos/{$targetFile}", 0777);
                $filesize = filesize($vehicle_photos_folder."/photos/{$targetFile}");
                
                $new_width = $image->width;
                $new_height = $image->height;
                
                $image->resize(NULL, 240);
				$image->save($vehicle_photos_folder."/previews/{$targetFile}");
				chmod($vehicle_photos_folder."/previews/{$targetFile}", 0777);

                unset($image);
                								
				$photo = $this->photo->findBy(['file' => $targetFile,
											   'vehicle_id' => $vehicle_id]);
											   
				$max_position = $this->photo->findAll()
											->where(['vehicle_id' => $vehicle_id])
                                            ->max('position');
				
				if($photo->count() == 0) { // fotka s tímto názvem souboru neexistuje, vloží se nakonec				
    				$new_photo = $this->photo->insert(["file" => $targetFile,
    												   'vehicle_id' => $vehicle_id,
    						 						   "width" => $new_width,
    												   "height" => $new_height,
    												   "position" => $max_position + 1,
                                                     ]);

                    Debugger::enable(Debugger::PRODUCTION); // háček kvůli tomu, aby se v ajaxové odpovědi neodesílal debug bar
                    $this->setView("photo-box");
                    $this->template->photo = $this->photo->get($new_photo->id);
                    $this->template->new = true;
                    $this->template->vehicle_photos_folder = $vehicle_photos_folder;
				}
				
				else { // fotka s tímto názvem souboru existuje, aktualizuje se    				
    				$photo->update(['width' => $new_width,
    				                'height' => $new_height,
                                  ]);
    				                                          
                    //$this->handleUpdatePosition($new_photo->id, $max_position);
                    $photo = $photo->fetch();
                    $this->payload->photo = $this->photo->get($photo->id)
                                                        ->toArray();
                    
                    $filesize = \Latte\Runtime\Filters::bytes(filesize($vehicle_photos_folder."/photos/".$targetFile));
                    $this->payload->filesize = $filesize;
                    $this->payload->file_path = $vehicle_photos_folder."/previews/{$targetFile}";
                    
            		$this->payload->images_dir_count = Finder::findFiles('*.jpg', '*.jpeg', '*.gif', '*.png')->in($vehicle_photos_folder."/photos")
            		                                                                                         ->count();
            		$this->payload->images_db_count = $this->photo->findAll()
            													  ->where(['vehicle_id' => $vehicle_id])
            		                                              ->count();
                    $this->sendPayload();
                    
				}
			}
		}
	}

	function handleRemovePhoto($photo_id) {
		$photo = $this->photo->get($photo_id);
		$dir = $this->photos_dir."/".$photo->vehicle->id."_".$photo->vehicle->rz;
		
		@unlink($dir."/photos/".$photo->file);
		@unlink($dir."/previews/".$photo->file);

		$this->photo->findAll()
		            ->where('position > ?', $photo->position)
					->where(['vehicle_id' => $photo->vehicle_id])
                    ->update(["position" => new SqlLiteral("position - 1")]);

		$this->photo->delete($photo_id);
		$this->payload->images_dir_count = Finder::findFiles('*.jpg', '*.jpeg', '*.gif', '*.png')->in($dir."/photos")
		                                                                                         ->count();
		$this->payload->images_db_count = $this->photo->findAll()
													  ->where(['vehicle_id' => $photo->vehicle_id])
		                                              ->count();
		$this->payload->success = true;
		$this->sendPayload();
		$this->terminate();
	}

	function actionGeneratePhotos($vehicle_id) {
		$vehicle = $this->vehicle->get($vehicle_id);
		$photos_dir = $this->photos_dir."/".$vehicle->id."_".$vehicle->rz;
		$vehicle->related("photo", "vehicle_id")->delete();

		foreach (Finder::findFiles('*.jpg', '*.jpeg', '*.png', '*.gif')->in($photos_dir."/previews") as $file_path => $file) {
			unlink($file_path);
		}
		
		foreach (Finder::findFiles('*.jpg', '*.jpeg', '*.png', '*.gif')->in($photos_dir."/photos") as $file_path => $file) {			
			$image = Image::fromFile($file_path);
			
			$pathinfo = pathinfo($file_path);
			
			$image->resize(NULL, 1200);
			unlink($photos_dir."/photos/".$pathinfo['basename']);
			$webalize_basename = Strings::webalize($pathinfo['filename']).".".$pathinfo['extension'];
			$image->save($photos_dir."/photos/".$webalize_basename);
			chmod($photos_dir."/photos/".$webalize_basename, 0777);
	
			$max_position = $this->photo->findAll()
										->where(['vehicle_id' => $vehicle_id])
                                        ->max('position');

			$this->photo->insert(array("file" => $webalize_basename,
									   "vehicle_id" => $vehicle_id,
									   "width" => $image->width,
									   "height" => $image->height,
									   "position" => $max_position + 1,
									  ));

			$image->resize(NULL, 240);
			$image->save($photos_dir."/previews/".basename($file_path));
			
			unset($image);
		}
		
		$this->redirect("detail", $vehicle_id);
	}
	
	function actionSortPhotos($vehicle_id) {
		$galery = $this->vehicle->get($vehicle_id);
		$photos = $this->photo->findAll()
							  ->where(['vehicle_id' => $vehicle_id])
							  ->order('CONCAT(REPEAT("0", 18 - LENGTH(file)), file)');
		
		$position = 1;
		foreach($photos as $photo) {
			$this->photo->update($photo->id, ['position' => $position]);
			$position++;
		}
				
		$this->redirect("detail", $vehicle_id);
	}	
	
	
	
	function handleUpdateDescription($photo_id, $text) {
    	$this->photo->update($photo_id, ["description" => $text]);
    	$this->sendPayload();
	}	
	
	function handleUpdatePosition($vehicle_id, $photo_id, $new_position) {    	
        $old_position = $this->photo->get($photo_id)
                                    ->position;
		
        if($old_position != $new_position) {
            $max_position = $this->photo->findAll()
            							->where(['vehicle_id' => $vehicle_id])
                                        ->max('position');
            
            $this->photo->update($photo_id, ['position' => $new_position]);
            $sign = $old_position < $new_position ? "-" : "+";
            $this->photo->findAll()
                        ->where("id != ? AND position BETWEEN ? AND ?", $photo_id, min($old_position, $new_position), max($old_position, $new_position))
						->where(['vehicle_id' => $vehicle_id])
                        ->update(["position" => new SqlLiteral("position {$sign} 1")]);
        }
        
        $this->sendPayload();
	}	
}
