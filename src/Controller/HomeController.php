<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function __invoke(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/answer/idle', name: 'app_answer_idle', methods: ['GET'])]
    public function idle(): Response
    {
        return $this->render('partials/_answer.html.twig', [
            'status' => 'idle',
            'answer' => 'Jack is waiting.',
            'question' => '',
        ]);
    }

    #[Route('/ask', name: 'app_ask', methods: ['POST'])]
    public function ask(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('ask_oracle', (string) $request->request->get('_token'))) {
            return $this->render('partials/_answer.html.twig', [
                'status' => 'blocked',
                'answer' => 'Jack lost the thread. Refresh the page and try again.',
                'question' => '',
            ], new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $question = trim((string) $request->request->get('question'));
        $seededAnswer = trim((string) $request->request->get('seeded_answer'));

        if ($question === '') {
            return $this->render('partials/_answer.html.twig', [
                'status' => 'blocked',
                'answer' => 'Ask a question first.',
                'question' => '',
            ], new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        return $this->render('partials/_answer.html.twig', [
            'status' => $seededAnswer === '' ? 'wild' : 'seeded',
            'answer' => $seededAnswer !== '' ? $seededAnswer : $this->fallbackAnswer($question),
            'question' => $question,
        ]);
    }

    private function fallbackAnswer(string $question): string
    {
        $answers = [
            'Jack sees the shape of it, but not the details.',
            'Not yet. The timing is off.',
            'Yes, but not in the way you expect.',
            'The answer is closer than you think.',
            'Leave it alone for now and watch what changes.',
            'Someone nearby already knows and is not saying it.',
        ];

        return $answers[abs(crc32($question)) % count($answers)];
    }
}
