<?php 

namespace App\Tests\Fixtures;

use App\Entity\Photo;
use App\Repository\PhotoRepository;

class PhotoDbTest extends DataTest
{ 
    private PhotoRepository $photoRepository;

    protected function getContainerPhoto(): void
    {
        $this->initContainer();
        $this->photoRepository = $this->container->get(PhotoRepository::class);   
    }

    public function testsPhotoSetUp(): void 
    {
        $this->getContainerPhoto();
        $this->testPhotoData();
    }

    protected function testPhotoData(): void
    {
        $photos = $this->photoRepository->findAll(); 

        foreach ($photos as $photo) {
           $this->assertInstanceOf(Photo::class, $photo, "l'objet retourné ne provient pas de la classe Photo");
           
           $this->checkDbUnicity($this->photoRepository, [
            'key' => 'name',
            'value' => $photo->getName()
            ]);

          $this->checkDbUnicity($this->photoRepository, [
            'key' => 'urlCdn',
            'value' => $photo->getUrlCdn()
            ]);

        }
    }

}