<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                ->from($data->email)
                ->to($data->service)
                ->subject('Demande de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['data' => $data]);

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Votre message a bien été envoyé');
                return $this->redirectToRoute('contact');

            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de l\'email');
            }
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form
        ]);
    }
}
