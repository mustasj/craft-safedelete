<?php

namespace Craft;

class SafeDeletePlugin extends BasePlugin
{
    public function init()
    {
        Craft::import('plugins.safedelete.elementactions.SafeDelete_BaseDeleteElementAction');

        $this->initEventHandlers();

        if (craft()->request->isCpRequest()) {

        }
    }

    public function initEventHandlers()
    {

    }

    public function getName()
    {
        return Craft::t('SafeDelete');
    }

    public function getVersion()
    {
        return '0.0.1';
    }

    public function getDeveloper()
    {
        return 'Christian Ruhstaller';
    }

    public function getDeveloperUrl()
    {
        return 'http://goldinteractive.ch';
    }

    public function addAssetActions($source)
    {
        $actions = [];

        if (preg_match('/^folder:(\d+)$/', $source, $matches))
        {
            $folderId = $matches[1];

            if (craft()->assets->canUserPerformAction($folderId, 'removeFromAssetSource')) {
                $action = craft()->elements->getAction('SafeDelete_DeleteAssets');

                $action->setParams(
                    [
                        'label' => Craft::t('Safe Delete…'),
                    ]
                );
                $actions[] = $action;
            }
        }

        return $actions;
    }

    public function addEntryActions($source)
    {
        $actions = [];

        // Get the section(s) we need to check permissions on
        switch ($source)
        {
            case '*':
            {
                $sections = craft()->sections->getEditableSections();
                break;
            }
            case 'singles':
            {
                $sections = craft()->sections->getSectionsByType(SectionType::Single);
                break;
            }
            default:
            {
                if (preg_match('/^section:(\d+)$/', $source, $matches))
                {
                    $section = craft()->sections->getSectionById($matches[1]);

                    if ($section)
                    {
                        $sections = array($section);
                    }
                }
            }
        }

        $userSessionService = craft()->userSession;

        if (
            $userSessionService->checkPermission('deleteEntries:'.$section->id) &&
            $userSessionService->checkPermission('deletePeerEntries:'.$section->id)
        )
        {
            $action = craft()->elements->getAction('SafeDelete_Delete');

            $action->setParams(
                [
                    'label' => Craft::t('Safe Delete…'),
                ]
            );
            $actions[] = $action;
        }

        return $actions;
    }
}