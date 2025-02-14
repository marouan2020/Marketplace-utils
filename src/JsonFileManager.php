<?php

declare(strict_types=1);

namespace MageTunisia\Marketplace\Utils;

use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

class JsonFileManager
{
    private DirectoryList $directoryList;
    private LoggerInterface $logger;

    public function __construct(DirectoryList $directoryList, LoggerInterface $logger)
    {
        $this->directoryList = $directoryList;
        $this->logger        = $logger;
    }

    public function saveJsonInFile(string $dataJson, string $filename): ?string
    {
        try {
            // Récupérer le chemin du dossier 'marketplace' sous 'pub/media/'
            $mediaDirectory  = $this->directoryList->getPath(DirectoryList::MEDIA);
            $marketplacePath = $mediaDirectory . '/marketplace';

            // Vérifier si le dossier existe, sinon le créer
            if (!is_dir($marketplacePath)) {
                mkdir($marketplacePath, 0777, true);
                $this->logger->debug('Dossier marketplace créé : ' . $marketplacePath);
            }

            $filePath = $marketplacePath . '/' . $filename;

            // Lire l'ancien contenu si le fichier existe
            if (file_exists($filePath)) {
                $existingData    = file_get_contents($filePath);
                $existingData    = json_decode($existingData, true) ?? [];
            } else {
                $existingData = [];
            }

            // Ajouter la nouvelle donnée
            $newData = json_decode($dataJson, true);

            $newData = array_merge($existingData, $newData);

            // Écrire les données dans le fichier
            if (file_put_contents($filePath, json_encode($newData, JSON_PRETTY_PRINT)) === false) {
                throw new \Exception('Impossible d’ajouter les données au fichier JSON.');
            }
            $this->logger->debug('Fichier JSON mis à jour avec succès : ' . $filePath);

            return $filePath;
        } catch (\Exception $e) {
            $this->logger->debug('Erreur : ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Convert file json in array.
     *
     * @param string $identifiant
     *   File id.
     *
     * @return array
     *   Data infos.
     */
    public function getDataById(string $identifiant): array
    {
        try {
            // Définir le chemin du fichier JSON
            $filePath = $this->directoryList->getPath('media') . "/marketplace/{$identifiant}.json";

            // Vérifier si le fichier existe
            if (!file_exists($filePath)) {
                throw new \Exception("Le fichier JSON n'existe pas : " . $filePath);
            }

            // Lire le fichier JSON
            $jsonData  = file_get_contents($filePath);
            $dataArray = json_decode($jsonData, true);

            // Vérifier si la conversion JSON => Array a réussi
            if (!is_array($dataArray)) {
                throw new \Exception("Erreur lors du décodage JSON.");
            }

            return $dataArray;
        } catch (\Exception $e) {
            $this->logger->error("Erreur dans getDataById : " . $e->getMessage());
            return [];
        }
    }
}
