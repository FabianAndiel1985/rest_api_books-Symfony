<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Book;


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
       #request = Request::create('/hello-world','GET', [], [],[], [], $content );
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

        $entityManager = $doctrine->getManager();

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->json(
            "The book with the {$id} has been deleted"
        );
    }


    #[Route('/update/{id}', name: 'app_book_update', methods:["PUT","GET"],requirements: ['id' => '\d+'])]
    public function update(int $id, ManagerRegistry $doctrine): JsonResponse
    {

        $book= $doctrine->getRepository(Book::class)->find($id);

        #$request = Request::createFromGlobals();

        // Mock content with std class

        $content = new \stdClass;
        $content->title='Coaching nach Fabian Andiel';
        $content->description='In seinem neuem Meisterwerk Fabian Andiel ist es ihm gelungen, alles zu ergründen.';
        $content->author='';
        $content->isbn='';

        $content= json_encode($content);

        $request = Request::create('/hello-world','GET', [], [],[], [], $content );

        $requestContent = json_decode($request->getContent()); 
        
    //    $book = new Book();
    //    $book->setTitle($requestContent->title);
    //    $book->setDescription($requestContent->description);
    //    $book->setAuthor($requestContent->author);
    //    $book->setIsbn($requestContent->isbn);
    //    $book->setPrice($requestContent->price);
    //    $book->setAvailable($requestContent->available);



        //ToDo: createOwnService in Symphony: check for keyname and when value is not empty substitute it in the object then return the object
        //have a look at documentation
        var_dump($requestContent); 

        $foo = (array) $book;

        print_r($foo);
    
    foreach($requestContent as $key => $value) {
        #print "$key => $value\n";
        if(!empty($value)){
            
            
        }
    }
    

        //persist the updated book here ==================
        // $entityManager = $doctrine->getManager();

        // $entityManager->persist($book);
        // $entityManager->flush();

        //==============================================

        return $this->json(
            "The book with the {$id} has been updated"
        );
    }



    #[Route('/all', name: 'app_book_all', methods:["GET"])]
    public function all(ManagerRegistry $doctrine ): JsonResponse
    {

       $products = $doctrine->getRepository(Book::class)->findAll();

       foreach ($products as $product) {
        $data[] = [
            'id' => $product->getId(),
            'name' => $product->getTitle(),
            'description' => $product->getDescription(),
            'author' => $product->getAuthor(),
            'isbn' => $product->getIsbn(),
            'price'=> $product->getPrice(),
            'available'=>$product->isAvailable()
        ];
     }

        return $this->json(
            $data,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }



    
}



