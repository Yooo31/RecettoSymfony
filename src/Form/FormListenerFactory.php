<?php

namespace App\Form;

use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\SluggerInterface;

class FormListenerFactory
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function autoSlug(string $field): callable
    {
        return function (PreSubmitEvent $event) use ($field) {
            $data = $event->getData();

            $data['slug'] = strtolower($this->slugger->slug($data[$field]));
            $event->setData($data);
        };
    }

    public function attachTimestamps(): callable
    {
        return function (PostSubmitEvent $event) {
            $data = $event->getData();

            if (!$data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }

            $data->setUpdatedAt(new \DateTimeImmutable());
        };
    }
}
