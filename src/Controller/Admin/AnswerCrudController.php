<?php

namespace App\Controller\Admin;

use App\DBAL\Types\SmileyType;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\System;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use App\Service\AnswerService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class AnswerCrudController extends AbstractCrudController
{

    private $entityManager;
    private AnswerRepository $answerRepository;
    private ReportRepository $reportRepository;
    private SystemRepository $systemRepository;
    private QuestionRepository $questionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AnswerRepository $answerRepository,
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        QuestionRepository $questionRepository,
        RequestStack $requestStack,
    ) {
        $this->entityManager = $entityManager;
        $this->answerRepository = $answerRepository;
        $this->reportRepository = $reportRepository;
        $this->systemRepository = $systemRepository;
        $this->questionRepository = $questionRepository;
        $this->requestStack = $requestStack;
    }

    public static function getEntityFqcn(): string
    {
        return Answer::class;
    }

    public function new(AdminContext $context)
    {
        $systemId = $context->getRequest()->get('system');
        $findsystem = $this->entityManager->getRepository(System::class)->find($systemId);

        if (!$findsystem) {
            throw $this->createNotFoundException('No system found for id '.$systemId);
        }

        $questionId = $context->getRequest()->get('question');
        $findquestion = $this->entityManager->getRepository(Question::class)->find($questionId);

        if (!$findquestion) {
            throw $this->createNotFoundException('No system found for id '.$questionId);
        }

        $answer = new Answer();
        $answer->setSystem($findsystem);
        $answer->setQuestion($findquestion);
        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return parent::new($context); // TODO: Change the autogenerated stub
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::EDIT)
            ->add(Crud::PAGE_NEW, Action::NEW)
        ;
    }

    /**
     * @throws \Exception
     */
    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_NEW !== $pageName && Crud::PAGE_EDIT !== $pageName) {
            throw new \Exception('This action does not exist.');
        }

        $questionId = $this->getContext()->getRequest()->get('question');
        $question = $this->questionRepository->find($questionId);
        yield AssociationField::new('question')->setFormTypeOption('data', $question)->setFormTypeOption('disabled', true);
        yield ChoiceField::new('smiley')->setChoices([
            'Green' => SmileyType::GREEN,
            'Red' => SmileyType::RED,
            'Blue' => SmileyType::BLUE,
            'Yellow' => SmileyType::YELLOW,
        ]);
        yield TextField::new('note');
    }
}
