<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class fileService 
{
    private $uploadsDirectory;

    public function __construct($uploadsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function upload(UploadedFile $file, $folder, $fileName)
    {
        $imageName = $fileName . '.' . $file->guessExtension();
        $file->move($folder, $imageName);
        return $imageName;
    }

    public function update(UploadedFile $file, $oldImageName, $newFileName, $folder)
    {
        $newImageName = $newFileName . '.' . $file->guessExtension();
        unlink($folder . '/' . $oldImageName); 
        $file->move($folder, $newImageName);
        return $newImageName;
    }

    public function deleteFolder($folderName) //Supprime un dossier et tout son contenu du serveur
    {
        $files = glob($this->uploadsDirectory . '/' . $folderName . '/*');
        foreach ($files as $file) {
            is_dir($file) ? deleteFolder($file) : unlink($file); //Si le fichier est un dossier on relane la fonction dans ce dossier sinon on supprime le fichier
        }
        rmdir($this->uploadsDirectory . '/' . $folderName); //Suppime le dossier une fois vide
        return;
    } 

    public function moveTempFolder($episode) // Déplace le dossier /uploads/temp/ vers le dossier du pays et le renomme avec la date de l'episode
    {

    }

} //End of class