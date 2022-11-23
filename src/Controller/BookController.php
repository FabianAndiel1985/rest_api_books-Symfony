<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Book;
use App\Service\UpdateProduct;


#[Route('/books', name: 'books_')]
class BookController extends AbstractController
{

    #[Route('/', name: 'index', methods:["GET"])]
    public function index( ): JsonResponse
    {

        return $this->json(
            "Welcome to index page"
        );
    }

    #[Route('/new', name: 'app_book', methods:["POST"])]
    public function new(ManagerRegistry $doctrine ): JsonResponse
    {
      $DBmanager = $doctrine->getManager();

       $request = Request::createFromGlobals();
       $requestContent = json_decode($request->getContent()); 
        
       $book = new Book();
       $book->setTitle($requestContent->title);
       $book->setDescription($requestContent->description);
       $book->setAuthor($requestContent->author);
       $book->setIsbn($requestContent->isbn);
       $book->setPrice($requestContent->price);
       $book->setAvailable($requestContent->available);

       $DBmanager->persist($book);
       $DBmanager->flush();

       $title= $requestContent->title;

        return $this->json(
            "The product {$title} was saved in the DB"
        );
    }
  

    #[Route('/delete/{id}', name: 'app_book_delete', methods:["DELETE"],requirements: ['id' => '\d+'])]
    public function delete(int $id, ManagerRegistry $doctrine): JsonResponse
    {

        $book= $doctrine->getRepository(Book::class)->find($id);

        if(!$book) {
            return $this->json(
                "There is no book with the id {$id} to delete",
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );    
        }

        $entityManager = $doctrine->getManager();

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->json(
            "The book with the {$id} has been deleted",
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }

    #[Route('/update/{id}', name: 'app_book_update', methods:["PUT"],requirements: ['id' => '\d+'])]
    public function update(int $id, ManagerRegistry $doctrine, UpdateProduct $updateProduct): JsonResponse
    {

        $book= $doctrine->getRepository(Book::class)->find($id);

        $request = Request::createFromGlobals();

        if(!$book) {
            return $this->json(
                "There is no book with the id {$id} to update",
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );    
        }

        $requestContent = json_decode($request->getContent()); 
        
        $updatedBook =  $updateProduct->updateObjectValues($requestContent, $book);

        $entityManager = $doctrine->getManager();

        $entityManager->persist($updatedBook);

        $entityManager->flush();

        return $this->json(
            "The book with the id {$id} has been updated",
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }



    #[Route('/all', name: 'app_book_all', methods:["GET"])]
    public function all(ManagerRegistry $doctrine ): JsonResponse
    {

       $books = $doctrine->getRepository(Book::class)->findAll();

       if(!$books) {
        return $this->json(
            "There seems to be no data in the database",
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
       }

       foreach ($books as $book) {
        $data[] = [
            'id' => $book->getId(),
            'name' => $book->getTitle(),
            'description' => $book->getDescription(),
            'author' => $book->getAuthor(),
            'isbn' => $book->getIsbn(),
            'price'=> $book->getPrice(),
            'available'=>$book->isAvailable()
        ];
     }

        return $this->json(
            $data,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }
}



