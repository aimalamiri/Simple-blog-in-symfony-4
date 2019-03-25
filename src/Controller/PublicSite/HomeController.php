<?php

namespace App\Controller\PublicSite;

use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    /**
     * @Route("/", name="public_site_home")
     */
    public function index(Request $request)
    {
        $db = $this->getDoctrine()->getManager();

        $listPosts = $db->getRepository(Posts::class)->findByPage($request->query->getInt('page', 1));

        return $this->render('public_site/home/index.html.twig', [
            'posts' => $listPosts,
        ]);
    }

    /**
     * @Route("/post/{url}", name="public_site_post")
     */
    public function show(string $url)
    {
        $post = $this->getDoctrine()->getRepository(Posts::class)->findByUrl($url);
        if (!$post) {
            return $this->createNotFoundException();
        }

        return $this->render('public_site/home/post.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/tag/{tag}", name="public_site_tag")
     */
    public function byTags(string $tag, Request $request)
    {
        $db = $this->getDoctrine()->getManager();

        $listPosts = $db->getRepository(Posts::class)->findByPage($request->query->getInt('page', 1), 2, $tag);

        return $this->render('public_site/home/index.html.twig', [
            'posts' => $listPosts,
        ]);
    }
}
