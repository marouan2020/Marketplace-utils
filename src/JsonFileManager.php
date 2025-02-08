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
    $this->logger = $logger;
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
        $existingData = file_get_contents($filePath);
        $jsonArray = json_decode($existingData, true) ?? [];
      } else {
        $jsonArray = [];
      }

      // Ajouter la nouvelle donnée
      $jsonArray[] = json_decode($dataJson, true);

      // Écrire les données dans le fichier
      if (file_put_contents($filePath, json_encode($jsonArray, JSON_PRETTY_PRINT)) === false) {
        throw new \Exception('Impossible d’ajouter les données au fichier JSON.');
      }
      $this->logger->debug('Fichier JSON mis à jour avec succès : ' . $filePath);

      return $filePath;
    } catch (\Exception $e) {
      $this->logger->debug('Erreur : ' . $e->getMessage());
    }

    return null;
  }

}
