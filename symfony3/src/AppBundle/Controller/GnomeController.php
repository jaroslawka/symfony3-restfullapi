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

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class GnomeController extends FOSRestController 
{

    /**
     * API Test
     * 
     * @Rest\Get("/api/test")    
     * 
     * @SWG\Response(
     *     response=200,
     *     description="TEST OK",
     * )
     * 
     * @return mixed
     */
    public function testAction() 
    {

        return new View("TEST OK", Response::HTTP_OK);
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
     * NOTICE: remove line with FileParam for test API without upload file
     *
     * @param ParamFetcher $paramFetcher
     * @return mixed
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Gnome created successfully",
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal server error",
     * )
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
            
            return $data;

        } catch (\Exception $e) {
            
            return new View("Internal server error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Read Gnome action 
     *
     * @Rest\Get("/api/gnome/{id}", requirements={"id"="\d+"})    
     * 
     * @SWG\Response(
     *     response=200,
     *     description="JSON Result",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Gnome not found",
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal server error",
     * )
     * 
     * @param int $id
     * @return mixed
     */
    public function getAction(int $id) 
    {

        try {
            $result = $this->getDoctrine()->getRepository('AppBundle:Gnome')->find($id);

            if ($result === null) {

                return new View("Gnome not found", Response::HTTP_NOT_FOUND);
            }

            // convert Avatar data to base64 string
            $avatar = $result->getAvatar();
            if (is_resource($avatar)) {
                $result->setAvatar(base64_encode(stream_get_contents($avatar)));
            }

            return $result;

        } catch (\Exception $e) {
            
            return new View("Internal server error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Gnome action
     * 
     * @Rest\Put("/api/gnome/{id}", requirements={"id"="\d+"})
     * @Rest\RequestParam(name="name", requirements="[a-zA-Z0-9 -]+", description="Gnome name")
     * @Rest\RequestParam(name="strength", requirements="^[1-9][0-9]?$|^100$", description="Gnome strength")
     * @Rest\RequestParam(name="age", requirements="^[1-9][0-9]?$|^100$", description="Gnome age")
     * @Rest\RequestParam(name="avatar", description="File: image/png")
     * @Rest\FileParam(name="avatar", requirements={"mimeTypes"="image/png"}, image=true)
     * NOTICE: remove line with FileParam for test API without upload file
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Gnome updated successfully",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Gnome not found",
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal server error",
     * )
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return mixed
     * 
     * WARNING: php7-fileinfo required for avatar validation !
     */
    public function putAction(int $id, ParamFetcher $paramFetcher) 
    {
        
        $name = $paramFetcher->get('name');
        $strength = $paramFetcher->get('strength');
        $age = $paramFetcher->get('age');
        $avatar = $paramFetcher->get('avatar');

        try {

            $manager = $this->getDoctrine()->getManager();
            $gnome = $this->getDoctrine()->getRepository('AppBundle:Gnome')->find($id);

            if (empty($gnome)) {

                return new View("Gnome not found", Response::HTTP_NOT_FOUND);
            } else {

                if (!empty($name)) {
                    $gnome->setName($name);
                }
                if (!empty($strength)) {
                    $gnome->setStrength($strength);
                }
                if (!empty($age)) {
                    $gnome->setAge($age);
                }
                if ($avatar instanceof UploadedFile) {
                    $avatarRaw = $this->uploadedFileToRawConvert($avatar);
                    $gnome->setAvatar($avatarRaw);
                }

                $manager->flush();

                return new View("Gnome updated successfully", Response::HTTP_OK);
            }
        } catch (\Exception $e) {

            return new View("Internal server error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
     /**
     * Gnome delete action
     * 
     * @Rest\Delete("/api/gnome/{id}", requirements={"id"="\d+"})
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Gnome deleted successfully",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Gnome not found",
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Internal server error",
     * )
     * 
     * @param int $id
     * @return mixed
     */
    public function deleteAction(int $id) 
    {
        try { 
            
            $manager = $this->getDoctrine()->getManager();
            $gnome = $this->getDoctrine()->getRepository('AppBundle:Gnome')->find($id);

            if (empty($gnome)) {

                return new View("Gnome not found", Response::HTTP_NOT_FOUND);
            } else {

                    $manager->remove($gnome);
                    $manager->flush();

                    return new View("Gnome deleted successfully", Response::HTTP_OK);
            }
        
        } catch (Exception $e) {
                
                return new View("Internal server error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
