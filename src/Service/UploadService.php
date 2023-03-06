<?php

namespace App\Service;

use App\Entity\Objects\Media\Image;
use App\Entity\Objects\Media\Video;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{
    public function __construct(
        private string $publicDirectory,
    ){}

    public function isImage($file): bool
    {
        $extension = $file->getClientOriginalExtension();
        $arrVideoExtension  = ['jpeg', 'jpg', 'gif', 'png', 'bmp'];

        if(in_array(strtolower($extension), $arrVideoExtension))
        {
            return true;
        }
        return false;
    }

    public function isVideo($file): bool
    {
        $extension = $file->getClientOriginalExtension();
        $arrVideoExtension  = ['mov', 'mp4', 'avi', 'wmv', 'flv', '3gp', 'webm'];

        if(in_array(strtolower($extension), $arrVideoExtension))
        {
            return true;
        }
        return false;
    }

    public function isFile($file): bool
    {
        $extension = $file->getClientOriginalExtension();
        $arrVideoExtension  = ['pdf', 'xls', 'xlsx'];

        if(in_array(strtolower($extension), $arrVideoExtension))
        {
            return true;
        }
        return false;
    }

    public function upload(UploadedFile $file, $entity, $fileNameCode): string
    {

        $fileName = $fileNameCode.'.'.$file->getClientOriginalExtension();

        if ($this->isImage($file)) {
            $path = $this->publicDirectory . Image::UPLOAD_DIR;
            $file->move($path, $fileName);
        } elseif ($this->isVideo($file)) {
            $path = $this->publicDirectory . Video::UPLOAD_DIR;
            $file->move($path, $fileName);
        } elseif ($this->isFile($file)) {
            $path = $this->publicDirectory . Video::UPLOAD_DIR;
            $file->move($path, $fileName);
        }

        return $fileName;
    }


    public function createFileName(UploadedFile $file, $entity, $repository): string
    {
        $arrCode    = [];
        $arrLetter  = [];
        $alphas = range('a', 'z');

        if ($this->isImage($file)) {

            $arrImages  = $repository->findAllImagesByObject($entity);

            //extraire code pour toutes les images de l'objet
            foreach ($arrImages as $fileCode) {
                $arrCode[] = $fileCode->getCode();
            }

            //extraire chacune des lettres unique pour les fichiers de l'objet
            foreach ($arrCode as $code) {
                $arrLetter[] = explode('_', $code)[1];
            }

            //Attribuer lettre du regex si inexistante
            foreach ($alphas as $a) {
                if (in_array($a, $arrLetter) == false) {
                    return $entity->getCode().'_'.$a;
                }
            }

            return $entity->getCode().'_'.'none';

        } elseif ($this->isVideo($file)) {

            $arrVideos  = $repository->findAllVideosByObject($entity);

            //extraire code pour toutes les videos de l'objet
            foreach ($arrVideos as $vidCode) {
                $arrCode[] = $vidCode->getCode();
            }

            //extraie chacune des lettre pour toutes les images de l'objet
            foreach ($arrCode as $code) {
                $arrLetter[] = explode('_', $code)[2];
            }
            //Attribuer lettre du regex si inexistante
            foreach ($alphas as $a) {
                $res = in_array($a, $arrLetter );
                if ($res == false) {
                    return $entity->getCode().'_video_'.$a;
                }
            }
            return $entity->getCode().'_'.'none';

        } elseif ($this->isFile($file)) {
            $arrFiles  = $repository->findAllFilesByObject($entity);

            //extraire code pour toutes les videos de l'objet
            foreach ($arrFiles as $fileCode) {
                $arrCode[] = $fileCode->getCode();
            }

            //extraie chacune des lettre pour toutes les images de l'objet
            foreach ($arrCode as $code) {
                $arrLetter[] = explode('_', $code)[2];
            }
            //Attribuer lettre du regex si inexistante
            foreach ($alphas as $a) {
                $res = in_array($a, $arrLetter );
                if ($res == false) {
                    return $entity->getCode().'_file_'.$a;
                }
            }
            return $entity->getCode().'_'.'none';
        }
        return $entity->getCode().'_'.'none';
    }

    public function deleteFile($entity): bool
    {
        if ($entity !== null) {
            $path =  $this->publicDirectory . $entity::UPLOAD_DIR . $entity->getSrc();
            $filesystem = new Filesystem();
            $filesystem->remove($path);
            return true;
        }
        return false;
    }

}