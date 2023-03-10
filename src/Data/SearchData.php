<?php


namespace App\Data;

class SearchData
{

    public int $page = 1;

    /*
     * @var string
     */
    public null|string|int $q = '';

    public null|object $vernacularName;
    public null|object $typology;

    public null|object $gods;
    public null|array $relatedGods = [];

    public null|object $population;
    public null|object $origin;

    public int $antequemDatation;
    public int $preciseDatation;
    public int $postquemDatation;

    public null|array $materials = [];
    public null|array $state = [];

    public bool $isBasemented = false;

    public null|array $expositionLocation = [];

    public null|array $floor = [];

    public null|string|int $showcaseCode;
    public null|string|int $shelf;

    //SORT
    public bool $isSortAlpha = false;
    public bool $isSortAlphaReverse = false;
    public bool $isSortNumeric = false;
    public bool $isSortNumericReverse = false;
    public bool $sortDateUpdate = false;
    public null|array $updatedBy = [];
}