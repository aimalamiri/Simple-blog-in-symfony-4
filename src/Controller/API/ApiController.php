<?php

namespace App\Controller\API;

use App\Entity\Posts;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
final class ApiController extends AbstractController
{
    /**
 * @Route("/posts", name="posts_api")
 */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Posts::class);

        $posts = $repository->findAll();
        $postsArray = $repository->PostsFilteredArray($posts);

        return $repository->returnAsJSON($postsArray);
    }

    /**
     * @Route("/posts/{url}", name="posts_api_show")
     */
    public function show($url)
    {
        $repository = $this->getDoctrine()->getRepository(Posts::class);
        $post = $repository->findByUrl($url);
        $serializer = SerializerBuilder::create()->build();
        $post = $serializer->toArray($post);

        if (!$post) {
            return $this->createNotFoundException();
        }

        return $repository->returnAsJSON($post);
    }
}
