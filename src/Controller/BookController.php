<?php
//Probably needs validator for the request!!!!
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Book;
use App\Service\UpdateProduct;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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

    //insufficient cause strange post requse error
    #[Route('/new', name: 'app_book', methods:["POST"])]
    public function new(ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse
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

       $errors = $validator->validate($book);


    if (count($errors) > 0) {
    
        $errorsString = (string) $errors;
        
        // get non valid fields
        preg_match_all('/\.(.*?):/', $errorsString, $result);
        $nonValidFields= $result[1];
        
        $nonValidFieldsText = "";

        foreach ($nonValidFields as $key=> $value) {
            if($key+1 == count($nonValidFields)) {
                $nonValidFieldsText .= "$value";
            }
            else {
            $nonValidFieldsText .= "$value, ";
            }
          }

        return $this->json(
            "The following fields are invalid: ".$nonValidFieldsText 
        );
    }

    //    $DBmanager->persist($book);
    //    $DBmanager->flush();

       $title= $requestContent->title;

        return $this->json(
            "The product {$errors} was saved in the DB"
        );
    }
  
    // tested on 08.01.2023 fully sufficient
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

    // tested on 08.01.2023 fully sufficient
    #[Route('/update/{id}', name: 'app_book_update', methods:["PATCH"],requirements: ['id' => '\d+'])]
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


    // tested on 08.01.2023 fully sufficient
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



