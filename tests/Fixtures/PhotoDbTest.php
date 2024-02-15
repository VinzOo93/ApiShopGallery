<?php

namespace App\Tests\Fixtures;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Tests\Base\DataTestBase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * PhotoDbTest.
 */
class PhotoDbTest extends DataTestBase
{
    private const URL_HOST_CDN = 'https://ucarecdn.com/';
    private const URL_PARAM_CDN = '/-/preview/1880x864/-/quality/smart/-/format/auto/';

    private PhotoRepository $photoRepository;
    private HttpClientInterface $client;

    /**
     * @var array<int, Photo>
     */
    private array $photos;

    /**
     * getContainerPhoto.
     */
    protected function getContainerPhoto(): void
    {
        $this->initContainerDataBase();
        $this->photoRepository = $this->container->get(PhotoRepository::class);
        $this->client = HttpClient::create();
    }

    /**
     * testsPhotoSetUp.
     */
    public function testsPhotoSetUp(): void
    {
        $this->getContainerPhoto();
        $this->photos = $this->getAllPhotos();
        $this->testPhotoData();
        $this->testPhotoUrl();
    }

    /**
     * testPhotoData.
     */
    protected function testPhotoData(): void
    {
        foreach ($this->photos as $photo) {
            $this->assertInstanceOf(Photo::class, $photo, "l'objet retourné ne provient pas de la classe Photo");

            $this->checkDbUnicity($this->photoRepository, [
                'key' => 'name',
                'value' => $photo->getName(),
            ]);

            $this->checkDbUnicity($this->photoRepository, [
                'key' => 'urlCdn',
                'value' => $photo->getUrlCdn(),
            ]);
        }
    }

    /**
     * testPhotoUrl.
     * @throws TransportExceptionInterface
     */
    protected function testPhotoUrl(): void
    {
        foreach ($this->photos as $photo) {
            $urlCdnImage = self::URL_HOST_CDN.$photo->getUrlCdn().self::URL_PARAM_CDN;
            $response = $this->client->request('GET', $urlCdnImage);

            $this->assertEquals(
                200,
                $response->getStatusCode(),
                "L'image ".$photo->getName()." dont l'url est  : $urlCdnImage n'a pas été trouvée dans le cdn"
            );
            $this->assertEquals(
                'image/jpeg',
                $response->getHeaders()['content-type'][0],
                "L'image ".$photo->getName()." dont l'url est  : $urlCdnImage n'est retournée au format image/jpeg"
            );
        }
    }

    /**
     * @return Photo[]
     */
    private function getAllPhotos(): array
    {
        return $this->photoRepository->findAll();
    }
}
