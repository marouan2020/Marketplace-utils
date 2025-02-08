<?php

namespace MageTunisia\Marketplace\Test\Functional;

use Magento\Framework\App\ObjectManager;
use MageTunisia\Marketplace\Utils\JsonFileManager;
use PHPUnit\Framework\TestCase;

class TestProductSync extends TestCase
{
    protected JsonFileManager $jsonFileManager;

    protected function setUp(): void
    {
        // Configuration de l'environnement Magento pour les tests
        $objectManager         = ObjectManager::getInstance();
        $this->jsonFileManager = $objectManager->create(JsonFileManager::class);
    }

    /**
     * Test pour la méthode syncProduct
     */
    public function testSaveJsonInFile()
    {
        // Simuler une entrée JSON pour le produit
        $data = [
          "sku"              => "sku-123456789",
          "name"             => "Product test",
          "price"            => 125,
          "status"           => 1,
          "visibility"       => 4,
          "type_id"          => "simple",
          "attribute_set_id" => 4
        ];
        $dataJson = json_encode($data);

        $pathfile = date('Y-m-dH:i').'.json';

        // Appeler la méthode syncProduct
        $filePath = $this->jsonFileManager->saveJsonInFile($dataJson, $pathfile);
        $this->assertFileExists($filePath, 'Le fichier JSON n\'a pas été créé correctement.');
    }

    /**
     * Test l'échec de la sauvegarde d'un fichier JSON (chemin invalide)
     */
    public function testSaveJsonInHerFileFailure()
    {
        $jsonData = json_encode(['sku' => 'invalid-product']);
        $fileName = 'invalid.json';

        // Tester que la méthode retourne `false` en cas d'échec
        $this->assertFalse($this->jsonFileManager->saveJsonInFile($jsonData, $fileName), 'La sauvegarde aurait dû échouer mais a réussi.');
    }
}
