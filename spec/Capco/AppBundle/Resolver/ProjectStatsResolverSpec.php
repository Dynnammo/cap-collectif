<?php

namespace spec\Capco\AppBundle\Resolver;

use Capco\AppBundle\Entity\Project;
use Capco\AppBundle\Entity\Steps\CollectStep;
use Capco\AppBundle\Entity\Steps\SelectionStep;
use Capco\AppBundle\Repository\CollectStepRepository;
use Capco\AppBundle\Repository\ProposalDistrictRepository;
use Capco\AppBundle\Repository\ProposalRepository;
use Capco\AppBundle\Repository\ProposalCategoryRepository;
use Capco\AppBundle\Repository\ProposalSelectionVoteRepository;
use Capco\AppBundle\Repository\SelectionStepRepository;
use Capco\AppBundle\Repository\ThemeRepository;
use Capco\UserBundle\Repository\UserTypeRepository;
use PhpSpec\ObjectBehavior;

class ProjectStatsResolverSpec extends ObjectBehavior
{
    public function let(
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalSelectionVoteRepo
    ) {
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalSelectionVoteRepo
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Capco\AppBundle\Resolver\ProjectStatsResolver');
    }

    public function it_can_get_themes_with_proposals_count_for_step(
        CollectStep $cs,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $cs->getProposalsCount()->willReturn(100);
        $themeRepo->getThemesWithProposalsCountForStep($cs, null)->willReturn([
            [
                'name' => 'Thème 1',
                'value' => 40,
            ],
            [
                'name' => 'Thème 2',
                'value' => 60,
            ],
        ]);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getThemesWithProposalsCountForStep($cs)->shouldReturn([
            [
                'name' => 'Thème 1',
                'value' => 40,
                'percentage' => 40.0,
            ],
            [
                'name' => 'Thème 2',
                'value' => 60,
                'percentage' => 60.0,
            ],
        ]);
    }

    public function it_can_count_themes(
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $themeRepo->countAll()->willReturn(12);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->countThemes()->shouldReturn(12);
    }

    public function it_can_get_districts_with_proposals_count_for_step(
        CollectStep $cs,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $cs->getProposalsCount()->willReturn(100);
        $districtRepo->getDistrictsWithProposalsCountForStep($cs, null)->willReturn([
            [
                'name' => 'Quartier 1',
                'value' => 40,
            ],
            [
                'name' => 'Quartier 2',
                'value' => 60,
            ],
        ]);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getDistrictsWithProposalsCountForStep($cs)->shouldReturn([
            [
                'name' => 'Quartier 1',
                'value' => 40,
                'percentage' => 40.0,
            ],
            [
                'name' => 'Quartier 2',
                'value' => 60,
                'percentage' => 60.0,
            ],
        ]);
    }

    public function it_can_count_districts(
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $districtRepo->countAll()->willReturn(12);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->countDistricts()->shouldReturn(12);
    }

    public function it_can_get_user_type_with_proposals_count_for_step(
        CollectStep $cs,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $cs->getProposalsCount()->willReturn(100);
        $userTypeRepo->getUserTypesWithProposalsCountForStep($cs, null)->willReturn([
            [
                'name' => 'User type 1',
                'value' => 40,
            ],
            [
                'name' => 'User type 2',
                'value' => 60,
            ],
        ]);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getUserTypesWithProposalsCountForStep($cs)->shouldReturn([
            [
                'name' => 'User type 1',
                'value' => 40,
                'percentage' => 40.0,
            ],
            [
                'name' => 'User type 2',
                'value' => 60,
                'percentage' => 60.0,
            ],
        ]);
    }

    public function it_can_count_userTypes(
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $userTypeRepo->countAll()->willReturn(12);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->countUserTypes()->shouldReturn(12);
    }

    public function it_can_get_proposals_with_costs_for_step(
        CollectStep $cs,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $proposalRepo->getTotalCostForStep($cs)->willReturn(100);
        $proposalRepo->getProposalsWithCostsForStep($cs, null)->willReturn([
            [
                'name' => 'Proposal 1',
                'value' => 40,
            ],
            [
                'name' => 'Proposal 2',
                'value' => 60,
            ],
        ]);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getProposalsWithCostsForStep($cs)->shouldReturn([
            [
                'name' => 'Proposal 1',
                'value' => 40,
                'percentage' => 40.0,
            ],
            [
                'name' => 'Proposal 2',
                'value' => 60,
                'percentage' => 60.0,
            ],
        ]);
    }

