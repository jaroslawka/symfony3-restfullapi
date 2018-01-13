<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Entity\Gnome;

class GnomeController extends FOSRestController 
{

    /**
     * @Rest\Get("/api/test")    
     * 
     * @return mixed
     */
    public function testAction() 
    {

        return new View("OK", Response::HTTP_OK);
    }

    /**
     * Helper funtion for convert UploadedFile to raw data
     * 
     * @param UploadedFile $uploaded
     * @return binary
     */
    protected function uploadedFileToRawConvert(UploadedFile $uploaded) 
    {

        $realPath = $uploaded->getRealPath();
        if (is_file($realPath)) {
            $stream = fopen($uploaded->getRealPath(), 'rb');
            $raw = stream_get_contents($stream);

            return $raw;
        }

        return null;
    }

    /**
     * Create new Gnome action
     * 
     * @Rest\Post("/api/gnome")
     * @Rest\RequestParam(name="name", requirements="[a-zA-Z0-9 -]+", strict=true, description="Gnome name")
     * @Rest\RequestParam(name="strength", requirements="^[1-9][0-9]?$|^100$", strict=true, description="Gnome strength")
     * @Rest\RequestParam(name="age", requirements="^[1-9][0-9]?$|^100$", strict=true, description="Gnome age")
     * @Rest\RequestParam(name="avatar", description="File: image/png")
     * @Rest\FileParam(name="avatar", requirements={"mimeTypes"="image/png"}, image=true)
     *
     * @param ParamFetcher $paramFetcher
     * @return mixed
     * 
     * WARNING: php7-fileinfo required for avatar validation !
     */
    public function postAction(ParamFetcher $paramFetcher) 
    {

        $name = $paramFetcher->get('name');
        $strength = $paramFetcher->get('strength');
        $age = $paramFetcher->get('age');
        $avatar = $paramFetcher->get('avatar');

        try {
            $data = new Gnome;

            $data->setName($name);
            $data->setStrength($strength);
            $data->setAge($age);
            if ($avatar instanceof UploadedFile) {
                $avatarRaw = $this->uploadedFileToRawConvert($avatar);
                $data->setAvatar($avatarRaw);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($data);
            $manager->flush();

            return new View("Gnome created successfully", Response::HTTP_CREATED);
        } catch (\Exception $e) {

            return new View("Internal server error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
