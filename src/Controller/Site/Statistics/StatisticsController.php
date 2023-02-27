<?php

namespace App\Controller\Site\Statistics;

//use App\Repository\Objects\CategoriesRepository;
use App\Repository\Objects\Metadata\FloorRepository;
use App\Repository\Objects\Metadata\GodsRepository;
use App\Repository\Objects\Metadata\MaterialsRepository;
use App\Repository\Objects\Metadata\ExpositionLocationRepository;
use App\Repository\Objects\Metadata\StateRepository;
use App\Repository\Objects\ObjectsRepository;
use App\Repository\Objects\Metadata\OriginRepository;
use App\Repository\Objects\Metadata\PopulationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatisticsController extends AbstractController
{

    public function __construct(
        private ObjectsRepository $objectsRepository,
        private GodsRepository $godsRepository,
        private OriginRepository $originRepository,
        private PopulationRepository $populationRepository,
        private MaterialsRepository $materialsRepository,
        private ExpositionLocationRepository $expositionLocationRepository,
        private FloorRepository $floorRepository,
        private StateRepository $stateRepository,
        private ChartBuilderInterface $chartBuilder,
    ){}


    #[Route('/statistics', name: 'statistics')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function index(): Response
    {
        $objects = $this->objectsRepository->findAllNoDeleted();

        $materials = $this->materialsRepository->findAll();



        //Matériaux
        $arrDatasMaterials = [];
        $arrLabelsMaterials = [];
        foreach ($materials as $material) {
            $arrDatasMaterials[] = count($material->getObjects());
        }
        foreach ($materials as $material) {
            $arrLabelsMaterials[] = $material->getName().' - '.count($material->getObjects());
        }

        //Exposition
        $arrDatasLocationsToCount = [];
        $arrLabelsLocations = [];
        foreach ($objects as $object) {
            if ($object->getExpositionLocation() != null) {
                $arrDatasLocationsToCount[] = $object->getExpositionLocation()->getId();
                $arrDatasLocations = array_count_values($arrDatasLocationsToCount);
            }
        }
        foreach ($arrDatasLocations as $key => $location) {
            $arrLabelsLocations[] = $this->expositionLocationRepository->find($key)->getNameFR().' - '. $location;
        }

        //Etages
        $arrDatasFloors = [];
        $arrLabelsFloors = [];
        foreach ($this->floorRepository->findAll() as $floor) {
            $arrDatasFloors[] = count($floor->getObjects());
            $arrLabelsFloors[] = $floor->getName().' - '.count($floor->getObjects());
        }

        //Etats
        $arrDatasState = [];
        $arrLabelsState = [];
        foreach ($this->stateRepository->findAll() as $state) {
            $arrDatasState[] = count($state->getObjects());
            $arrLabelsState[] = $state->getName();
        }

        //Population
        $arrDatasPopulations = [];
        $arrLabelsPopulations = [];
        foreach ($this->populationRepository->findAll() as $population) {
            $arrDatasPopulations[] = count($population->getObjects());
            $arrLabelsPopulations[] = $population->getName().' - '.count($population->getObjects());
        }

        //Origine
        $arrDatasOrigins = [];
        $arrLabelsOrigins = [];
        foreach ($this->originRepository->findAll() as $origin) {
            $arrDatasOrigins[] = count($origin->getObjects());
            $arrLabelsOrigins[] = $origin->getName().' - '.count($origin->getObjects());
        }

        return $this->render('site/statistics/statistics.html.twig', [
            'OriginChart' => $this->chartOriginByState(),

//            'arrDatasGods' => $arrDatasGods,
//            'arrLabelsGods' => $arrLabelsGods,

            'arrDatasOrigins' => $arrDatasOrigins,
            'arrLabelsOrigins' => $arrLabelsOrigins,

            'arrDatasPopulations' => $arrDatasPopulations,
            'arrLabelsPopulations' => $arrLabelsPopulations,

            'arrDatasMaterials' => $arrDatasMaterials,
            'arrLabelsMaterials' => $arrLabelsMaterials,

            'arrDatasLocations' => $arrDatasLocations,
            'arrLabelsLocations' => $arrLabelsLocations,

            'arrDatasFloors' => $arrDatasFloors,
            'arrLabelsFloors' => $arrLabelsFloors,
        ]);
    }

//    #[Route('/statistics.svg', name: 'statistics_svg')]
//    public function statisticsSVG(ObjectsRepository $objectsRepository): Response
//    {
//        $objects = $objectsRepository->findAllNoDeleted();
//
//
//        return $this->render('site/statistics/statistics.html.twig', [
//            [ 'data' => $objects->get() ],
//            new Response('', 200, ['Content-Type' => 'image/svg+xml'])
//        ]);
//    }


    public function chartOriginByState(): Chart
    {
        //Divinitès
        $results = [];
        // initialisation des compteurs à 0 pour chaque état
        foreach ($this->stateRepository->findAll() as $state) {
            $results[$state->getId()] = [];
        }
        foreach ($this->godsRepository->findAll() as $god) {
            if ($god->getName() !== '???' && count($god->getObjects()) > 0) {
                $arrLabelsGods[] = $god->getName().' - '.count($god->getObjects());
                $counts = [];//compteurs pour chaque état
                // initialisation des compteurs à 0 pour chaque état
                foreach ($this->stateRepository->findAll() as $state) {
                    $results[$state->getId()][$god->getName()] = 0;
                }
                // parcours de chaque objet du dieu
                foreach ($god->getObjects() as $obj) {
                    // ajoute 1 au compteur correspondant à l'état de l'objet
                    $results[$obj->getState()->getId()][$god->getName()]++;
                }
            }
        }
        $newResults = [];
        foreach ($results as $res) {
            $newR = [];
            foreach ($res as $r) {
                $newR[] = $r;
            }
            $newResults[] = $newR;
        }
        $originChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $originChart->setData([
            'labels' => $arrLabelsGods,
            'datasets' => [
                [
                    'label' => $this->stateRepository->findAll()[0]->getName(),
                    'data' => $newResults[0],
                    'backgroundColor' => '#0000FF',
                    'borderColor' => '#7F7FFF',
                ],
                [
                    'label' => $this->stateRepository->findAll()[1]->getName(),
                    'data' => $newResults[1],
                    'backgroundColor' => '#7FFF7F',
                    'borderColor' => '#00A500',
                ],
                [
                    'label' => $this->stateRepository->findAll()[2]->getName(),
                    'data' => $newResults[2],
                    'backgroundColor' => '#7FFF00',
                    'borderColor' => '#C5FFA8',
                ],
                [
                    'label' => $this->stateRepository->findAll()[3]->getName(),
                    'data' => $newResults[3],
                    'backgroundColor' => '#FFFF00',
                    'borderColor' => '#FFFFC5',
                ],
                [
                    'label' => $this->stateRepository->findAll()[4]->getName(),
                    'data' => $newResults[4],
                    'backgroundColor' => '#FFA500',
                    'borderColor' => '#FFD1A8',
                ],
                [
                    'label' => $this->stateRepository->findAll()[5]->getName(),
                    'data' => $newResults[5],
                    'backgroundColor' => ' #FF7F7F',
                    'borderColor' => '#FF0000',
                ]
            ],
        ]);
        $originChart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 20,
                ],
            ],
        ]);

        return $originChart;
    }

}