    public function it_can_get_total_cost_for_step(
        CollectStep $cs,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $proposalRepo->getTotalCostForStep($cs)->willReturn(100);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getTotalCostForStep($cs)->shouldReturn(100);
    }

    public function it_can_get_proposals_with_votes_count_for_selection_step(
        SelectionStep $ss,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $proposalVoteRepo->getVotesCountForSelectionStep($ss, null, null, null)->willReturn(100);
        $proposalRepo
            ->getProposalsWithVotesCountForSelectionStep($ss, null, null, null, null)
            ->willReturn([
                [
                    'name' => 'Proposal 1',
                    'value' => 40,
                ],
                [
                    'name' => 'Proposal 2',
                    'value' => 60,
                ],
            ]);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getProposalsWithVotesCountForSelectionStep($ss)->shouldReturn([
            [
                'name' => 'Proposal 1',
                'value' => 40,
                'percentage' => 40.0,
            ],
            [
                'name' => 'Proposal 2',
                'value' => 60,
                'percentage' => 60.0,
            ],
        ]);
    }

    public function it_can_get_votes_count_for_selection_step(
        SelectionStep $ss,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $proposalVoteRepo->getVotesCountForSelectionStep($ss, null, null, null)->willReturn(100);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getVotesCountForSelectionStep($ss)->shouldReturn(100);
    }

    public function it_can_get_proposals_count_for_selection_step(
        SelectionStep $ss,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $proposalRepo->countForSelectionStep($ss, null, null, null)->willReturn(100);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->getProposalsCountForSelectionStep($ss)->shouldReturn(100);
    }

    public function it_can_add_percentage_to_array()
    {
        $this->addPercentages(
            [
                [
                    'name' => 'Nom 1',
                    'value' => 40,
                ],
                [
                    'name' => 'Nom 2',
                    'value' => 30,
                ],
            ],
            100
        )->shouldReturn([
            [
                'name' => 'Nom 1',
                'value' => 40,
                'percentage' => 40.0,
            ],
            [
                'name' => 'Nom 2',
                'value' => 30,
                'percentage' => 30.0,
            ],
        ]);

        $this->addPercentages(
            [
                [
                    'name' => 'Nom 1',
                    'value' => 58,
                ],
                [
                    'name' => 'Nom 2',
                    'value' => 376,
                ],
            ],
            960
        )->shouldReturn([
            [
                'name' => 'Nom 1',
                'value' => 58,
                'percentage' => 6.04,
            ],
            [
                'name' => 'Nom 2',
                'value' => 376,
                'percentage' => 39.17,
            ],
        ]);
    }

    public function it_can_tell_if_project_has_steps_with_stats(
        Project $projectWithSteps,
        Project $projectWithNoSteps,
        SelectionStep $ss,
        CollectStep $cs,
        SelectionStepRepository $selectionStepRepo,
        CollectStepRepository $collectStepRepo,
        ThemeRepository $themeRepo,
        ProposalDistrictRepository $districtRepo,
        ProposalCategoryRepository $categoryRepo,
        UserTypeRepository $userTypeRepo,
        ProposalRepository $proposalRepo,
        ProposalSelectionVoteRepository $proposalVoteRepo
    ) {
        $selectionStepRepo->getVotableStepsForProject($projectWithSteps)->willReturn([$ss]);
        $selectionStepRepo->getVotableStepsForProject($projectWithNoSteps)->willReturn([]);
        $collectStepRepo->getCollectStepsForProject($projectWithSteps)->willReturn([$cs]);
        $collectStepRepo->getCollectStepsForProject($projectWithNoSteps)->willReturn([]);
        $this->beConstructedWith(
            $selectionStepRepo,
            $collectStepRepo,
            $themeRepo,
            $districtRepo,
            $categoryRepo,
            $userTypeRepo,
            $proposalRepo,
            $proposalVoteRepo
        );
        $this->hasStepsWithStats($projectWithSteps)->shouldReturn(true);
        $this->hasStepsWithStats($projectWithNoSteps)->shouldReturn(false);
    }
}
