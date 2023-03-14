<?php

namespace App\Controller;

use App\Entity\Products;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: "api_")]
class ProductsController extends AbstractController
{
    #[Route('/products', name: 'products', methods: 'GET')]
    public function index(): JsonResponse
    {
        $sql = "SELECT * FROM products";
        $stmt = $this->db->getConnection()->prepare($sql);
        $products = $stmt->executeQuery()->fetchAllAssociative();

        return $this->json($products);
    }

    // TODO: Remove, for debug only.
    #[Route('/products/deleteAll', name: 'delete_products', methods: 'GET')]
    public function deleteAll(): JsonResponse
    {
        $sql = "DELETE FROM products";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->executeQuery();

        return $this->json(['message' => 'all products successfully deleted']);
    }

    #[Route('/products/{id}', name: 'products_by_id', methods: 'GET')]
    public function getProductsById($id): JsonResponse
    {
        $sql = "SELECT * FROM products WHERE id = $id";
        $stmt = $this->db->getConnection()->prepare($sql);
        $products = $stmt->executeQuery()->fetchAllAssociative();

        return $this->json($products);
    }

    // TODO: Remove, for debug only.
    #[Route('/products', name: 'add_product', methods: 'POST')]
    public function add(): JsonResponse
    {
        $result = $this->validateRequest($_POST);

        if (!empty($result)) {
            return $this->json($result);
        }
        $result = $this->insertProduct($_POST);

        if ($result) {
            return $this->json(['message' => 'Product sucessfully added to the database']);
        }
        return  $this->json(['errors' => 'something went wrong, please contact our team']);
    }

    private function insertProduct(array $request): bool
    {
        $title = (string) $request['title'];
        $description = (string) $request['description'];
        $price = (float) $request['price'];
        $quantity = (int) $request['quantity'];

        $sql = "INSERT INTO products (`title`, `description`, `price`, `quantity`) VALUES ('$title', '$description', '$price', '$quantity')";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->executeQuery();
        return true;
    }

    private function validateRequest(array $request): array
    {
        $result = [];

        if (!isset($request['title'])) {
            $result['errors'][] = 'Title is not set';
        }
        if (!isset($request['description'])) {
            $result['errors'][] = 'Description is not set';
        }
        if (!isset($request['price'])) {
            $result['errors'][] = 'Price is not set';
        }
        if (!isset($request['quantity'])) {
            $result['errors'][] = 'Quantity is not set';
        }

        return $result;
    }
}
