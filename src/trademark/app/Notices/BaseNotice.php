<?php

namespace App\Notices;

class BaseNotice
{
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        //
    }

    /**
     * Set Notice Detail
     *
     * @param int|null $targetID
     * @param int|null $typeAcc
     * @param string|null $targetPage
     * @param string|null $redirectPage
     * @param int|null $typeNotify
     * @param int|null $typePage
     * @param bool|null $isAction
     * @param string|null $content
     * @param string|null $attribute
     * @return array
     */
    public function setNoticeDetail(
        ?int $targetID = null,
        ?int $typeAcc = null,
        ?string $targetPage = null,
        ?string $redirectPage = null,
        ?int $typeNotify = null,
        ?int $typePage = null,
        ?bool $isAction = null,
        ?string $content = null,
        ?string $attribute = null
    ): array
    {
        return [
            'target_id' => $targetID,
            'type_acc' => $typeAcc,
            'target_page' => $targetPage,
            'redirect_page' => $redirectPage,
            'type_notify' => $typeNotify,
            'type_page' => $typePage,
            'is_action' => $isAction,
            'content' => $content,
            'attribute' => $attribute,
        ];
    }
}
