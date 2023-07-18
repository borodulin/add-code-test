<?php

declare(strict_types=1);

namespace App\Infrastructure\ArgumentResolver;

use App\Infrastructure\Attribute\ApiForm;
use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ApiFormResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();
        if (!$type || !class_exists($type)) {
            return false;
        }

        return \count($argument->getAttributes(ApiForm::class)) > 0;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $hasBody = \in_array(
            $request->getMethod(),
            [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH],
            true
        );
        if ($hasBody) {
            $format = $request->getContentType();
            if ('json' === $format) {
                $formData = json_decode($request->getContent(), true);
            } elseif ('form' === $format) {
                $formData = $request->request->all();
            } else {
                $formData = [];
            }
        } else {
            $formData = $request->query->all();
        }

        /** @var ApiForm $attribute */
        foreach ($argument->getAttributes(ApiForm::class) as $attribute) {
            $formClass = $attribute->formClass;

            $form = $this->formFactory->create($formClass, null, [
                'data_class' => $argument->getType(),
            ]);

            $form->submit($formData, false);

            if ($form->isSubmitted() && $form->isValid()) {
                yield $form->getData();
            } else {
                $errors = $this->processErrors($form);

                throw new ValidationException($errors);
            }
        }
    }

    private function processErrors(FormInterface $form): array
    {
        $formName = $form->getName();
        $errors = [];

        foreach ($form->getErrors(true, true) as $formError) {
            $name = $formError->getOrigin()->getName() === $formName ? [] : [$formError->getOrigin()->getName()];
            $origin = $formError->getOrigin();

            while ($origin = $origin->getParent()) {
                if ($formName !== $origin->getName()) {
                    $name[] = $origin->getName();
                }
            }
            $fieldName = empty($name) ? 'global' : implode('_', array_reverse($name));

            if (!isset($errors[$fieldName])) {
                $errors[$fieldName] = [];
            }
            $errors[$fieldName][] = $formError->getMessage();
        }

        return $errors;
    }
}
