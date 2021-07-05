<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;

class PaginationService
{
    private $pg;

    public function __construct(PaginatorInterface $paginator){
        $this->pg = $paginator;
    }

    public function settingPagination(array $data, string $page, string $limit)
    {
        if(preg_match('$^-\d+$',$page)){
            return 'redirect';
        }
        $pagination = $this->pg->paginate($data, $page, $limit);
        if(!$pagination->getItems()){
            return 'redirect';
        }
        return $pagination;
    }
}
