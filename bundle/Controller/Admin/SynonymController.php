<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Controller\Admin;

use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\Form\Type\LanguageSelectType;
use Netgen\TagsBundle\Form\Type\SynonymCreateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function count;

final class SynonymController extends Controller
{
    private TagsService $tagsService;

    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
    }

    /**
     * This method is called for add new synonym action without selected language.
     * It renders a form to select language for the keyword of new synonym.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $mainTagId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addSynonymSelectAction(Request $request, $mainTagId): Response
    {
        $this->denyAccessUnlessGranted('ibexa:tags:addsynonym');

        $availableLanguages = $this->getConfigResolver()->getParameter('languages');
        if (count($availableLanguages) === 1) {
            return $this->redirectToRoute(
                'netgen_tags_admin_synonym_add',
                [
                    'mainTagId' => (int) $mainTagId,
                    'languageCode' => $availableLanguages[0],
                ]
            );
        }

        $form = $this->createForm(
            LanguageSelectType::class,
            null,
            [
                'action' => $request->getPathInfo(),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute(
                'netgen_tags_admin_synonym_add',
                [
                    'mainTagId' => (int) $mainTagId,
                    'languageCode' => $form->getData()['languageCode'],
                ]
            );
        }

        return $this->render(
            '@NetgenTags/admin/tag/select_translation.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * This method renders view with a form for adding new synonym.
     * After form is being submitted, it stores new synonym and redirects to it.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $mainTagId
     * @param string $languageCode
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSynonymAction(Request $request, $mainTagId, string $languageCode): Response
    {
        $this->denyAccessUnlessGranted('ibexa:tags:addsynonym');

        $synonymCreateStruct = $this->tagsService->newSynonymCreateStruct((int) $mainTagId, $languageCode);

        $form = $this->createForm(
            SynonymCreateType::class,
            $synonymCreateStruct,
            [
                'action' => $request->getPathInfo(),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newSynonym = $this->tagsService->addSynonym($form->getData());

            $this->addFlashMessage('success', 'tag_added', ['%tagKeyword%' => $newSynonym->keyword]);

            return $this->redirectToTag($newSynonym);
        }

        return $this->render(
            '@NetgenTags/admin/tag/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
